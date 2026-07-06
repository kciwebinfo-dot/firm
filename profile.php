<?php
require_once __DIR__ . '/auth/auth_check.php';
$pageTitle = 'Profile';
$active = 'profile';
$user = current_user();
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/sidebar.php';
include __DIR__ . '/includes/topbar.php';
?>
<div class="page-title"><div><h1>Profile</h1><p class="muted">Manage account details and session preferences.</p></div><span class="badge badge-theme"><?= e($user['role']) ?></span></div>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="panel-card">
            <div class="text-center mb-3">
                <img id="photoPreview" class="profile-photo" src="<?= e(asset($user['photo'] ?: 'assets/uploads/users/default-user.svg')) ?>" alt="Profile">
                <h4 class="mt-3 mb-0"><?= e($user['name']) ?></h4>
                <p class="muted"><?= e($user['email']) ?></p>
            </div>
            <p><strong>Last login:</strong> <?= e($user['last_login'] ?: 'N/A') ?></p>
            <p><strong>Last IP:</strong> <?= e($user['last_ip'] ?: 'N/A') ?></p>
            <?php if (!empty($user['sign'])): ?><p><strong>Signature:</strong><br><img class="signature-img" src="<?= e(asset($user['sign'])) ?>" alt="Signature"></p><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="panel-card mb-3">
            <h5>Update Profile</h5>
            <form class="ajax-form" action="ajax/profile-update.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= e($user['name']) ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= e($user['email']) ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Mobile</label><input class="form-control" name="mobile" value="<?= e($user['mobile']) ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Photo</label><input class="form-control" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" data-preview="#photoPreview"></div>
                    <div class="col-md-6"><label class="form-label">Signature</label><input class="form-control" type="file" name="sign" accept=".jpg,.jpeg,.png,.webp"></div>
                </div>
                <button class="btn btn-primary mt-3" type="submit">Save Profile</button>
            </form>
        </div>
        <div class="panel-card mb-3">
            <h5>Change Password</h5>
            <form class="ajax-form" action="ajax/password-update.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="row g-3">
                    <div class="col-md-4"><input class="form-control" type="password" name="old_password" placeholder="Old password" required></div>
                    <div class="col-md-4"><input class="form-control" type="password" name="new_password" placeholder="New password" required></div>
                    <div class="col-md-4"><input class="form-control" type="password" name="confirm_password" placeholder="Confirm password" required></div>
                </div>
                <button class="btn btn-primary mt-3" type="submit">Change Password</button>
            </form>
        </div>
        <div class="panel-card">
            <h5>Active Session Duration</h5>
            <form class="ajax-form" action="ajax/session-setting.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <select class="form-select" name="session_timeout_minutes">
                    <?php foreach ([15,30,45,60,120] as $min): ?><option value="<?= $min ?>" <?= (($_SESSION['session_timeout_minutes'] ?? 15) == $min) ? 'selected' : '' ?>><?= $min ?> minutes</option><?php endforeach; ?>
                </select>
                <button class="btn btn-primary mt-3" type="submit">Update Session</button>
            </form>
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>bindAjaxForm('.ajax-form'));</script>
<?php include __DIR__ . '/includes/footer.php'; include __DIR__ . '/includes/scripts.php'; ?>
