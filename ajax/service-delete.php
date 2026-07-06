<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) json_response(false, 'Invalid service.');
$stmt = $pdo->prepare("UPDATE services SET status = 0, updated_at = NOW() WHERE id = ?");
$stmt->execute([$id]);
json_response(true, 'Service marked inactive.');
