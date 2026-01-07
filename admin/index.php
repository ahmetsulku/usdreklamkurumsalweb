<?php
/**
 * Admin Panel - Front Controller
 */

// Hata gösterimi (geliştirme için)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ana config ve core dosyaları
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Mailer.php';
require_once __DIR__ . '/../core/Cache.php';
require_once __DIR__ . '/../core/RateLimiter.php';

// Session başlat
Session::start();

// Admin helper fonksiyonları
function isLoggedIn(): bool {
    return Session::get('admin_logged_in') === true;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/panel/giris');
    }
}

function requirePasswordChange(): void {
    $admin = Session::get('admin_user');
    if ($admin && $admin['must_change_password']) {
        if (strpos($_SERVER['REQUEST_URI'], 'sifre-degistir') === false) {
            redirect('/panel/sifre-degistir');
        }
    }
}

function getAdmin(): ?array {
    return Session::get('admin_user');
}

// URL'den sayfa belirle
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?'); // Query string'i kaldır
$uri = rtrim($uri, '/');

// /panel prefix'ini kaldır
$page = str_replace('/panel', '', $uri);
if (empty($page)) $page = '/';

// Routing
switch ($page) {
    case '/':
    case '/dashboard':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/DashboardController.php';
        break;
        
    case '/giris':
        if (isLoggedIn()) {
            redirect('/panel/dashboard');
        }
        require __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->loginForm();
        break;
        
    case '/giris-yap':
        require __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case '/cikis':
        require __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case '/sifre-degistir':
        requireLogin();
        require __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->changePassword();
        } else {
            $controller->changePasswordForm();
        }
        break;
        
    case '/urunler':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/ProductController.php';
        break;
        
    case '/kategoriler':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/CategoryController.php';
        break;
        
    case '/hizmetler':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/ServiceController.php';
        break;
        
    case '/blog':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/BlogController.php';
        break;
        
    case '/sayfalar':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/PageController.php';
        break;
        
    case '/slider':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/SliderController.php';
        break;
        
    case '/yorumlar':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/ReviewController.php';
        break;
        
    case '/teklif-talepleri':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/QuoteController.php';
        break;
        
    case '/ayarlar':
        requireLogin();
        requirePasswordChange();
        require __DIR__ . '/controllers/SettingsController.php';
        break;
        
    default:
        // Alt sayfalar için kontrol (örn: /urunler/ekle, /urunler/duzenle/5)
        if (preg_match('#^/urunler/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/ProductController.php';
        } elseif (preg_match('#^/kategoriler/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/CategoryController.php';
        } elseif (preg_match('#^/hizmetler/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/ServiceController.php';
        } elseif (preg_match('#^/blog/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/BlogController.php';
        } elseif (preg_match('#^/sayfalar/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/PageController.php';
        } elseif (preg_match('#^/slider/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/SliderController.php';
        } elseif (preg_match('#^/teklif-talepleri/(.+)$#', $page, $matches)) {
            requireLogin();
            requirePasswordChange();
            $_GET['action'] = $matches[1];
            require __DIR__ . '/controllers/QuoteController.php';
        } else {
            http_response_code(404);
            echo '<h1>404 - Sayfa Bulunamadı</h1>';
            echo '<p><a href="/panel">Panel\'e Dön</a></p>';
        }
        break;
}