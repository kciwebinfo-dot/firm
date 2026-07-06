<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();

$user = current_user();
$old = (string)($_POST['old_password'] ?? '');
$new = (string)($_POST['new_password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');
if (!password_verify($old, (string)$user['password'])) {
    json_response(false, 'Old password is incorrect.');
}
if (strlen($new) < 8) {
    json_response(false, 'New password must be at least 8 characters.');
}
if ($new !== $confirm) {
    json_response(false, 'Passwords do not match.');
}
$pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")
    ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
json_response(true, 'Password changed successfully.');
