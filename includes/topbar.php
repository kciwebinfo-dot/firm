<?php $user = current_user() ?: []; ?>
<header class="topbar">
    <div class="topbar-left">
        <button class="icon-btn mobile-menu" type="button" id="mobileMenu" title="Menu"><i class="bi bi-list"></i></button>
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="search" placeholder="Search">
        </div>
    </div>
    <div class="topbar-actions">
        <button class="icon-btn" type="button" id="themeModeToggle" title="Light/Dark"><i class="bi bi-moon-stars"></i></button>
        <div class="dropdown">
            <button class="icon-btn" data-bs-toggle="dropdown" type="button" title="Theme colors"><i class="bi bi-palette"></i></button>
            <div class="dropdown-menu dropdown-menu-end p-3 theme-menu">
                <div class="theme-swatches">
                    <?php foreach (['royal','orange','blue','green','purple','red','teal','pink','indigo','cyan','amber','emerald'] as $color): ?>
                        <button type="button" class="swatch color-<?= e($color) ?>" data-theme-color="<?= e($color) ?>" title="<?= e(ucfirst($color)) ?>"></button>
                    <?php endforeach; ?>
                </div>
                <select class="form-select form-select-sm mt-3" id="themeStyleSelect">
                    <?php foreach (['default','soft','solid','gradient'] as $style): ?>
                        <option value="<?= e($style) ?>" <?= (($user['theme_style'] ?? 'default') === $style) ? 'selected' : '' ?>><?= e(ucfirst($style)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button class="icon-btn" type="button" title="Notifications"><i class="bi bi-bell"></i></button>
        <button class="icon-btn text-success" type="button" title="WhatsApp"><i class="bi bi-whatsapp"></i></button>
        <div class="dropdown">
            <button class="user-chip" data-bs-toggle="dropdown" type="button">
                <img src="<?= e(asset($user['photo'] ?: 'assets/uploads/users/default-user.svg')) ?>" alt="User">
                <span><?= e($user['name'] ?? 'User') ?></span>
                <small><?= e($user['role'] ?? '') ?></small>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-wallet2 me-2"></i>Wallet</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>
<section class="content-wrap">
