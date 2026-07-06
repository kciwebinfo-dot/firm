<?php
require_once __DIR__ . '/auth/auth_check.php'; require_once __DIR__ . '/config/billing_helpers.php';
$pageTitle='Bills'; $active='bills';
$rows=$pdo->query("SELECT b.*, c.name client_name, c.mobile FROM bills b JOIN clients c ON c.id=b.client_id ORDER BY b.id DESC")->fetchAll();
include __DIR__.'/includes/head.php'; include __DIR__.'/includes/sidebar.php'; include __DIR__.'/includes/topbar.php';
?>
<div class="page-title"><div><h1>Bills</h1><p class="muted">Invoices and dues.</p></div><a class="btn btn-primary" href="generate-bill.php">Generate Bill</a></div>
<div class="panel-card"><table class="table table-striped data-table nowrap w-100"><thead><tr><th>Bill No</th><th>Client</th><th>Mobile</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): $t=bill_token($r['bill_no']); ?><tr><td><?= e($r['bill_no']) ?></td><td><?= e($r['client_name']) ?></td><td><?= e($r['mobile']) ?></td><td><?= e($r['bill_date']) ?></td><td><?= money($r['grand_total']) ?></td><td><?= money($r['paid_amount']) ?></td><td><?= money($r['due_amount']) ?></td><td><?= payment_badge($r['payment_status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="bill-pdf.php?token=<?= e($t) ?>">PDF</a> <a class="btn btn-sm btn-outline-success" href="receive-payment.php?bill=<?= e($t) ?>">Pay</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__.'/includes/footer.php'; include __DIR__.'/includes/scripts.php'; ?>
