<?php require_once __DIR__ . '/config/helpers.php'; if (is_logged_in()) redirect('dashboard.php'); ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title>Forgot Password | <?= e(firm('short_name', 'Tax Portal')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= e(asset('assets/css/themes.css')) ?>" rel="stylesheet"><link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet"><link href="<?= e(asset('assets/css/responsive.css')) ?>" rel="stylesheet">
</head><body class="theme-light color-<?= e(firm('theme_color', 'royal')) ?> style-gradient">
<main class="login-page"><section class="login-brand"><img src="<?= e(asset(firm('logo') ?: 'assets/uploads/firm/default-logo.svg')) ?>"><h1>Password Recovery</h1><p class="fs-5 opacity-75">Reset your staff portal password safely.</p></section>
<section class="login-form-wrap"><div class="login-card"><h2>Forgot Password</h2><p class="muted mb-4">Verify WhatsApp OTP, then set a new password.</p>
<form id="forgotForm" action="auth/forgot_process.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" id="forgotAction" value="send">
<div class="mb-3"><label class="form-label">Registered Mobile</label><input class="form-control" name="mobile" required></div>
<div class="mb-3 d-none step-otp"><label class="form-label">OTP</label><input class="form-control" name="otp" maxlength="6"></div>
<div class="d-none step-pass"><div class="mb-3"><label class="form-label">New Password</label><input class="form-control" type="password" name="password"></div><div class="mb-3"><label class="form-label">Confirm Password</label><input class="form-control" type="password" name="confirm_password"></div></div>
<button class="btn btn-primary w-100" type="submit">Send OTP</button><a class="d-block text-center mt-3" href="login.php">Back to login</a></form></div></section></main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><script src="<?= e(asset('assets/js/ajax.js')) ?>"></script>
<script>
document.getElementById('forgotForm').addEventListener('submit', async e => {
 e.preventDefault(); const form=e.target, action=document.getElementById('forgotAction'), btn=form.querySelector('button'); const data=await safeFetch(form.action,{method:'POST',body:new FormData(form),button:btn});
 if(!data.success) return;
 if(action.value==='send'){Swal.fire('Sent',data.message,'success');action.value='verify';document.querySelector('.step-otp').classList.remove('d-none');btn.textContent='Verify OTP';}
 else if(action.value==='verify'){Swal.fire('Verified',data.message,'success');action.value='reset';document.querySelector('.step-pass').classList.remove('d-none');btn.textContent='Change Password';}
 else if(data.redirect){Swal.fire('Success',data.message,'success').then(()=>location.href=data.redirect);}
});
</script></body></html>
