<?php
require_once __DIR__ . '/db.php';

function get_firm(): array
{
    static $firm = null;
    global $pdo;
    if ($firm === null) {
        $stmt = $pdo->query("SELECT * FROM firm_settings WHERE status = 1 ORDER BY id ASC LIMIT 1");
        $firm = $stmt->fetch() ?: [];
    }
    return $firm;
}

function firm($key, $default = '')
{
    $firm = get_firm();
    return $firm[$key] ?? $default;
}

function current_user(): ?array
{
    static $user = null;
    global $pdo;
    if ($user === null && !empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function asset($path): string
{
    return ltrim((string)$path, '/');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$token || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh and try again.']);
        exit;
    }
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']) && !empty($_SESSION['session_token']);
}

function user_role(): string
{
    return (string)($_SESSION['role'] ?? '');
}

function can_access($menu_key): bool
{
    $role = user_role();
    if (in_array($role, ['super_admin', 'admin'], true)) {
        return true;
    }
    return in_array($menu_key, ['dashboard', 'profile', 'logout'], true);
}

function clean_mobile($mobile): string
{
    $digits = preg_replace('/\D+/', '', (string)$mobile);
    if (strlen($digits) === 10) {
        return '91' . $digits;
    }
    return $digits;
}

function redirect($url): void
{
    header('Location: ' . $url);
    exit;
}

function json_response(bool $success, string $message, array $extra = []): void
{
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

function upload_image(string $field, string $dir): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }
    if ($_FILES[$field]['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Image must be smaller than 2 MB.');
    }
    $targetDir = __DIR__ . '/../' . trim($dir, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $target = $targetDir . '/' . $name;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }
    return trim($dir, '/') . '/' . $name;
}
