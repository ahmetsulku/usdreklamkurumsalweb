<?php
/**
 * Statik Sayfa Controller
 */

class PageController
{
    /**
     * Hakkımızda sayfası
     */
    public function about(): void
    {
        $page = Database::fetchOne(
            "SELECT * FROM pages WHERE slug = 'hakkimizda' AND is_active = 1"
        );
        
        if (!$page) {
            $page = [
                'title' => 'Hakkımızda',
                'content' => '<p>Hakkımızda içeriği henüz eklenmedi.</p>',
                'seo_title' => null,
                'meta_description' => null
            ];
        }
        
        $seo = [
            'title' => ($page['seo_title'] ?: $page['title']) . ' - ' . setting('site_name'),
            'description' => $page['meta_description'] ?: 'USD Reklam hakkında bilgi edinin.',
            'canonical' => url('hakkimizda')
        ];
        
        view('layouts.main', [
            'content' => 'pages.about',
            'page' => $page,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Hakkımızda', 'url' => null]
            ]
        ]);
    }
    
    /**
     * İletişim sayfası
     */
    public function contact(): void
    {
        $page = Database::fetchOne(
            "SELECT * FROM pages WHERE slug = 'iletisim' AND is_active = 1"
        );
        
        $seo = [
            'title' => 'İletişim - ' . setting('site_name'),
            'description' => 'Bizimle iletişime geçin. Adres, telefon ve e-posta bilgilerimiz.',
            'canonical' => url('iletisim')
        ];
        
        view('layouts.main', [
            'content' => 'pages.contact',
            'page' => $page,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'İletişim', 'url' => null]
            ]
        ]);
    }
    
    /**
     * İletişim formu gönderimi
     */
    public function contactSubmit(): void
    {
        // CSRF kontrolü
        if (!CSRF::verifyRequest()) {
            flashError('Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.');
            redirect('/iletisim');
        }
        
        // Rate limit kontrolü
        if (!RateLimiter::check('contact_form', 3, 300)) {
            flashError('Çok fazla mesaj gönderdiniz. Lütfen biraz bekleyin.');
            redirect('/iletisim');
        }
        
        // Honeypot ve zaman kontrolü
        if (!RateLimiter::checkHoneypot() || !RateLimiter::checkFormTime()) {
            flashError('Form gönderilemedi.');
            redirect('/iletisim');
        }
        
        // Doğrulama
        $validator = Validator::make($_POST)
            ->required('name', 'Ad Soyad zorunludur.')
            ->required('email', 'E-posta zorunludur.')
            ->email('email', 'Geçerli bir e-posta adresi giriniz.')
            ->required('message', 'Mesaj zorunludur.')
            ->minLength('message', 10, 'Mesaj en az 10 karakter olmalıdır.');
        
        if (!$validator->isValid()) {
            flashError($validator->getFirstError());
            redirect('/iletisim');
        }
        
        $data = $validator->sanitized();
        
        // E-posta gönder
        $mailer = new Mailer();
        
        ob_start();
        ?>
        <h2>Yeni İletişim Mesajı</h2>
        <p><strong>Ad Soyad:</strong> <?= e($data['name']) ?></p>
        <p><strong>E-posta:</strong> <?= e($data['email']) ?></p>
        <p><strong>Telefon:</strong> <?= e($data['phone'] ?? '-') ?></p>
        <p><strong>Mesaj:</strong></p>
        <p><?= nl2br(e($data['message'])) ?></p>
        <hr>
        <p><small>Gönderim tarihi: <?= date('d.m.Y H:i') ?></small></p>
        <?php
        $body = ob_get_clean();
        
        $sent = $mailer->send(
            NOTIFICATION_EMAIL,
            'İletişim Formu - ' . $data['name'],
            $body,
            ['reply_to' => $data['email']]
        );
        
        if ($sent) {
            flashSuccess('Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
        } else {
            flashError('Mesaj gönderilemedi. Lütfen daha sonra tekrar deneyin.');
        }
        
        redirect('/iletisim');
    }
}