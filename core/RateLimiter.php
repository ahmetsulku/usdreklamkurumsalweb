<?php
/**
 * Rate Limiting (Spam Koruması)
 */

class RateLimiter
{
    /**
     * İstek limitini kontrol et
     */
    public static function check(string $action, int $maxAttempts = null, int $windowSeconds = null): bool
    {
        if (!RATE_LIMIT_ENABLED) {
            return true;
        }
        
        $maxAttempts = $maxAttempts ?? RATE_LIMIT_REQUESTS;
        $windowSeconds = $windowSeconds ?? RATE_LIMIT_WINDOW;
        
        $ip = getClientIP();
        $now = date('Y-m-d H:i:s');
        $windowStart = date('Y-m-d H:i:s', time() - $windowSeconds);
        
        try {
            // Eski kayıtları temizle
            Database::query(
                "DELETE FROM rate_limits WHERE last_attempt < ?",
                [$windowStart]
            );
            
            // Mevcut kaydı kontrol et
            $record = Database::fetchOne(
                "SELECT * FROM rate_limits WHERE ip_address = ? AND action_type = ? AND first_attempt > ?",
                [$ip, $action, $windowStart]
            );
            
            if ($record) {
                if ($record['attempts'] >= $maxAttempts) {
                    return false; // Limit aşıldı
                }
                
                // Sayacı artır
                Database::query(
                    "UPDATE rate_limits SET attempts = attempts + 1, last_attempt = ? WHERE id = ?",
                    [$now, $record['id']]
                );
            } else {
                // Yeni kayıt
                Database::insert('rate_limits', [
                    'ip_address' => $ip,
                    'action_type' => $action,
                    'attempts' => 1,
                    'first_attempt' => $now,
                    'last_attempt' => $now
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            logError('Rate limit hatası', ['error' => $e->getMessage()]);
            return true; // Hata durumunda izin ver
        }
    }
    
    /**
     * Kalan deneme hakkı
     */
    public static function remaining(string $action, int $maxAttempts = null, int $windowSeconds = null): int
    {
        if (!RATE_LIMIT_ENABLED) {
            return PHP_INT_MAX;
        }
        
        $maxAttempts = $maxAttempts ?? RATE_LIMIT_REQUESTS;
        $windowSeconds = $windowSeconds ?? RATE_LIMIT_WINDOW;
        
        $ip = getClientIP();
        $windowStart = date('Y-m-d H:i:s', time() - $windowSeconds);
        
        try {
            $record = Database::fetchOne(
                "SELECT attempts FROM rate_limits WHERE ip_address = ? AND action_type = ? AND first_attempt > ?",
                [$ip, $action, $windowStart]
            );
            
            if ($record) {
                return max(0, $maxAttempts - $record['attempts']);
            }
            
            return $maxAttempts;
            
        } catch (Exception $e) {
            return $maxAttempts;
        }
    }
    
    /**
     * IP için kaydı sıfırla
     */
    public static function reset(string $action): bool
    {
        $ip = getClientIP();
        
        try {
            Database::query(
                "DELETE FROM rate_limits WHERE ip_address = ? AND action_type = ?",
                [$ip, $action]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Honeypot kontrolü (bot tespiti)
     */
    public static function checkHoneypot(string $fieldName = 'website_url'): bool
    {
        // Honeypot alanı doluysa bot
        if (!empty($_POST[$fieldName])) {
            logError('Honeypot tetiklendi', ['ip' => getClientIP()]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Form zaman kontrolü (çok hızlı gönderim = bot)
     */
    public static function checkFormTime(int $minSeconds = 3): bool
    {
        $formTime = $_POST['_form_time'] ?? null;
        
        if (!$formTime) {
            return true; // Zaman alanı yoksa kontrol etme
        }
        
        $submitted = (int) base64_decode($formTime);
        $elapsed = time() - $submitted;
        
        if ($elapsed < $minSeconds) {
            logError('Form çok hızlı gönderildi', ['ip' => getClientIP(), 'elapsed' => $elapsed]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Honeypot ve zaman alanlarını oluştur
     */
    public static function formFields(): string
    {
        $time = base64_encode((string) time());
        
        return '
            <div style="position:absolute;left:-9999px;top:-9999px;">
                <input type="text" name="website_url" value="" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="_form_time" value="' . $time . '">
        ';
    }
}