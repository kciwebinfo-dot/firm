<?php
require_once __DIR__ . '/../config/whatsapp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../otp-login.php');
}
verify_csrf();
$action = $_POST['action'] ?? '';

if ($action === 'send') {
    $mobile = clean_mobile($_POST['mobile'] ?? '');
    $rawMobile = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? ''));
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (mobile = ? OR mobile = ?) AND status = 1 LIMIT 1");
    $stmt->execute([$mobile, $rawMobile]);
    $user = $stmt->fetch();
    if (!$user) {
        json_response(false, 'No active user found with this mobile number.');
    }
    $otp = (string)random_int(100000, 999999);
    $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_type = 'login', otp_expires = ?, otp_attempts = 0 WHERE id = ?");
    $stmt->execute([$otp, date('Y-m-d H:i:s', time() + 600), $user['id']]);
    $wa = sendWhatsAppTemplate($mobile, 'otp_login', [$otp, 'login to ' . firm('firm_name', 'Firm'), firm('mobile'), firm('mobile')]);
    if (!empty($wa['error'])) {
        $message = is_array($wa['error']) ? ($wa['error']['message'] ?? 'WhatsApp message could not be sent.') : $wa['error'];
        json_response(false, 'WhatsApp OTP failed: ' . $message);
    }
    json_response(true, 'OTP sent on WhatsApp.');
}

if ($action === 'verify') {
    $mobile = clean_mobile($_POST['mobile'] ?? '');
    $rawMobile = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? ''));
    $otp = trim($_POST['otp'] ?? '');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (mobile = ? OR mobile = ?) AND status = 1 LIMIT 1");
    $stmt->execute([$mobile, $rawMobile]);
    $user = $stmt->fetch();
    if (!$user || $user['otp_type'] !== 'login' || strtotime((string)$user['otp_expires']) < time()) {
        json_response(false, 'OTP expired. Please request a new OTP.');
    }
    if ((int)$user['otp_attempts'] >= 5) {
        json_response(false, 'Too many OTP attempts.');
    }
    if (!hash_equals((string)$user['otp_code'], $otp)) {
        $pdo->prepare("UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?")->execute([$user['id']]);
        json_response(false, 'Invalid OTP.');
    }
    session_regenerate_id(true);
    $token = bin2hex(random_bytes(32));
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['role'] = (string)$user['role'];
    $_SESSION['session_token'] = $token;
    $_SESSION['session_timeout_minutes'] = 15;
    $stmt = $pdo->prepare("UPDATE users SET session_token = ?, session_expires = ?, last_login = NOW(), last_ip = ?, last_user_agent = ?, otp_code = NULL, otp_type = NULL, otp_expires = NULL, otp_attempts = 0 WHERE id = ?");
    $stmt->execute([$token, date('Y-m-d H:i:s', time() + 900), $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $user['id']]);
    json_response(true, 'OTP verified.', ['redirect' => 'dashboard.php']);
}

json_response(false, 'Invalid request.');
