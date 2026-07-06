<?php
require_once __DIR__ . '/../config/whatsapp.php';

header('Content-Type: application/json');
set_exception_handler(function ($e) {
    json_response(false, 'OTP request failed: ' . $e->getMessage());
});
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode(['success' => false, 'message' => 'OTP request failed on server. Please check WhatsApp/API settings.']);
    }
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../forgot-password.php');
}
verify_csrf();
$action = $_POST['action'] ?? '';
$mobile = clean_mobile($_POST['mobile'] ?? '');
$rawMobile = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? ''));

$stmt = $pdo->prepare("SELECT * FROM users WHERE (mobile = ? OR mobile = ?) AND status = 1 LIMIT 1");
$stmt->execute([$mobile, $rawMobile]);
$user = $stmt->fetch();
if (!$user) {
    json_response(false, 'No active user found with this mobile number.');
}

if ($action === 'send') {
    $otp = (string)random_int(100000, 999999);
    $pdo->prepare("UPDATE users SET otp_code = ?, otp_type = 'forgot', otp_expires = ?, otp_attempts = 0 WHERE id = ?")
        ->execute([$otp, date('Y-m-d H:i:s', time() + 600), $user['id']]);
    $wa = sendWhatsAppTemplate($mobile, 'forgot_otp', [$otp, firm('mobile')]);
    if (!empty($wa['error'])) {
        $message = is_array($wa['error']) ? ($wa['error']['message'] ?? 'WhatsApp message could not be sent.') : $wa['error'];
        json_response(false, 'WhatsApp OTP failed: ' . $message);
    }
    json_response(true, 'Password recovery OTP sent.');
}

if ($action === 'verify') {
    $otp = trim($_POST['otp'] ?? '');
    if ($user['otp_type'] !== 'forgot' || strtotime((string)$user['otp_expires']) < time()) {
        json_response(false, 'OTP expired. Please request a new OTP.');
    }
    if (!hash_equals((string)$user['otp_code'], $otp)) {
        $pdo->prepare("UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?")->execute([$user['id']]);
        json_response(false, 'Invalid OTP.');
    }
    $_SESSION['forgot_verified_mobile'] = $mobile;
    json_response(true, 'OTP verified.');
}

if ($action === 'reset') {
    if (($_SESSION['forgot_verified_mobile'] ?? '') !== $mobile) {
        json_response(false, 'Please verify OTP first.');
    }
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (strlen($password) < 8) {
        json_response(false, 'Password must be at least 8 characters.');
    }
    if ($password !== $confirm) {
        json_response(false, 'Passwords do not match.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_type = NULL, otp_expires = NULL, otp_attempts = 0 WHERE id = ?")
        ->execute([$hash, $user['id']]);
    unset($_SESSION['forgot_verified_mobile']);
    json_response(true, 'Password changed successfully.', ['redirect' => 'login.php']);
}

json_response(false, 'Invalid request.');
