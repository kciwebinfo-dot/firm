<?php
require_once __DIR__ . '/auth/auth_check.php'; require_once __DIR__ . '/config/billing_helpers.php';
$pageTitle='Payments'; $active='payments';
$rows=$pdo->query("SELECT p.*, b.bill_no, c.name client_name, u.name received_by_name FROM payments p JOIN bills b ON b.id=p.bill_id JOIN clients c ON c.id=p.client_id LEFT JOIN users u ON u.id=p.received_by ORDER BY p.id DESC")->fetchAll();
include __DIR__.'/includes/head.php'; include __DIR__.'/includes/sidebar.php'; include __DIR__.'/includes/topbar.php';
?>
<div class="page-title"><div><h1>Payments</h1><p class="muted">Receipts and collections.</p></div><a class="btn btn-primary" href="receive-payment.php">Receive Payment</a></div>
<div class="panel-card"><table class="table table-striped data-table nowrap w-100"><thead><tr><th>Receipt</th><th>Bill</th><th>Client</th><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Received By</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['receipt_no']) ?></td><td><?= e($r['bill_no']) ?></td><td><?= e($r['client_name']) ?></td><td><?= e($r['payment_date']) ?></td><td><?= money($r['amount']) ?></td><td><?= e($r['payment_mode']) ?></td><td><?= e($r['reference_no']) ?></td><td><?= e($r['received_by_name']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="receipt-pdf.php?token=<?= e(receipt_token($r['receipt_no'])) ?>">PDF</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/includes/footer.php'; include __DIR__.'/includes/scripts.php'; ?>
