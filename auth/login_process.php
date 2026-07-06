<?php
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php');
}

verify_csrf();
$login = trim($_POST['login'] ?? '');
$cleanLogin = clean_mobile($login);
$password = (string)($_POST['password'] ?? '');
$kill = ($_POST['kill_old_session'] ?? '') === '1';

if ($login === '' || $password === '') {
    json_response(false, 'Enter your login ID and password.');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR mobile = ? OR mobile = ? LIMIT 1");
$stmt->execute([$login, $login, $login, $cleanLogin]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, (string)$user['password'])) {
    json_response(false, 'Invalid login details.');
}
if ((int)$user['status'] !== 1) {
    json_response(false, 'Your account is inactive.');
}
if (!$kill && !empty($user['session_token']) && !empty($user['session_expires']) && strtotime($user['session_expires']) > time()) {
    $_SESSION['pending_login_user'] = (int)$user['id'];
    json_response(false, 'You are already logged in on another device.', ['dual' => true]);
}

session_regenerate_id(true);
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 900);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['role'] = (string)$user['role'];
$_SESSION['session_token'] = $token;
$_SESSION['session_timeout_minutes'] = 15;

$stmt = $pdo->prepare("UPDATE users SET session_token = ?, session_expires = ?, last_login = NOW(), last_ip = ?, last_user_agent = ? WHERE id = ?");
$stmt->execute([$token, $expires, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $user['id']]);
json_response(true, 'Login successful.', ['redirect' => 'dashboard.php']);
