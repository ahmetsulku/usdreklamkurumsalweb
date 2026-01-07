<?php
/**
 * SMTP E-posta Gönderici
 * PHPMailer olmadan basit SMTP
 */

class Mailer
{
    private string $host = '';
    private int $port = 587;
    private string $username = '';
    private string $password = '';
    private string $encryption = 'tls';
    private string $fromEmail = '';
    private string $fromName = '';
    private string $charset = 'UTF-8';
    
    private array $errors = [];
    
    public function __construct()
    {
        // Ayarları veritabanından al
        $this->host = setting('smtp_host', '');
        $this->port = (int) setting('smtp_port', 587);
        $this->username = setting('smtp_user', '');
        $this->password = setting('smtp_pass', '');
        $this->encryption = setting('smtp_encryption', 'tls');
        $this->fromEmail = setting('smtp_from_email', '');
        $this->fromName = setting('smtp_from_name', 'USD Reklam');
    }
    
    /**
     * E-posta gönder
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $replyTo = $options['reply_to'] ?? null;
        $isHtml = $options['is_html'] ?? true;
        
        // SMTP ayarları yoksa PHP mail() kullan
        if (empty($this->host) || empty($this->username)) {
            return $this->sendWithPhpMail($to, $subject, $body, $replyTo, $isHtml);
        }
        
        return $this->sendWithSmtp($to, $subject, $body, $replyTo, $isHtml);
    }
    
    /**
     * PHP mail() ile gönder
     */
    private function sendWithPhpMail(string $to, string $subject, string $body, ?string $replyTo, bool $isHtml): bool
    {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = $isHtml 
            ? 'Content-type: text/html; charset=' . $this->charset 
            : 'Content-type: text/plain; charset=' . $this->charset;
        $headers[] = 'From: ' . $this->formatAddress($this->fromEmail, $this->fromName);
        
        if ($replyTo) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }
        
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if (!$result) {
            $this->errors[] = 'PHP mail() fonksiyonu ile gönderilemedi.';
            logError('Mail gönderilemedi', ['to' => $to, 'subject' => $subject]);
        }
        
        return $result;
    }
    
    /**
     * SMTP ile gönder
     */
    private function sendWithSmtp(string $to, string $subject, string $body, ?string $replyTo, bool $isHtml): bool
    {
        try {
            // SMTP bağlantısı
            $socket = $this->connect();
            
            if (!$socket) {
                return $this->sendWithPhpMail($to, $subject, $body, $replyTo, $isHtml);
            }
            
            // EHLO
            $this->sendCommand($socket, "EHLO " . gethostname());
            
            // TLS başlat
            if ($this->encryption === 'tls') {
                $this->sendCommand($socket, "STARTTLS");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->sendCommand($socket, "EHLO " . gethostname());
            }
            
            // Kimlik doğrulama
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->sendCommand($socket, base64_encode($this->username));
            $this->sendCommand($socket, base64_encode($this->password));
            
            // Gönderici
            $this->sendCommand($socket, "MAIL FROM:<{$this->fromEmail}>");
            
            // Alıcı
            $this->sendCommand($socket, "RCPT TO:<{$to}>");
            
            // Data
            $this->sendCommand($socket, "DATA");
            
            // Mesaj içeriği
            $message = $this->buildMessage($to, $subject, $body, $replyTo, $isHtml);
            fwrite($socket, $message . "\r\n.\r\n");
            $this->getResponse($socket);
            
            // Kapat
            $this->sendCommand($socket, "QUIT");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            logError('SMTP hatası', ['error' => $e->getMessage()]);
            
            // Fallback olarak PHP mail dene
            return $this->sendWithPhpMail($to, $subject, $body, $replyTo, $isHtml);
        }
    }
    
    /**
     * SMTP'ye bağlan
     */
    private function connect()
    {
        $host = $this->encryption === 'ssl' 
            ? 'ssl://' . $this->host 
            : $this->host;
        
        $socket = @fsockopen($host, $this->port, $errno, $errstr, 30);
        
        if (!$socket) {
            $this->errors[] = "SMTP bağlantı hatası: {$errstr}";
            return false;
        }
        
        $this->getResponse($socket);
        
        return $socket;
    }
    
    /**
     * SMTP komutu gönder
     */
    private function sendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->getResponse($socket);
    }
    
    /**
     * SMTP yanıtı al
     */
    private function getResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
    
    /**
     * Mesaj oluştur
     */
    private function buildMessage(string $to, string $subject, string $body, ?string $replyTo, bool $isHtml): string
    {
        $boundary = md5(time());
        
        $headers = [];
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'From: ' . $this->formatAddress($this->fromEmail, $this->fromName);
        $headers[] = 'To: ' . $to;
        $headers[] = 'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=';
        
        if ($replyTo) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }
        
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: ' . ($isHtml ? 'text/html' : 'text/plain') . '; charset=' . $this->charset;
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = '';
        $headers[] = chunk_split(base64_encode($body));
        
        return implode("\r\n", $headers);
    }
    
    /**
     * Adres formatla
     */
    private function formatAddress(string $email, string $name = ''): string
    {
        if (empty($name)) {
            return $email;
        }
        return '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
    }
    
    /**
     * Hataları al
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Teklif bildirim maili gönder
     */
    public function sendQuoteNotification(array $data): bool
    {
        $to = NOTIFICATION_EMAIL;
        $subject = "Yeni Teklif Talebi - {$data['reference_no']}";
        
        // HTML template yükle
        ob_start();
        extract($data);
        include __DIR__ . '/../views/emails/quote-notification.php';
        $body = ob_get_clean();
        
        return $this->send($to, $subject, $body, [
            'reply_to' => $data['email'] ?? null,
            'is_html' => true
        ]);
    }
}