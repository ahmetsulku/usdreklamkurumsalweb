<?php
/**
 * Minimal Index Test
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Starting...<br>";

// Config
require_once __DIR__ . '/config/config.php';
echo "Step 2: Config loaded<br>";

// Core files
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Validator.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Mailer.php';
require_once __DIR__ . '/core/Cache.php';
require_once __DIR__ . '/core/RateLimiter.php';
echo "Step 3: Core loaded<br>";

// Session
Session::start();
echo "Step 4: Session started<br>";

// Database check
if (!Database::isInstalled()) {
    echo "Step 5: Installing database...<br>";
    Database::install();
}
echo "Step 5: Database OK<br>";

// URI
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = strtok($uri, '?');
$uri = rtrim($uri, '/') ?: '/';
echo "Step 6: URI = " . htmlspecialchars($uri) . "<br>";

// Simple routing test
echo "Step 7: Routing...<br>";

if ($uri === '/' || $uri === '/index-test.php') {
    echo "Step 8: Loading HomeController...<br>";
    require_once __DIR__ . '/controllers/HomeController.php';
    echo "Step 9: HomeController loaded, calling index()...<br>";
    $controller = new HomeController();
    $controller->index();
} else {
    echo "Step 8: Unknown route, showing 404<br>";
}