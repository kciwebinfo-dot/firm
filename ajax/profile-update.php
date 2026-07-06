<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();

try {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = clean_mobile($_POST['mobile'] ?? '');
    if ($name === '' || $email === '' || $mobile === '') {
        json_response(false, 'Name, email, and mobile are required.');
    }
    $photo = upload_image('photo', 'assets/uploads/users');
    $sign = upload_image('sign', 'assets/uploads/users');
    $sets = ['name = ?', 'email = ?', 'mobile = ?', 'updated_at = NOW()'];
    $params = [$name, $email, $mobile];
    if ($photo) {
        $sets[] = 'photo = ?';
        $params[] = $photo;
    }
    if ($sign) {
        $sets[] = 'sign = ?';
        $params[] = $sign;
    }
    $params[] = $_SESSION['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?");
    $stmt->execute($params);
    json_response(true, 'Profile updated.', ['photo' => $photo, 'sign' => $sign]);
} catch (Throwable $e) {
    json_response(false, $e->getMessage());
}
