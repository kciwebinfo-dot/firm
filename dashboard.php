<?php
require_once __DIR__ . '/auth/auth_check.php';
$pageTitle = 'Dashboard';
$active = 'dashboard';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/sidebar.php';
include __DIR__ . '/includes/topbar.php';
?>
<div class="page-title">
    <div><h1>Dashboard</h1><p class="muted">Today at <?= e(firm('short_name', 'Firm')) ?></p></div>
    <button class="btn btn-primary" id="sweetDemo"><i class="bi bi-check2-circle me-2"></i>Demo Success</button>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([['Total Clients','245','bi-people'],['Pending Work','32','bi-hourglass-split'],['Completed Work','189','bi-check-circle'],['Wallet Balance','Rs. 42,800','bi-wallet2']] as $i => $card): ?>
    <div class="col-12 col-sm-6 col-xl-3"><div class="stat-card <?= $i === 0 ? 'accent' : '' ?>"><div class="icon"><i class="bi <?= e($card[2]) ?>"></i></div><h3><?= e($card[1]) ?></h3><p><?= e($card[0]) ?></p></div></div>
    <?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="panel-card"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Work Overview</h5><span class="badge badge-theme">Sample</span></div><div style="height:310px"><canvas id="workChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="panel-card h-100"><h5>Recent Activity</h5><div class="list-group list-group-flush mt-3"><div class="list-group-item bg-transparent px-0">ITR file reviewed <span class="float-end muted">10m</span></div><div class="list-group-item bg-transparent px-0">Client KYC pending <span class="float-end muted">1h</span></div><div class="list-group-item bg-transparent px-0">Wallet updated <span class="float-end muted">3h</span></div></div><div class="mt-4"><div class="d-flex justify-content-between"><span>Monthly target</span><strong>72%</strong></div><div class="progress mt-2"><div class="progress-bar" style="width:72%"></div></div></div></div></div>
</div>
<div class="panel-card">
    <h5 class="mb-3">DataTables Export Demo</h5>
    <table class="table table-striped data-table nowrap w-100">
        <thead><tr><th>Client</th><th>Service</th><th>Status</th><th>Due Date</th><th>Amount</th></tr></thead>
        <tbody>
            <tr><td>Acme Traders</td><td>GST Return</td><td><span class="badge text-bg-warning">Pending</span></td><td>15 Jul 2026</td><td>Rs. 2,500</td></tr>
            <tr><td>Sharma & Co</td><td>ITR Filing</td><td><span class="badge text-bg-success">Done</span></td><td>20 Jul 2026</td><td>Rs. 4,000</td></tr>
            <tr><td>Prime Retail</td><td>TDS</td><td><span class="badge text-bg-info">Review</span></td><td>25 Jul 2026</td><td>Rs. 3,200</td></tr>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; include __DIR__ . '/includes/scripts.php'; ?>
