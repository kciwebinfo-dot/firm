<?php
require_once __DIR__ . '/../config/helpers.php';

if (!is_logged_in()) {
    $_SESSION['flash_error'] = 'Please login to continue.';
    redirect('login.php');
}

$user = current_user();
if (!$user || (int)$user['status'] !== 1) {
    session_destroy();
    redirect('login.php?message=inactive');
}

$dbToken = (string)($user['session_token'] ?? '');
if (!$dbToken || !hash_equals($dbToken, (string)$_SESSION['session_token'])) {
    session_destroy();
    redirect('login.php?message=dual');
}

if (!empty($user['session_expires']) && strtotime($user['session_expires']) < time()) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET session_token = NULL, last_logout = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    session_destroy();
    redirect('login.php?message=timeout');
}

$timeout = (int)($_SESSION['session_timeout_minutes'] ?? 15);
if (!in_array($timeout, [15, 30, 45, 60, 120], true)) {
    $timeout = 15;
}
$expires = date('Y-m-d H:i:s', time() + ($timeout * 60));
$stmt = $pdo->prepare("UPDATE users SET session_expires = ? WHERE id = ?");
$stmt->execute([$expires, $user['id']]);
