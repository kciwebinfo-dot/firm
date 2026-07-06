<?php
require_once __DIR__ . '/../auth/auth_check.php';
verify_csrf();

$mode = $_POST['theme_mode'] ?? 'light';
$color = $_POST['theme_color'] ?? 'royal';
$style = $_POST['theme_style'] ?? 'default';
$modes = ['light', 'dark'];
$colors = ['royal', 'orange', 'blue', 'green', 'purple', 'red', 'teal', 'pink', 'indigo', 'cyan', 'amber', 'emerald'];
$styles = ['default', 'soft', 'solid', 'gradient'];
if (!in_array($mode, $modes, true) || !in_array($color, $colors, true) || !in_array($style, $styles, true)) {
    json_response(false, 'Invalid theme option.');
}
$pdo->prepare("UPDATE users SET theme_mode = ?, theme_color = ?, theme_style = ?, updated_at = NOW() WHERE id = ?")
    ->execute([$mode, $color, $style, $_SESSION['user_id']]);
json_response(true, 'Theme saved.');
