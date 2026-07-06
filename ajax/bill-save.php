<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
try {
    $pdo->beginTransaction();
    $clientId = (int)($_POST['client_id'] ?? 0);
    if ($clientId <= 0) throw new RuntimeException('Select a client.');
    $names = $_POST['service_name'] ?? [];
    $descs = $_POST['description'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $rates = $_POST['rate'] ?? [];
    $serviceIds = $_POST['service_id'] ?? [];
    $items = [];
    $subtotal = 0;
    foreach ($names as $i => $name) {
        $name = trim($name);
        $qty = max(0, (float)($qtys[$i] ?? 0));
        $rate = max(0, (float)($rates[$i] ?? 0));
        if ($name === '' || $qty <= 0) continue;
        $amount = round($qty * $rate, 2);
        $subtotal += $amount;
        $items[] = [(int)($serviceIds[$i] ?? 0), $name, trim($descs[$i] ?? ''), $qty, $rate, $amount];
    }
    if (!$items) throw new RuntimeException('Add at least one bill item.');
    $discount = max(0, (float)($_POST['discount'] ?? 0));
    $tax = max(0, (float)($_POST['tax_amount'] ?? 0));
    $taxable = max(0, $subtotal - $discount);
    $grand = $taxable + $tax;
    $billNo = next_document_no('bills', 'bill_no', 'BILL');
    $stmt = $pdo->prepare("INSERT INTO bills (bill_no, client_id, bill_date, subtotal, discount, taxable_amount, tax_amount, grand_total, paid_amount, due_amount, payment_status, notes, created_by) VALUES (?,?,?,?,?,?,?,?,0,?,'unpaid',?,?)");
    $stmt->execute([$billNo, $clientId, $_POST['bill_date'] ?: date('Y-m-d'), $subtotal, $discount, $taxable, $tax, $grand, $grand, trim($_POST['notes'] ?? ''), $_SESSION['user_id']]);
    $billId = (int)$pdo->lastInsertId();
    $ins = $pdo->prepare("INSERT INTO bill_items (bill_id, service_id, service_name, description, qty, rate, amount) VALUES (?,?,?,?,?,?,?)");
    foreach ($items as $item) $ins->execute(array_merge([$billId], $item));
    $pdo->commit();
    json_response(true, 'Bill generated successfully.', ['bill_no' => $billNo, 'token' => bill_token($billNo)]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(false, $e->getMessage());
}
