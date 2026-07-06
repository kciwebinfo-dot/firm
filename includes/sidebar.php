<?php
$active = $active ?? 'dashboard';
$menus = [
    ['dashboard', 'dashboard.php', 'bi-speedometer2', 'Dashboard'],
    ['clients', '#', 'bi-people', 'Clients'],
    ['work', '#', 'bi-kanban', 'Work Status'],
    ['reports', '#', 'bi-file-earmark-bar-graph', 'Reports'],
    ['wallet', '#', 'bi-wallet2', 'Wallet'],
    ['profile', 'profile.php', 'bi-person-circle', 'Profile'],
    ['settings', '#', 'bi-gear', 'Settings'],
    ['logout', 'logout.php', 'bi-box-arrow-right', 'Logout'],
];
?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="<?= e(asset(firm('logo') ?: 'assets/uploads/firm/default-logo.svg')) ?>" alt="Logo">
        <div>
            <strong><?= e(firm('short_name', 'TAX')) ?></strong>
            <span><?= e(firm('tagline', 'Staff Portal')) ?></span>
        </div>
        <button class="icon-btn sidebar-collapse" type="button" id="sidebarCollapse" title="Collapse"><i class="bi bi-layout-sidebar-inset"></i></button>
    </div>
    <nav class="menu">
        <?php foreach ($menus as [$key, $url, $icon, $label]): ?>
            <?php if (can_access($key)): ?>
                <a href="<?= e($url) ?>" class="<?= $active === $key ? 'active' : '' ?>">
                    <i class="bi <?= e($icon) ?>"></i>
                    <span><?= e($label) ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>
<div class="mobile-overlay" id="mobileOverlay"></div>
<main class="main-panel">
