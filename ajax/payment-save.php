<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();
try {
    $pdo->beginTransaction();
    $billId = (int)($_POST['bill_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM bills WHERE id=? FOR UPDATE");
    $stmt->execute([$billId]);
    $bill = $stmt->fetch();
    if (!$bill) throw new RuntimeException('Bill not found.');
    if ($amount <= 0 || $amount > (float)$bill['due_amount']) throw new RuntimeException('Payment amount cannot exceed due amount.');
    $receiptNo = next_document_no('payments', 'receipt_no', 'REC');
    $newPaid = (float)$bill['paid_amount'] + $amount;
    $newDue = max(0, (float)$bill['grand_total'] - $newPaid);
    $status = $newDue <= 0 ? 'paid' : 'partial';
    $stmt = $pdo->prepare("INSERT INTO payments (receipt_no, bill_id, client_id, payment_date, amount, payment_mode, reference_no, notes, received_by) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$receiptNo, $billId, $bill['client_id'], $_POST['payment_date'] ?: date('Y-m-d'), $amount, $_POST['payment_mode'] ?? 'Cash', trim($_POST['reference_no'] ?? ''), trim($_POST['notes'] ?? ''), $_SESSION['user_id']]);
    $pdo->prepare("UPDATE bills SET paid_amount=?, due_amount=?, payment_status=?, updated_at=NOW() WHERE id=?")->execute([$newPaid, $newDue, $status, $billId]);
    $pdo->commit();
    json_response(true, 'Payment received successfully.', ['receipt_no' => $receiptNo, 'token' => receipt_token($receiptNo)]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(false, $e->getMessage());
}
