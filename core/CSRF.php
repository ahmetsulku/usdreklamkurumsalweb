<?php
/**
 * CSRF Token Koruması
 */

class CSRF
{
    /**
     * Token oluştur veya mevcut olanı döndür
     */
    public static function generate(): string
    {
        Session::start();
        
        if (!Session::has(CSRF_TOKEN_NAME)) {
            $token = bin2hex(random_bytes(32));
            Session::set(CSRF_TOKEN_NAME, $token);
        }
        
        return Session::get(CSRF_TOKEN_NAME);
    }
    
    /**
     * Token doğrula
     */
    public static function verify(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        
        $sessionToken = Session::get(CSRF_TOKEN_NAME);
        
        if (empty($sessionToken)) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * POST isteğinden token doğrula
     */
    public static function verifyRequest(): bool
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return self::verify($token);
    }
    
    /**
     * Hidden input alanı oluştur
     */
    public static function input(): string
    {
        $token = self::generate();
        $name = CSRF_TOKEN_NAME;
        return '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Meta tag oluştur (AJAX için)
     */
    public static function meta(): string
    {
        $token = self::generate();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Token yenile
     */
    public static function refresh(): string
    {
        Session::remove(CSRF_TOKEN_NAME);
        return self::generate();
    }
}