<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
try {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['service_name'] ?? '');
    $code = trim($_POST['service_code'] ?? '');
    $category = trim($_POST['service_category'] ?? '');
    $fee = (float)($_POST['default_fee'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);
    if ($name === '' || $fee < 0) json_response(false, 'Service name and valid fee are required.');
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE services SET service_name=?, service_code=?, service_category=?, default_fee=?, description=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$name, $code, $category, $fee, $description, $status, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO services (service_name, service_code, service_category, default_fee, description, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $code, $category, $fee, $description, $status]);
    }
    json_response(true, 'Service saved successfully.');
} catch (Throwable $e) {
    json_response(false, $e->getMessage());
}
