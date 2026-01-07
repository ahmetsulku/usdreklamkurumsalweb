<?php
/**
 * USD Reklam - Front Controller
 * Tüm istekler bu dosyadan geçer
 */

// Hata gösterimi (geliştirme modunda)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Config yükle
$configFile = __DIR__ . '/config/config.php';
if (!file_exists($configFile)) {
    die('Config dosyası bulunamadı.');
}
require_once $configFile;

// Hata gösterimini config'e göre ayarla
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    ini_set('display_errors', 1);
}

// Core dosyaları yükle
$coreFiles = [
    'helpers.php',
    'Database.php', 
    'Session.php',
    'CSRF.php',
    'Validator.php',
    'Router.php',
    'Mailer.php',
    'Cache.php',
    'RateLimiter.php'
];

foreach ($coreFiles as $file) {
    $path = __DIR__ . '/core/' . $file;
    if (!file_exists($path)) {
        die('Core dosyası bulunamadı: ' . $file);
    }
    require_once $path;
}

// Session başlat
Session::start();

// Veritabanı kurulumu kontrolü
try {
    if (!Database::isInstalled()) {
        if (!Database::install()) {
            die('Veritabanı kurulumu başarısız.');
        }
    }
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die('Veritabanı hatası: ' . $e->getMessage());
    }
    die('Veritabanı bağlantı hatası.');
}

// URI al ve temizle
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = strtok($uri, '?'); // Query string kaldır
$uri = rawurldecode($uri);
$uri = rtrim($uri, '/') ?: '/';

// Admin paneli kontrolü - /panel ile başlıyorsa admin'e yönlendir
if (strpos($uri, '/panel') === 0) {
    $adminIndex = __DIR__ . '/admin/index.php';
    if (file_exists($adminIndex)) {
        require $adminIndex;
        exit;
    }
}

// 301 Yönlendirmeleri kontrol et
try {
    $redirect = Database::fetchOne(
        "SELECT new_url FROM redirects WHERE old_url = ? AND is_active = 1",
        [$uri]
    );
    if ($redirect) {
        header('Location: ' . $redirect['new_url'], true, 301);
        exit;
    }
} catch (Exception $e) {
    // Yönlendirme tablosu yoksa devam et
}

// Controller'ları yükle
$controllerFiles = [
    'HomeController.php',
    'ProductController.php',
    'ServiceController.php',
    'PageController.php',
    'BlogController.php',
    'QuoteController.php',
    'SitemapController.php'
];

foreach ($controllerFiles as $file) {
    $path = __DIR__ . '/controllers/' . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

// Router ile yönlendirme
$router = new Router();

// Ana sayfa
$router->get('/', 'HomeController@index');

// Statik sayfalar
$router->get('/hakkimizda', 'PageController@about');
$router->get('/iletisim', 'PageController@contact');
$router->post('/iletisim', 'PageController@contactSubmit');

// Hizmetler
$router->get('/hizmetler', 'ServiceController@index');
$router->get('/hizmetler/{slug}', 'ServiceController@detail');

// Blog
$router->get('/blog', 'BlogController@index');
$router->get('/blog/kategori/{slug}', 'BlogController@category');
$router->get('/blog/{slug}', 'BlogController@detail');

// API - Teklif talepleri
$router->post('/api/quote/call', 'QuoteController@submitCall');
$router->post('/api/quote/email', 'QuoteController@submitEmail');
$router->post('/api/quote/whatsapp', 'QuoteController@submitWhatsapp');
$router->post('/api/quote/check', 'QuoteController@checkExisting');

// Sitemap ve robots
$router->get('/sitemap.xml', 'SitemapController@index');
$router->get('/robots.txt', 'SitemapController@robots');

// Dinamik ürün rotaları (en sonda olmalı)
$router->get('/{category}', 'ProductController@category');
$router->get('/{category}/{product}', 'ProductController@detail');

// Route'u çalıştır
try {
    if (!$router->dispatch($uri, $_SERVER['REQUEST_METHOD'])) {
        // 404 Sayfa bulunamadı
        http_response_code(404);
        
        $errorView = __DIR__ . '/views/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>404 - Sayfa Bulunamadı</h1>';
            echo '<p><a href="/">Ana Sayfaya Dön</a></p>';
        }
    }
} catch (Exception $e) {
    if (DEBUG_MODE) {
        echo '<h1>Hata</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Bir hata oluştu</h1>';
        echo '<p><a href="/">Ana Sayfaya Dön</a></p>';
    }
}