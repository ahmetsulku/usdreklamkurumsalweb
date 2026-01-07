<?php
/**
 * Yardımcı Fonksiyonlar
 */

/**
 * XSS koruması ile escape
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * URL oluştur
 */
function url(string $path = ''): string
{
    $base = rtrim(SITE_URL, '/');
    $path = ltrim($path, '/');
    return $path ? $base . '/' . $path : $base;
}

/**
 * Asset URL oluştur
 */
function asset(string $path): string
{
    return '/' . ltrim($path, '/');
}

/**
 * Yönlendirme
 */
function redirect(string $url, int $code = 302): void
{
    header('Location: ' . $url, true, $code);
    exit;
}

/**
 * JSON response
 */
function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 404 hatası
 */
function abort404(): void
{
    http_response_code(404);
    $errorView = __DIR__ . '/../views/errors/404.php';
    if (file_exists($errorView)) {
        include $errorView;
    } else {
        echo '<h1>404 - Sayfa Bulunamadi</h1>';
    }
    exit;
}

/**
 * Slug olustur (Turkce karakter destekli)
 */
function slugify(string $text): string
{
    $turkishChars = [
        'ı' => 'i', 'İ' => 'i', 'ğ' => 'g', 'Ğ' => 'g',
        'ü' => 'u', 'Ü' => 'u', 'ş' => 's', 'Ş' => 's',
        'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c'
    ];
    
    $text = strtr($text, $turkishChars);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Metni kisalt
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Tarih formatla
 */
function formatDate(?string $date, string $format = 'd.m.Y'): string
{
    if (!$date) return '';
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Turkce tarih formatla
 */
function formatDateTurkish(?string $date): string
{
    if (!$date) return '';
    
    $months = [
        1 => 'Ocak', 2 => 'Subat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayis', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Agustos',
        9 => 'Eylul', 10 => 'Ekim', 11 => 'Kasim', 12 => 'Aralik'
    ];
    
    try {
        $dt = new DateTime($date);
        $day = $dt->format('d');
        $month = $months[(int)$dt->format('m')];
        $year = $dt->format('Y');
        return "$day $month $year";
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Referans numarasi olustur
 */
function generateRefNo(string $prefix = 'TKL'): string
{
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -5));
}

/**
 * Telefon formatla
 */
function formatPhone(string $phone): string
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10) {
        return '0' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8, 2);
    }
    if (strlen($phone) === 11 && $phone[0] === '0') {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 2) . ' ' . substr($phone, 9, 2);
    }
    return $phone;
}

/**
 * WhatsApp linki olustur
 */
function whatsappLink(string $phone, string $message = ''): string
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strpos($phone, '90') !== 0 && strlen($phone) === 10) {
        $phone = '90' . $phone;
    }
    $url = 'https://wa.me/' . $phone;
    if ($message) {
        $url .= '?text=' . urlencode($message);
    }
    return $url;
}

/**
 * Gorsel URL olustur
 */
function imageUrl(?string $path, string $default = 'images/static/placeholder.svg'): string
{
    if ($path && file_exists(__DIR__ . '/../public/' . $path)) {
        return '/' . $path;
    }
    return '/' . $default;
}

/**
 * Client IP adresini al
 */
function getClientIP(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/**
 * Ayar degerini al
 */
function setting(string $key, $default = null)
{
    static $settings = null;
    
    if ($settings === null) {
        try {
            $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $settings = [];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * View renderla
 */
function view(string $name, array $data = []): void
{
    extract($data);
    
    $viewPath = __DIR__ . '/../views/' . str_replace('.', '/', $name) . '.php';
    
    if (!file_exists($viewPath)) {
        echo "View bulunamadi: " . e($name);
        return;
    }
    
    if (strpos($name, 'layouts/') === 0 || strpos($name, 'partials/') === 0 || strpos($name, 'errors/') === 0) {
        include $viewPath;
        return;
    }
    
    $data['content'] = $name;
    extract($data);
    
    $layoutPath = __DIR__ . '/../views/layouts/main.php';
    if (file_exists($layoutPath)) {
        include $layoutPath;
    } else {
        include $viewPath;
    }
}

/**
 * Partial renderla
 */
function partial(string $name, array $data = []): void
{
    extract($data);
    $path = __DIR__ . '/../views/partials/' . $name . '.php';
    if (file_exists($path)) {
        include $path;
    }
}

/**
 * CSRF input
 */
function csrfInput(): string
{
    if (class_exists('CSRF')) {
        return CSRF::input();
    }
    return '';
}

/**
 * Flash mesaj
 */
function flash(string $key, string $message): void
{
    if (class_exists('Session')) {
        Session::flash($key, $message);
    }
}

/**
 * Debug dump
 */
function dd(...$vars): void
{
    echo '<pre style="background:#1e1e1e;color:#fff;padding:15px;margin:10px;border-radius:5px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

/**
 * Hata logla
 */
function logError(string $message, array $context = []): void
{
    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message}{$contextStr}\n";
    
    @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}