<?php
/**
 * Admin Ayarlar Controller
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    saveSettings();
} else {
    showSettings();
}

function showSettings() {
    $pageTitle = 'Ayarlar';
    
    // Ayarları grupla
    $settings = Database::fetchAll("SELECT * FROM settings ORDER BY setting_group, setting_key");
    $grouped = [];
    foreach ($settings as $s) {
        $grouped[$s['setting_group']][$s['setting_key']] = $s['setting_value'];
    }
    
    ob_start();
    ?>
    <div class="page-header">
        <h1>Site Ayarları</h1>
        <p>Genel site ayarlarını ve SMTP yapılandırmasını yönetin</p>
    </div>
    
    <form action="" method="POST">
        <?= csrfInput() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <!-- Genel Ayarlar -->
            <div class="card">
                <div class="card-header">
                    <h3>Genel Ayarlar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Site Adı</label>
                        <input type="text" name="settings[site_name]" value="<?= e($grouped['general']['site_name'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Site Sloganı</label>
                        <input type="text" name="settings[site_slogan]" value="<?= e($grouped['general']['site_slogan'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Site Açıklaması</label>
                        <textarea name="settings[site_description]" rows="3"><?= e($grouped['general']['site_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- İletişim Bilgileri -->
            <div class="card">
                <div class="card-header">
                    <h3>İletişim Bilgileri</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>E-posta</label>
                        <input type="email" name="settings[site_email]" value="<?= e($grouped['contact']['site_email'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="text" name="settings[site_phone]" value="<?= e($grouped['contact']['site_phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>WhatsApp Numarası</label>
                        <input type="text" name="settings[site_whatsapp]" value="<?= e($grouped['contact']['site_whatsapp'] ?? '') ?>">
                        <small>Ülke kodu ile birlikte (örn: 905551234567)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Adres</label>
                        <textarea name="settings[site_address]" rows="2"><?= e($grouped['contact']['site_address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Çalışma Saatleri</label>
                        <input type="text" name="settings[working_hours]" value="<?= e($grouped['contact']['working_hours'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <!-- Banka / Fatura -->
            <div class="card">
                <div class="card-header">
                    <h3>Banka & Fatura Bilgileri</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Banka Bilgileri</label>
                        <textarea name="settings[bank_info]" rows="4"><?= e($grouped['contact']['bank_info'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Fatura Bilgileri</label>
                        <textarea name="settings[invoice_info]" rows="4"><?= e($grouped['contact']['invoice_info'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Sosyal Medya -->
            <div class="card">
                <div class="card-header">
                    <h3>Sosyal Medya</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Facebook URL</label>
                        <input type="url" name="settings[facebook_url]" value="<?= e($grouped['social']['facebook_url'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Instagram URL</label>
                        <input type="url" name="settings[instagram_url]" value="<?= e($grouped['social']['instagram_url'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Twitter URL</label>
                        <input type="url" name="settings[twitter_url]" value="<?= e($grouped['social']['twitter_url'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>LinkedIn URL</label>
                        <input type="url" name="settings[linkedin_url]" value="<?= e($grouped['social']['linkedin_url'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <!-- SMTP Ayarları -->
            <div class="card">
                <div class="card-header">
                    <h3>SMTP E-posta Ayarları</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" name="settings[smtp_host]" value="<?= e($grouped['smtp']['smtp_host'] ?? '') ?>">
                            <small>örn: smtp.gmail.com</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="text" name="settings[smtp_port]" value="<?= e($grouped['smtp']['smtp_port'] ?? '587') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>SMTP Kullanıcı</label>
                            <input type="text" name="settings[smtp_user]" value="<?= e($grouped['smtp']['smtp_user'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Şifre</label>
                            <input type="password" name="settings[smtp_pass]" value="<?= e($grouped['smtp']['smtp_pass'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Gönderen Adı</label>
                            <input type="text" name="settings[smtp_from_name]" value="<?= e($grouped['smtp']['smtp_from_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Gönderen E-posta</label>
                            <input type="email" name="settings[smtp_from_email]" value="<?= e($grouped['smtp']['smtp_from_email'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Şifreleme</label>
                        <select name="settings[smtp_encryption]">
                            <option value="tls" <?= ($grouped['smtp']['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($grouped['smtp']['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="" <?= empty($grouped['smtp']['smtp_encryption']) ? 'selected' : '' ?>>Yok</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="card">
                <div class="card-header">
                    <h3>SEO & Analytics</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Google Analytics ID</label>
                        <input type="text" name="settings[google_analytics]" value="<?= e($grouped['seo']['google_analytics'] ?? '') ?>">
                        <small>örn: G-XXXXXXXXXX</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-primary btn-lg">Ayarları Kaydet</button>
        </div>
    </form>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function saveSettings() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/ayarlar');
    }
    
    $settings = $_POST['settings'] ?? [];
    
    // Ayar grupları
    $groups = [
        'site_name' => 'general',
        'site_slogan' => 'general',
        'site_description' => 'general',
        'site_email' => 'contact',
        'site_phone' => 'contact',
        'site_whatsapp' => 'contact',
        'site_address' => 'contact',
        'working_hours' => 'contact',
        'bank_info' => 'contact',
        'invoice_info' => 'contact',
        'facebook_url' => 'social',
        'instagram_url' => 'social',
        'twitter_url' => 'social',
        'linkedin_url' => 'social',
        'smtp_host' => 'smtp',
        'smtp_port' => 'smtp',
        'smtp_user' => 'smtp',
        'smtp_pass' => 'smtp',
        'smtp_from_name' => 'smtp',
        'smtp_from_email' => 'smtp',
        'smtp_encryption' => 'smtp',
        'google_analytics' => 'seo',
    ];
    
    foreach ($settings as $key => $value) {
        $group = $groups[$key] ?? 'general';
        
        // Var mı kontrol et
        $existing = Database::fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        
        if ($existing) {
            Database::query(
                "UPDATE settings SET setting_value = ?, updated_at = datetime('now') WHERE setting_key = ?",
                [$value, $key]
            );
        } else {
            Database::insert('settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group
            ]);
        }
    }
    
    // Cache temizle
    Cache::clear();
    
    Session::flash('success', 'Ayarlar kaydedildi.');
    redirect('/panel/ayarlar');
}