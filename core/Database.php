<?php
/**
 * Veritabanı Bağlantı Sınıfı (PDO)
 * SQLite ve MySQL destekler
 */

class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Singleton pattern ile PDO bağlantısı
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                if (DB_DRIVER === 'sqlite') {
                    // SQLite bağlantısı
                    $dbPath = DB_SQLITE_PATH;
                    $dbDir = dirname($dbPath);
                    
                    // Klasör yoksa oluştur
                    if (!is_dir($dbDir)) {
                        mkdir($dbDir, 0755, true);
                    }
                    
                    self::$instance = new PDO(
                        "sqlite:{$dbPath}",
                        null,
                        null,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                        ]
                    );
                    
                    // SQLite optimizasyonları
                    self::$instance->exec('PRAGMA journal_mode=WAL');
                    self::$instance->exec('PRAGMA foreign_keys=ON');
                    
                } else {
                    // MySQL bağlantısı
                    $dsn = sprintf(
                        "mysql:host=%s;dbname=%s;charset=%s",
                        DB_HOST,
                        DB_NAME,
                        DB_CHARSET
                    );
                    
                    self::$instance = new PDO(
                        $dsn,
                        DB_USER,
                        DB_PASS,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                        ]
                    );
                }
                
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die("Veritabanı bağlantı hatası: " . $e->getMessage());
                }
                die("Veritabanı bağlantısı kurulamadı.");
            }
        }
        
        return self::$instance;
    }
    
    /**
 * Veritabanını başlat (ilk kurulum)
 */
public static function install(): bool
{
    $migrationFile = __DIR__ . '/../migrations/001_initial.sql';
    
    if (!file_exists($migrationFile)) {
        if (DEBUG_MODE) {
            die("Migration dosyası bulunamadı: " . $migrationFile);
        }
        return false;
    }
    
    try {
        $pdo = self::getInstance();
        $sql = file_get_contents($migrationFile);
        
        // Yorumları temizle
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // SQL komutlarını ayır
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && strlen($statement) > 5) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Tablo zaten varsa devam et
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        if (DEBUG_MODE) {
                            echo "SQL Hatası: " . $e->getMessage() . "<br>";
                            echo "Statement: " . substr($statement, 0, 100) . "...<br><br>";
                        }
                    }
                }
            }
        }
        
        // Admin şifresini hashle
        $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
        $stmt->execute([$hashedPassword]);
        
        return true;
        
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("Kurulum hatası: " . $e->getMessage());
        }
        return false;
    }
}
    
    /**
     * Veritabanı kurulu mu kontrol et
     */
    public static function isInstalled(): bool
    {
        try {
            $pdo = self::getInstance();
            $result = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
            return $result > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Basit query helper
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Tek satır getir
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    /**
     * Tüm satırları getir
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    /**
     * Insert ve son ID'yi döndür
     */
    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        self::query($sql, array_values($data));
        
        return (int) self::getInstance()->lastInsertId();
    }
    
    /**
     * Update helper
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setParts = array_map(fn($col) => "{$col} = ?", array_keys($data));
        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";
        
        $params = array_merge(array_values($data), $whereParams);
        return self::query($sql, $params)->rowCount();
    }
    
    /**
     * Delete helper
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return self::query($sql, $params)->rowCount();
    }
}