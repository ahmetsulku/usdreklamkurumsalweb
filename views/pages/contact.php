<?php
/**
 * İletişim Sayfası
 */
?>

<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">İletişim</h1>
            <p class="section-subtitle">Bizimle iletişime geçin, size yardımcı olalım</p>
        </div>
        
        <div class="contact-grid">
            <!-- Sol: İletişim Bilgileri -->
            <div>
                <div class="contact-info-card">
                    <h3 style="margin-bottom: 25px;">İletişim Bilgileri</h3>
                    
                    <?php if ($address = setting('site_address')): ?>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h4>Adres</h4>
                            <p><?= nl2br(e($address)) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($phone = setting('site_phone')): ?>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div>
                            <h4>Telefon</h4>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phone) ?>"><?= e($phone) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($email = setting('site_email')): ?>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div>
                            <h4>E-posta</h4>
                            <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($hours = setting('working_hours')): ?>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <h4>Çalışma Saatleri</h4>
                            <p><?= e($hours) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($whatsapp = setting('site_whatsapp')): ?>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                        <a href="<?= whatsappLink($whatsapp, 'Merhaba, bilgi almak istiyorum.') ?>" 
                           target="_blank" 
                           class="btn btn-block" 
                           style="background: #25d366; color: #fff;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp ile Yazın
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Banka / Fatura Bilgileri -->
                <?php if ($bankInfo = setting('bank_info')): ?>
                <div class="bank-info">
                    <h4>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Banka Bilgileri
                    </h4>
                    <p><?= nl2br(e($bankInfo)) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($invoiceInfo = setting('invoice_info')): ?>
                <div class="bank-info" style="margin-top: 15px;">
                    <h4>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        Fatura Bilgileri
                    </h4>
                    <p><?= nl2br(e($invoiceInfo)) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sağ: İletişim Formu -->
            <div class="contact-form-wrapper">
                <h3>Bize Yazın</h3>
                <p style="color: var(--text-light); margin-bottom: 25px;">Sorularınız veya talepleriniz için formu doldurun, en kısa sürede dönüş yapalım.</p>
                
                <form action="/iletisim" method="POST">
                    <?= csrfInput() ?>
                    <?= RateLimiter::formFields() ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Ad Soyad <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required placeholder="Adınızı giriniz">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-posta <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="ornek@email.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" name="phone" placeholder="05XX XXX XX XX">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Konu</label>
                        <select id="subject" name="subject">
                            <option value="">Konu seçiniz</option>
                            <option value="Fiyat Teklifi">Fiyat Teklifi</option>
                            <option value="Sipariş Takibi">Sipariş Takibi</option>
                            <option value="Teknik Destek">Teknik Destek</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mesajınız <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="5" required placeholder="Mesajınızı buraya yazın..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        Mesaj Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>