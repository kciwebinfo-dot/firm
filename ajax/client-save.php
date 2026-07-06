<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
try {
    $pdo->beginTransaction();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($name === '') throw new RuntimeException('Client name is required.');
    $mobile = clean_mobile($_POST['mobile'] ?? '');
    $whatsapp = clean_mobile($_POST['whatsapp_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Invalid email.');
    $fields = [$name, trim($_POST['trade_name'] ?? ''), $mobile, $whatsapp, $email, strtoupper(trim($_POST['pan'] ?? '')), strtoupper(trim($_POST['gstin'] ?? '')), trim($_POST['address'] ?? ''), trim($_POST['city'] ?? ''), trim($_POST['state'] ?? ''), trim($_POST['pincode'] ?? ''), (int)($_POST['status'] ?? 1)];
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE clients SET name=?, trade_name=?, mobile=?, whatsapp_number=?, email=?, pan=?, gstin=?, address=?, city=?, state=?, pincode=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->execute(array_merge($fields, [$id]));
    } else {
        $stmt = $pdo->prepare("INSERT INTO clients (name, trade_name, mobile, whatsapp_number, email, pan, gstin, address, city, state, pincode, status, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array_merge($fields, [$_SESSION['user_id']]));
        $id = (int)$pdo->lastInsertId();
    }
    $pdo->prepare("DELETE FROM client_services WHERE client_id=?")->execute([$id]);
    $services = $_POST['services'] ?? [];
    $fees = $_POST['assigned_fee'] ?? [];
    $ins = $pdo->prepare("INSERT INTO client_services (client_id, service_id, assigned_fee, status) VALUES (?,?,?,1)");
    foreach ($services as $serviceId) {
        $serviceId = (int)$serviceId;
        $fee = max(0, (float)($fees[$serviceId] ?? 0));
        if ($serviceId > 0) $ins->execute([$id, $serviceId, $fee]);
    }
    $pdo->commit();
    json_response(true, 'Client saved successfully.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(false, $e->getMessage());
}
