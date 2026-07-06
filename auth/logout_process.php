<?php
require_once __DIR__ . '/../config/helpers.php';

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET last_logout = NOW(), session_token = NULL, session_expires = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
redirect('../login.php?message=logout');
