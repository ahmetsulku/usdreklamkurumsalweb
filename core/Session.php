<?php
/**
 * Güvenli Session Yönetimi
 */

class Session
{
    private static bool $started = false;
    
    /**
     * Session başlat
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }
        
        // Session ayarları
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        
        // HTTPS varsa secure cookie
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        session_start();
        self::$started = true;
        
        // Session fixation koruması
        if (!isset($_SESSION['_initialized'])) {
            session_regenerate_id(true);
            $_SESSION['_initialized'] = true;
            $_SESSION['_created'] = time();
        }
        
        // Session timeout kontrolü
        if (isset($_SESSION['_last_activity']) && 
            (time() - $_SESSION['_last_activity'] > SESSION_LIFETIME)) {
            self::destroy();
            self::start();
        }
        
        $_SESSION['_last_activity'] = time();
    }
    
    /**
     * Session değeri al
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Session değeri ayarla
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Session değeri var mı?
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Session değerini sil
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Flash mesaj ayarla (bir kez gösterilir)
     */
    public static function flash(string $key, $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Flash mesaj al ve sil
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    /**
     * Tüm flash mesajları al
     */
    public static function getAllFlash(): array
    {
        self::start();
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }
    
    /**
     * Session yok et
     */
    public static function destroy(): void
    {
        if (self::$started) {
            $_SESSION = [];
            
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            session_destroy();
            self::$started = false;
        }
    }
    
    /**
     * Session ID'yi yenile (güvenlik için)
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
    
    /**
     * Admin giriş kontrolü
     */
    public static function isLoggedIn(): bool
    {
        return self::get('admin_logged_in') === true;
    }
    
    /**
     * Admin bilgisini al
     */
    public static function getAdmin(): ?array
    {
        return self::get('admin_user');
    }
}