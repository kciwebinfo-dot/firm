<?php
require_once __DIR__ . '/auth/auth_check.php';
require_once __DIR__ . '/config/billing_helpers.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $_SESSION['client_view_id'] = (int)decode_token($_POST['client_token'] ?? '');
}
$clientId = (int)($_SESSION['client_view_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id=?");
$stmt->execute([$clientId]);
$client = $stmt->fetch();
if (!$client) redirect('clients.php');
$svc = $pdo->prepare("SELECT s.service_name, cs.assigned_fee FROM client_services cs JOIN services s ON s.id=cs.service_id WHERE cs.client_id=?");
$svc->execute([$clientId]);
$bills = $pdo->prepare("SELECT * FROM bills WHERE client_id=? ORDER BY id DESC");
$bills->execute([$clientId]);
$payments = $pdo->prepare("SELECT p.*, b.bill_no FROM payments p JOIN bills b ON b.id=p.bill_id WHERE p.client_id=? ORDER BY p.id DESC");
$payments->execute([$clientId]);
$due = $pdo->prepare("SELECT COALESCE(SUM(due_amount),0) FROM bills WHERE client_id=?");
$due->execute([$clientId]);
$pageTitle='Client View'; $active='clients';
include __DIR__.'/includes/head.php'; include __DIR__.'/includes/sidebar.php'; include __DIR__.'/includes/topbar.php';
?>
<div class="page-title"><div><h1><?= e($client['name']) ?></h1><p class="muted"><?= e($client['trade_name']) ?> | <?= e($client['mobile']) ?></p></div><div><a class="btn btn-primary" href="generate-bill.php">Generate Bill</a> <a class="btn btn-outline-primary" href="receive-payment.php">Receive Payment</a></div></div>
<div class="row g-3"><div class="col-lg-4"><div class="panel-card"><h5>Client Details</h5><p><?= e($client['address']) ?></p><p><?= e($client['city']) ?> <?= e($client['state']) ?> <?= e($client['pincode']) ?></p><p>PAN: <?= e($client['pan']) ?><br>GSTIN: <?= e($client['gstin']) ?></p><h4 class="text-danger">Due: <?= money($due->fetchColumn()) ?></h4></div></div><div class="col-lg-8"><div class="panel-card"><h5>Assigned Services</h5><table class="table"><tbody><?php foreach($svc as $s): ?><tr><td><?= e($s['service_name']) ?></td><td><?= money($s['assigned_fee']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<div class="row g-3 mt-1"><div class="col-lg-6"><div class="panel-card"><h5>Bills</h5><table class="table"><tbody><?php foreach($bills as $b): ?><tr><td><?= e($b['bill_no']) ?></td><td><?= money($b['grand_total']) ?></td><td><?= payment_badge($b['payment_status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div><div class="col-lg-6"><div class="panel-card"><h5>Payments</h5><table class="table"><tbody><?php foreach($payments as $p): ?><tr><td><?= e($p['receipt_no']) ?></td><td><?= e($p['bill_no']) ?></td><td><?= money($p['amount']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?php include __DIR__.'/includes/footer.php'; include __DIR__.'/includes/scripts.php'; ?>
