<?php
/**
 * Admin Kimlik Doğrulama Controller
 */

class AuthController
{
    /**
     * Giriş formu
     */
    public function loginForm(): void
    {
        require __DIR__ . '/../views/login.php';
    }
    
    /**
     * Giriş işlemi
     */
    public function login(): void
    {
        // CSRF kontrolü
        if (!CSRF::verifyRequest()) {
            Session::flash('error', 'Güvenlik doğrulaması başarısız.');
            redirect('/panel/giris');
        }
        
        // Rate limiting
        if (!RateLimiter::check('admin_login', 5, 300)) {
            Session::flash('error', 'Çok fazla deneme yaptınız. 5 dakika bekleyin.');
            redirect('/panel/giris');
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            Session::flash('error', 'Kullanıcı adı ve şifre gereklidir.');
            redirect('/panel/giris');
        }
        
        // Kullanıcıyı bul
        $user = Database::fetchOne(
            "SELECT * FROM users WHERE username = ? AND is_active = 1",
            [$username]
        );
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Kullanıcı adı veya şifre hatalı.');
            redirect('/panel/giris');
        }
        
        // Giriş başarılı
        Session::regenerate();
        Session::set('admin_logged_in', true);
        Session::set('admin_user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'must_change_password' => (bool) $user['must_change_password']
        ]);
        
        // Son giriş zamanını güncelle
        Database::query(
            "UPDATE users SET last_login = datetime('now') WHERE id = ?",
            [$user['id']]
        );
        
        // Rate limit sıfırla
        RateLimiter::reset('admin_login');
        
        // Şifre değişikliği gerekiyorsa oraya yönlendir
        if ($user['must_change_password']) {
            Session::flash('warning', 'Güvenliğiniz için lütfen şifrenizi değiştirin.');
            redirect('/panel/sifre-degistir');
        }
        
        Session::flash('success', 'Hoş geldiniz, ' . $user['full_name']);
        redirect('/panel/dashboard');
    }
    
    /**
     * Çıkış
     */
    public function logout(): void
    {
        Session::destroy();
        redirect('/panel/giris');
    }
    
    /**
     * Şifre değiştirme formu
     */
    public function changePasswordForm(): void
    {
        require __DIR__ . '/../views/change-password.php';
    }
    
    /**
     * Şifre değiştirme işlemi
     */
    public function changePassword(): void
    {
        if (!CSRF::verifyRequest()) {
            Session::flash('error', 'Güvenlik doğrulaması başarısız.');
            redirect('/panel/sifre-degistir');
        }
        
        $admin = getAdmin();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Mevcut şifreyi kontrol et (zorunlu değişiklik değilse)
        if (!$admin['must_change_password']) {
            $user = Database::fetchOne("SELECT password_hash FROM users WHERE id = ?", [$admin['id']]);
            if (!password_verify($currentPassword, $user['password_hash'])) {
                Session::flash('error', 'Mevcut şifre hatalı.');
                redirect('/panel/sifre-degistir');
            }
        }
        
        // Yeni şifre kontrolü
        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Yeni şifre en az 6 karakter olmalıdır.');
            redirect('/panel/sifre-degistir');
        }
        
        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Şifreler eşleşmiyor.');
            redirect('/panel/sifre-degistir');
        }
        
        // Şifreyi güncelle
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        Database::query(
            "UPDATE users SET password_hash = ?, must_change_password = 0, updated_at = datetime('now') WHERE id = ?",
            [$hashedPassword, $admin['id']]
        );
        
        // Session güncelle
        $admin['must_change_password'] = false;
        Session::set('admin_user', $admin);
        
        Session::flash('success', 'Şifreniz başarıyla değiştirildi.');
        redirect('/panel/dashboard');
    }
}