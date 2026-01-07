<?php
/**
 * Input Doğrulama Sınıfı
 */

class Validator
{
    private array $errors = [];
    private array $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * Zorunlu alan
     */
    public function required(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->errors[$field] = $message ?? "{$field} alanı zorunludur.";
        }
        
        return $this;
    }
    
    /**
     * E-posta doğrulama
     */
    public function email(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Geçerli bir e-posta adresi giriniz.";
        }
        
        return $this;
    }
    
    /**
     * Telefon doğrulama (Türkiye formatı)
     */
    public function phone(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value)) {
            // Sadece rakamları al
            $digits = preg_replace('/[^0-9]/', '', $value);
            
            // 10 veya 11 haneli olmalı (başında 0 ile veya 0 olmadan)
            if (strlen($digits) < 10 || strlen($digits) > 12) {
                $this->errors[$field] = $message ?? "Geçerli bir telefon numarası giriniz.";
            }
        }
        
        return $this;
    }
    
    /**
     * Minimum uzunluk
     */
    public function minLength(string $field, int $min, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = $message ?? "{$field} en az {$min} karakter olmalıdır.";
        }
        
        return $this;
    }
    
    /**
     * Maksimum uzunluk
     */
    public function maxLength(string $field, int $max, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = $message ?? "{$field} en fazla {$max} karakter olabilir.";
        }
        
        return $this;
    }
    
    /**
     * Sayısal değer
     */
    public function numeric(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = $message ?? "{$field} sayısal bir değer olmalıdır.";
        }
        
        return $this;
    }
    
    /**
     * Slug formatı (ASCII, lowercase, tire)
     */
    public function slug(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            $this->errors[$field] = $message ?? "Slug sadece küçük harf, rakam ve tire içerebilir.";
        }
        
        return $this;
    }
    
    /**
     * URL doğrulama
     */
    public function url(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = $message ?? "Geçerli bir URL giriniz.";
        }
        
        return $this;
    }
    
    /**
     * İzin verilen değerler
     */
    public function in(string $field, array $allowed, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !in_array($value, $allowed)) {
            $this->errors[$field] = $message ?? "Geçersiz seçim.";
        }
        
        return $this;
    }
    
    /**
     * Değer al (nested destekli)
     */
    private function getValue(string $field)
    {
        $keys = explode('.', $field);
        $value = $this->data;
        
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }
        
        return is_string($value) ? trim($value) : $value;
    }
    
    /**
     * Doğrulama başarılı mı?
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Hataları al
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * İlk hatayı al
     */
    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Belirli alanın hatasını al
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Temizlenmiş veriyi al
     */
    public function sanitized(): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
            return $value;
        }, $this->data);
    }
    
    /**
     * Statik kısayol
     */
    public static function make(array $data): self
    {
        return new self($data);
    }
}