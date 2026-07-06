<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();

$timeout = (int)($_POST['session_timeout_minutes'] ?? 15);
if (!in_array($timeout, [15, 30, 45, 60, 120], true)) {
    json_response(false, 'Invalid session duration.');
}
$_SESSION['session_timeout_minutes'] = $timeout;
$pdo->prepare("UPDATE users SET session_expires = ? WHERE id = ?")
    ->execute([date('Y-m-d H:i:s', time() + ($timeout * 60)), $_SESSION['user_id']]);
json_response(true, 'Session duration updated.');
