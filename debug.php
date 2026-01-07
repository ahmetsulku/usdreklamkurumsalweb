<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>Index.php Debug</h3>";

// 1. Config
echo "1. Config yükleniyor...<br>";
require_once __DIR__ . '/config/config.php';
echo "✅ Config OK<br>";

// 2. Core dosyaları
$coreFiles = ['helpers.php', 'Database.php', 'Session.php', 'CSRF.php', 'Validator.php', 'Router.php', 'Mailer.php', 'Cache.php', 'RateLimiter.php'];
echo "2. Core dosyaları yükleniyor...<br>";
foreach ($coreFiles as $file) {
    require_once __DIR__ . '/core/' . $file;
}
echo "✅ Core OK<br>";

// 3. Session
echo "3. Session başlatılıyor...<br>";
Session::start();
echo "✅ Session OK<br>";

// 4. Database kurulum kontrolü
echo "4. Database kontrol...<br>";
if (!Database::isInstalled()) {
    echo "⚠️ Database kurulu değil, kuruluyor...<br>";
    Database::install();
}
echo "✅ Database OK<br>";

// 5. Router
echo "5. Router test...<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'YOK') . "<br>";

// 6. HomeController
echo "6. HomeController yükleniyor...<br>";
require_once __DIR__ . '/controllers/HomeController.php';
echo "✅ HomeController OK<br>";

echo "<hr><h3>Tüm kontroller başarılı! Sorun .htaccess olabilir.</h3>";
echo "<p><a href='/test.php'>Test.php ile ana sayfayı gör</a></p>";