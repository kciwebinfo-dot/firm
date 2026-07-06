<?php
require_once __DIR__ . '/helpers.php';

function fetch_active_services(): array
{
    global $pdo;
    return $pdo->query("SELECT * FROM services WHERE status = 1 ORDER BY service_name ASC")->fetchAll();
}

function fetch_clients(): array
{
    global $pdo;
    return $pdo->query("SELECT * FROM clients ORDER BY id DESC")->fetchAll();
}

function payment_badge($status): string
{
    $map = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
    $class = $map[$status] ?? 'secondary';
    return '<span class="badge text-bg-' . $class . '">' . e(ucfirst($status)) . '</span>';
}

function bill_token($billNo): string
{
    return encode_token($billNo);
}

function receipt_token($receiptNo): string
{
    return encode_token($receiptNo);
}

function find_bill_by_token($token): ?array
{
    global $pdo;
    $billNo = decode_token($token);
    if ($billNo === '') return null;
    $stmt = $pdo->prepare("SELECT b.*, c.name client_name, c.trade_name, c.mobile, c.email, c.pan client_pan, c.gstin client_gstin, c.address client_address, c.city client_city, c.state client_state, c.pincode client_pincode FROM bills b JOIN clients c ON c.id = b.client_id WHERE b.bill_no = ? LIMIT 1");
    $stmt->execute([$billNo]);
    return $stmt->fetch() ?: null;
}

function find_receipt_by_token($token): ?array
{
    global $pdo;
    $receiptNo = decode_token($token);
    if ($receiptNo === '') return null;
    $stmt = $pdo->prepare("SELECT p.*, b.bill_no, b.grand_total, b.paid_amount, b.due_amount, c.name client_name, c.trade_name, c.mobile, u.name received_by_name FROM payments p JOIN bills b ON b.id = p.bill_id JOIN clients c ON c.id = p.client_id LEFT JOIN users u ON u.id = p.received_by WHERE p.receipt_no = ? LIMIT 1");
    $stmt->execute([$receiptNo]);
    return $stmt->fetch() ?: null;
}
