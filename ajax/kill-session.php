<?php
require_once __DIR__ . '/../config/helpers.php';
verify_csrf();

$userId = (int)($_SESSION['pending_login_user'] ?? 0);
if ($userId <= 0) {
    json_response(false, 'No old session found.');
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 1 LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user) {
    json_response(false, 'Account not found.');
}
session_regenerate_id(true);
$token = bin2hex(random_bytes(32));
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['role'] = (string)$user['role'];
$_SESSION['session_token'] = $token;
$_SESSION['session_timeout_minutes'] = 15;
unset($_SESSION['pending_login_user']);
$pdo->prepare("UPDATE users SET session_token = ?, session_expires = ?, last_login = NOW(), last_ip = ?, last_user_agent = ? WHERE id = ?")
    ->execute([$token, date('Y-m-d H:i:s', time() + 900), $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $user['id']]);
json_response(true, 'Old session killed. Login continued.', ['redirect' => 'dashboard.php']);
