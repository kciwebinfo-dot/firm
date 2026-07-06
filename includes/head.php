<?php
$user = current_user() ?: [];
$pageTitle = $pageTitle ?? 'Dashboard';
$logo = firm('logo') ?: 'assets/uploads/firm/default-logo.svg';
$favicon = firm('favicon') ?: $logo;
$themeMode = $user['theme_mode'] ?? 'light';
$themeColor = $user['theme_color'] ?? firm('theme_color', 'royal');
$themeStyle = $user['theme_style'] ?? 'default';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle) ?> | <?= e(firm('short_name', firm('firm_name', 'Tax Portal'))) ?></title>
    <link rel="icon" href="<?= e(asset($favicon)) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= e(asset('assets/css/app.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('assets/css/themes.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('assets/css/responsive.css')) ?>" rel="stylesheet">
</head>
<body class="theme-<?= e($themeMode) ?> color-<?= e($themeColor) ?> style-<?= e($themeStyle) ?>" data-theme-mode="<?= e($themeMode) ?>" data-theme-color="<?= e($themeColor) ?>" data-theme-style="<?= e($themeStyle) ?>">
<div class="app-shell">
