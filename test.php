<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>Ana Sayfa Render Testi</h3>";

try {
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/core/helpers.php';
    require_once __DIR__ . '/core/Database.php';
    require_once __DIR__ . '/core/Session.php';
    require_once __DIR__ . '/core/CSRF.php';
    require_once __DIR__ . '/core/Validator.php';
    require_once __DIR__ . '/core/Router.php';
    require_once __DIR__ . '/core/Mailer.php';
    require_once __DIR__ . '/core/Cache.php';
    require_once __DIR__ . '/core/RateLimiter.php';
    
    Session::start();
    
    echo "Tüm core dosyalar yüklendi ✅<br>";
    
    require_once __DIR__ . '/controllers/HomeController.php';
    
    echo "HomeController yüklendi ✅<br>";
    echo "Ana sayfa render ediliyor...<br><hr>";
    
    $controller = new HomeController();
    $controller->index();
    
} catch (Throwable $e) {
    echo "<div style='background:#fee;padding:20px;border:1px solid #c00;margin:20px 0;'>";
    echo "<strong>HATA:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Dosya:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Satır:</strong> " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}