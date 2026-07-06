<?php require_once __DIR__ . '/config/helpers.php'; if (is_logged_in()) redirect('dashboard.php'); ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title>WhatsApp OTP | <?= e(firm('short_name', 'Tax Portal')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= e(asset('assets/css/themes.css')) ?>" rel="stylesheet"><link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet"><link href="<?= e(asset('assets/css/responsive.css')) ?>" rel="stylesheet">
</head><body class="theme-light color-<?= e(firm('theme_color', 'royal')) ?> style-gradient">
<main class="login-page"><section class="login-brand"><img src="<?= e(asset(firm('logo') ?: 'assets/uploads/firm/default-logo.svg')) ?>"><h1>WhatsApp OTP</h1><p class="fs-5 opacity-75">Secure login for <?= e(firm('short_name', 'your firm')) ?> staff.</p></section>
<section class="login-form-wrap"><div class="login-card"><h2>Login with OTP</h2><p class="muted mb-4">Enter your registered mobile number.</p>
<form id="otpForm" action="auth/otp_process.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" id="otpAction" value="send">
<div class="mb-3"><label class="form-label">Mobile</label><input class="form-control" name="mobile" id="mobile" required></div>
<div class="mb-3 d-none" id="otpBox"><label class="form-label">OTP</label><input class="form-control" name="otp" maxlength="6"></div>
<button class="btn btn-primary w-100" type="submit">Send OTP</button><a class="d-block text-center mt-3" href="login.php">Back to password login</a></form></div></section></main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><script src="<?= e(asset('assets/js/ajax.js')) ?>?v=20260706-2"></script>
<script>
document.getElementById('otpForm').addEventListener('submit', async e => {
 e.preventDefault(); const form=e.target, btn=form.querySelector('button'); const data=await safeFetch(form.action,{method:'POST',body:new FormData(form),button:btn});
 if(data.success && document.getElementById('otpAction').value==='send'){Swal.fire('Sent',data.message,'success');document.getElementById('otpAction').value='verify';document.getElementById('otpBox').classList.remove('d-none');btn.textContent='Verify OTP';}
 else if(data.success && data.redirect){location.href=data.redirect;}
});
</script></body></html>
