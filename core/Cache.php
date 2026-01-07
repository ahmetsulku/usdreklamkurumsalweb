<?php
/**
 * Basit Dosya Tabanlı Cache
 */

class Cache
{
    private static string $cachePath = '';
    
    /**
     * Cache yolunu ayarla
     */
    private static function init(): void
    {
        if (empty(self::$cachePath)) {
            self::$cachePath = CACHE_PATH;
            
            if (!is_dir(self::$cachePath)) {
                mkdir(self::$cachePath, 0755, true);
            }
        }
    }
    
    /**
     * Cache key'den dosya yolu oluştur
     */
    private static function getFilePath(string $key): string
    {
        self::init();
        $hash = md5($key);
        return self::$cachePath . $hash . '.cache';
    }
    
    /**
     * Cache'e yaz
     */
    public static function set(string $key, $value, int $ttl = null): bool
    {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        $ttl = $ttl ?? CACHE_TTL;
        $filePath = self::getFilePath($key);
        
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];
        
        return file_put_contents($filePath, serialize($data), LOCK_EX) !== false;
    }
    
    /**
     * Cache'den oku
     */
    public static function get(string $key, $default = null)
    {
        if (!CACHE_ENABLED) {
            return $default;
        }
        
        $filePath = self::getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filePath));
        
        if ($data === false || !isset($data['expires']) || $data['expires'] < time()) {
            self::delete($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    /**
     * Cache var mı kontrol et
     */
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }
    
    /**
     * Cache sil
     */
    public static function delete(string $key): bool
    {
        $filePath = self::getFilePath($key);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * Tüm cache'i temizle
     */
    public static function clear(): bool
    {
        self::init();
        
        $files = glob(self::$cachePath . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Cache al veya oluştur
     */
    public static function remember(string $key, int $ttl, callable $callback)
    {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Prefix ile cache sil (örn: tüm ürün cache'leri)
     */
    public static function deleteByPrefix(string $prefix): int
    {
        self::init();
        
        $count = 0;
        $files = glob(self::$cachePath . '*.cache');
        
        foreach ($files as $file) {
            $data = @unserialize(file_get_contents($file));
            if ($data && isset($data['key']) && strpos($data['key'], $prefix) === 0) {
                unlink($file);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Expired cache'leri temizle
     */
    public static function gc(): int
    {
        self::init();
        
        $count = 0;
        $files = glob(self::$cachePath . '*.cache');
        
        foreach ($files as $file) {
            $data = @unserialize(file_get_contents($file));
            
            if ($data === false || !isset($data['expires']) || $data['expires'] < time()) {
                unlink($file);
                $count++;
            }
        }
        
        return $count;
    }
}