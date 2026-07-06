# Tax Consulting Firm Staff Management Portal

PHP + MySQL + Bootstrap 5 staff management portal for Hostinger shared hosting.

## Hostinger Setup

1. Upload all files to `public_html`.
2. Use the existing database `u937735496_tax` with the existing `firm_settings` and `users` tables.
3. Update `config/db.php` with the correct Hostinger database username and password.
4. Ensure `assets/uploads/users` and `assets/uploads/firm` are writable.
5. Open `login.php`.
6. Login using an active user from the existing `users` table.
7. Test theme saving, profile update, WhatsApp OTP, forgot password, logout, and the mobile sidebar.

## Notes

- The app uses `users.password` with `password_hash()` and `password_verify()`.
- The app uses `users.session_token` and `users.session_expires` for active session protection.
- WhatsApp Cloud API credentials are read from `firm_settings.wa_meta_token`, `firm_settings.wa_phone_id`, and `firm_settings.wa_api_version`.
- Firm logo, favicon, firm name, short name, mobile, email, and footer text are loaded dynamically from `firm_settings`.
