<?php
/**
 * Teklif Talebi Controller (AJAX)
 */

class QuoteController
{
    /**
     * Telefon ile arayın talebi
     */
    public function submitCall(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        // CSRF kontrolü
        if (!CSRF::verifyRequest()) {
            jsonResponse(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız.'], 403);
        }
        
        // Rate limit
        if (!RateLimiter::check('quote_call', 5, 300)) {
            jsonResponse(['success' => false, 'message' => 'Çok fazla talep gönderdiniz. Lütfen bekleyin.'], 429);
        }
        
        // Honeypot
        if (!RateLimiter::checkHoneypot()) {
            jsonResponse(['success' => false, 'message' => 'Form gönderilemedi.'], 400);
        }
        
        // Doğrulama
        $validator = Validator::make($_POST)
            ->required('name', 'Ad Soyad veya Şirket Adı zorunludur.')
            ->required('phone', 'Telefon numarası zorunludur.')
            ->phone('phone', 'Geçerli bir telefon numarası giriniz.');
        
        if (!$validator->isValid()) {
            jsonResponse(['success' => false, 'message' => $validator->getFirstError()], 422);
        }
        
        $data = $validator->sanitized();
        $refNo = generateRefNo('ARA');
        
        // Veritabanına kaydet
        $id = Database::insert('quote_requests', [
            'reference_no' => $refNo,
            'request_type' => 'call',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'item_type' => $data['item_type'] ?? null,
            'item_id' => $data['item_id'] ?? null,
            'item_name' => $data['item_name'] ?? null,
            'ip_address' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // E-posta gönder
        $mailer = new Mailer();
        $mailer->sendQuoteNotification([
            'reference_no' => $refNo,
            'request_type' => 'Telefon ile Arayın',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'item_name' => $data['item_name'] ?? '-',
            'date' => date('d.m.Y H:i')
        ]);
        
        // Daha önce talep var mı kontrol et
        $existing = $this->checkExistingByPhone($data['phone']);
        
        jsonResponse([
            'success' => true,
            'message' => 'Talebiniz alındı. En kısa sürede sizi arayacağız.',
            'reference_no' => $refNo,
            'existing_requests' => $existing
        ]);
    }
    
    /**
     * E-posta ile teklif talebi
     */
    public function submitEmail(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!CSRF::verifyRequest()) {
            jsonResponse(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız.'], 403);
        }
        
        if (!RateLimiter::check('quote_email', 5, 300)) {
            jsonResponse(['success' => false, 'message' => 'Çok fazla talep gönderdiniz.'], 429);
        }
        
        if (!RateLimiter::checkHoneypot()) {
            jsonResponse(['success' => false, 'message' => 'Form gönderilemedi.'], 400);
        }
        
        $validator = Validator::make($_POST)
            ->required('name', 'Ad Soyad veya Şirket Adı zorunludur.')
            ->required('email', 'E-posta zorunludur.')
            ->email('email', 'Geçerli bir e-posta adresi giriniz.')
            ->required('quantity', 'Adet bilgisi zorunludur.')
            ->required('dimensions', 'Ölçü bilgisi zorunludur.');
        
        if (!$validator->isValid()) {
            jsonResponse(['success' => false, 'message' => $validator->getFirstError()], 422);
        }
        
        $data = $validator->sanitized();
        $refNo = generateRefNo('MAI');
        $hasMultipleDimensions = isset($_POST['multiple_dimensions']) && $_POST['multiple_dimensions'] === '1';
        
        Database::insert('quote_requests', [
            'reference_no' => $refNo,
            'request_type' => 'email',
            'name' => $data['name'],
            'email' => $data['email'],
            'item_type' => $data['item_type'] ?? null,
            'item_id' => $data['item_id'] ?? null,
            'item_name' => $data['item_name'] ?? null,
            'quantity' => $data['quantity'],
            'dimensions' => $data['dimensions'],
            'has_multiple_dimensions' => $hasMultipleDimensions ? 1 : 0,
            'additional_notes' => $data['notes'] ?? null,
            'ip_address' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // E-posta gönder
        $mailer = new Mailer();
        $mailer->sendQuoteNotification([
            'reference_no' => $refNo,
            'request_type' => 'E-posta ile Teklif',
            'name' => $data['name'],
            'email' => $data['email'],
            'item_name' => $data['item_name'] ?? '-',
            'quantity' => $data['quantity'],
            'dimensions' => $data['dimensions'],
            'has_multiple_dimensions' => $hasMultipleDimensions,
            'notes' => $data['notes'] ?? null,
            'date' => date('d.m.Y H:i')
        ]);
        
        $existing = $this->checkExistingByEmail($data['email']);
        
        jsonResponse([
            'success' => true,
            'message' => 'Teklif talebiniz alındı. E-posta adresinize en kısa sürede dönüş yapacağız.',
            'reference_no' => $refNo,
            'existing_requests' => $existing
        ]);
    }
    
    /**
     * WhatsApp ile teklif talebi
     */
    public function submitWhatsapp(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!CSRF::verifyRequest()) {
            jsonResponse(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız.'], 403);
        }
        
        if (!RateLimiter::check('quote_whatsapp', 10, 300)) {
            jsonResponse(['success' => false, 'message' => 'Çok fazla talep gönderdiniz.'], 429);
        }
        
        $validator = Validator::make($_POST)
            ->required('name', 'Ad Soyad veya Şirket Adı zorunludur.')
            ->required('quantity', 'Adet bilgisi zorunludur.')
            ->required('dimensions', 'Ölçü bilgisi zorunludur.');
        
        if (!$validator->isValid()) {
            jsonResponse(['success' => false, 'message' => $validator->getFirstError()], 422);
        }
        
        $data = $validator->sanitized();
        $refNo = generateRefNo('WHA');
        $hasMultipleDimensions = isset($_POST['multiple_dimensions']) && $_POST['multiple_dimensions'] === '1';
        
        Database::insert('quote_requests', [
            'reference_no' => $refNo,
            'request_type' => 'whatsapp',
            'name' => $data['name'],
            'item_type' => $data['item_type'] ?? null,
            'item_id' => $data['item_id'] ?? null,
            'item_name' => $data['item_name'] ?? null,
            'quantity' => $data['quantity'],
            'dimensions' => $data['dimensions'],
            'has_multiple_dimensions' => $hasMultipleDimensions ? 1 : 0,
            'ip_address' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // WhatsApp mesaj metni
        $message = "Merhaba, teklif almak istiyorum.\n\n";
        $message .= "Ad/Firma: {$data['name']}\n";
        $message .= "Ürün: " . ($data['item_name'] ?? '-') . "\n";
        $message .= "Adet: {$data['quantity']}\n";
        $message .= "Ölçü: {$data['dimensions']}\n";
        $message .= "Referans: {$refNo}";
        
        $whatsappUrl = whatsappLink(WHATSAPP_NUMBER, $message);
        
        jsonResponse([
            'success' => true,
            'message' => 'WhatsApp\'a yönlendiriliyorsunuz...',
            'reference_no' => $refNo,
            'whatsapp_url' => $whatsappUrl
        ]);
    }
    
    /**
     * Mevcut talepleri kontrol et (AJAX)
     */
    public function checkExisting(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $email = $_GET['email'] ?? null;
        $phone = $_GET['phone'] ?? null;
        
        $existing = [];
        
        if ($email) {
            $existing = array_merge($existing, $this->checkExistingByEmail($email));
        }
        
        if ($phone) {
            $existing = array_merge($existing, $this->checkExistingByPhone($phone));
        }
        
        jsonResponse([
            'success' => true,
            'existing_requests' => $existing
        ]);
    }
    
    /**
     * E-posta ile önceki talepler
     */
    private function checkExistingByEmail(string $email): array
    {
        return Database::fetchAll(
            "SELECT reference_no, item_name, created_at FROM quote_requests 
             WHERE email = ? ORDER BY created_at DESC LIMIT 5",
            [$email]
        );
    }
    
    /**
     * Telefon ile önceki talepler
     */
    private function checkExistingByPhone(string $phone): array
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $digits = substr($digits, -10); // Son 10 hane
        
        return Database::fetchAll(
            "SELECT reference_no, item_name, created_at FROM quote_requests 
             WHERE phone LIKE ? ORDER BY created_at DESC LIMIT 5",
            ['%' . $digits]
        );
    }
}