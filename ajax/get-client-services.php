<?php
require_once __DIR__ . '/../auth/auth_check.php';
$clientId = (int)($_POST['client_id'] ?? 0);
if ($clientId <= 0) json_response(false, 'Invalid client.');
$stmt = $pdo->prepare("SELECT s.id service_id, s.service_name, cs.assigned_fee FROM client_services cs JOIN services s ON s.id=cs.service_id WHERE cs.client_id=? AND cs.status=1 AND s.status=1 ORDER BY s.service_name");
$stmt->execute([$clientId]);
json_response(true, 'Services loaded.', ['services' => $stmt->fetchAll()]);
