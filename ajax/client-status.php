<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
$id = (int)($_POST['id'] ?? 0);
$status = (int)($_POST['status'] ?? 0);
if ($id <= 0) json_response(false, 'Invalid client.');
$stmt = $pdo->prepare("UPDATE clients SET status=?, updated_at=NOW() WHERE id=?");
$stmt->execute([$status ? 1 : 0, $id]);
json_response(true, 'Client status updated.');
