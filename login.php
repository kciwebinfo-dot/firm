<?php
require_once __DIR__ . '/config/helpers.php';
if (is_logged_in()) redirect('dashboard.php');
$message = $_GET['message'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>Login | <?= e(firm('short_name', 'Tax Portal')) ?></title>
    <link rel="icon" href="<?= e(asset(firm('favicon') ?: firm('logo'))) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/themes.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('assets/css/responsive.css')) ?>" rel="stylesheet">
</head>
<body class="theme-light color-<?= e(firm('theme_color', 'royal')) ?> style-gradient">
<main class="login-page">
    <section class="login-brand">
        <img src="<?= e(asset(firm('logo') ?: 'assets/uploads/firm/default-logo.svg')) ?>" alt="Logo">
        <h1><?= e(firm('firm_name', 'Tax Consulting Firm')) ?></h1>
        <p class="fs-5 opacity-75"><?= e(firm('tagline', 'Secure staff management portal')) ?></p>
        <p class="mt-4 mb-0"><i class="bi bi-telephone me-2"></i><?= e(firm('mobile')) ?> <span class="mx-2">|</span> <i class="bi bi-envelope me-2"></i><?= e(firm('email')) ?></p>
    </section>
    <section class="login-form-wrap">
        <div class="login-card">
            <h2 class="mb-1">Welcome back</h2>
            <p class="muted mb-4">Login with username, email, or mobile.</p>
            <form id="loginForm" action="auth/login_process.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="kill_old_session" id="killOldSession" value="0">
                <div class="mb-3">
                    <label class="form-label">Login ID</label>
                    <input class="form-control" name="login" required autocomplete="username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="showPassword"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Login</button>
            </form>
            <div class="d-flex justify-content-between mt-3">
                <a href="otp-login.php">Login with WhatsApp OTP</a>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= e(asset('assets/js/ajax.js')) ?>?v=20260706-2"></script>
<script>
document.getElementById('showPassword').addEventListener('click', () => {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
});
document.getElementById('loginForm').addEventListener('submit', async event => {
    event.preventDefault();
    const form = event.target;
    const data = await safeFetch(form.action, { method: 'POST', body: new FormData(form), button: form.querySelector('button[type="submit"]') });
    if (data.dual) {
        Swal.fire({
            icon: 'warning',
            title: 'You are already logged in on another device.',
            text: 'Continue by killing the old session.',
            showCancelButton: true,
            confirmButtonText: 'Kill Old Session & Continue'
        }).then(async result => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
                const killed = await safeFetch('ajax/kill-session.php', { method: 'POST', body: fd });
                if (killed.success) location.href = killed.redirect;
            }
        });
        return;
    }
    if (data.success) location.href = data.redirect;
});
<?php if ($message === 'timeout'): ?>Swal.fire('Session expired', 'Please login again.', 'info');<?php endif; ?>
<?php if ($message === 'dual'): ?>Swal.fire('Logged out', 'Your account was opened on another device.', 'warning');<?php endif; ?>
<?php if ($message === 'logout'): ?>Swal.fire('Logged out', 'You have logged out successfully.', 'success');<?php endif; ?>
</script>
</body>
</html>
