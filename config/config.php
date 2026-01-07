<?php
/**
 * USD Reklam - Ana Konfigürasyon Dosyası
 */

// Hata raporlama (production'da kapatın)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/error.log');

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Site sabitleri
define('SITE_NAME', 'USD Reklam');
define('SITE_URL', 'https://deneme.fircadanders.com'); // Kendi domain'inizi yazın
define('ADMIN_PATH', '/panel');

// Veritabanı ayarları
define('DB_DRIVER', 'sqlite'); // 'sqlite' veya 'mysql'

// SQLite için
define('DB_SQLITE_PATH', __DIR__ . '/../storage/database.sqlite');

// MySQL için (eğer kullanacaksanız)
define('DB_HOST', 'localhost');
define('DB_NAME', 'usdreklam');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Güvenlik
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_NAME', 'usdreklam_session');
define('SESSION_LIFETIME', 7200); // 2 saat

// Upload ayarları
define('UPLOAD_PATH', __DIR__ . '/../public/images/uploads/');
define('UPLOAD_URL', '/images/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

// Cache ayarları
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../storage/cache/');
define('CACHE_TTL', 3600); // 1 saat

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 5); // form gönderimi
define('RATE_LIMIT_WINDOW', 60); // saniye

// WhatsApp numarası (başında 90 ile)
define('WHATSAPP_NUMBER', '905551234567');

// E-posta alıcısı
define('NOTIFICATION_EMAIL', 'usdreklam@gmail.com');

// Debug modu (production'da false yapın)
define('DEBUG_MODE', true);