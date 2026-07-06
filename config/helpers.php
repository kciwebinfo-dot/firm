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
    return in_array($menu_key, ['dashboard', 'clients', 'generate_bill', 'bills', 'payments', 'profile', 'logout'], true);
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

function encode_token($value): string
{
    return rtrim(strtr(base64_encode((string)$value), '+/', '-_'), '=');
}

function decode_token($token): string
{
    $token = strtr((string)$token, '-_', '+/');
    $pad = strlen($token) % 4;
    if ($pad) {
        $token .= str_repeat('=', 4 - $pad);
    }
    $decoded = base64_decode($token, true);
    return $decoded === false ? '' : $decoded;
}

function money($amount): string
{
    return number_format((float)$amount, 2);
}

function amount_in_words_indian($amount): string
{
    $number = (int)floor((float)$amount);
    if ($number === 0) {
        return 'Zero Rupees Only';
    }
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    $two = function ($n) use ($ones, $tens) {
        if ($n < 20) return $ones[$n];
        return trim($tens[(int)($n / 10)] . ' ' . $ones[$n % 10]);
    };
    $three = function ($n) use ($two, $ones) {
        $text = '';
        if ($n >= 100) {
            $text .= $ones[(int)($n / 100)] . ' Hundred ';
            $n %= 100;
        }
        return trim($text . ($n ? $two($n) : ''));
    };
    $parts = [];
    foreach ([10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand', 1 => ''] as $value => $label) {
        if ($number >= $value) {
            $chunk = (int)($number / $value);
            $number %= $value;
            $parts[] = trim($three($chunk) . ' ' . $label);
        }
    }
    return trim(implode(' ', array_filter($parts))) . ' Rupees Only';
}

function next_document_no(string $table, string $column, string $prefix): string
{
    global $pdo;
    $month = date('Ym');
    $like = $prefix . '/' . $month . '/%';
    $stmt = $pdo->prepare("SELECT {$column} FROM {$table} WHERE {$column} LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$like]);
    $last = (string)($stmt->fetchColumn() ?: '');
    $next = 1;
    if ($last) {
        $parts = explode('/', $last);
        $next = ((int)end($parts)) + 1;
    }
    return $prefix . '/' . $month . '/' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
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
