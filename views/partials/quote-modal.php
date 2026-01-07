<?php
/**
 * Teklif Al Modal/Popup
 */
?>
<div class="modal" id="quote-modal" aria-hidden="true">
    <div class="modal-overlay" data-modal-close></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Fiyat Teklifi Al</h3>
            <button type="button" class="modal-close" data-modal-close aria-label="Kapat">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Ürün/Hizmet Bilgisi -->
            <div class="quote-item-info" id="quote-item-info">
                <span class="quote-item-name" id="quote-item-name"></span>
            </div>
            
            <!-- Tab Seçimi -->
            <div class="quote-tabs">
                <button type="button" class="quote-tab active" data-tab="call">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    Sizi Arayalım
                </button>
                <button type="button" class="quote-tab" data-tab="email">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    E-posta
                </button>
                <button type="button" class="quote-tab" data-tab="whatsapp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </button>
            </div>
            
            <!-- Tab İçerikleri -->
            <div class="quote-tab-content">
                <!-- Sizi Arayalım Formu -->
                <form class="quote-form" id="quote-form-call" data-type="call">
                    <?= csrfInput() ?>
                    <?= RateLimiter::formFields() ?>
                    <input type="hidden" name="item_type" id="call-item-type">
                    <input type="hidden" name="item_id" id="call-item-id">
                    <input type="hidden" name="item_name" id="call-item-name">
                    
                    <div class="form-group">
                        <label for="call-name">Ad Soyad / Şirket Adı <span class="required">*</span></label>
                        <input type="text" id="call-name" name="name" required placeholder="Adınızı giriniz">
                    </div>
                    
                    <div class="form-group">
                        <label for="call-phone">Telefon Numarası <span class="required">*</span></label>
                        <input type="tel" id="call-phone" name="phone" required placeholder="05XX XXX XX XX">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Aranmak İstiyorum</span>
                        <span class="btn-loading" style="display:none;">Gönderiliyor...</span>
                    </button>
                </form>
                
                <!-- E-posta Formu -->
                <form class="quote-form" id="quote-form-email" data-type="email" style="display:none;">
                    <?= csrfInput() ?>
                    <?= RateLimiter::formFields() ?>
                    <input type="hidden" name="item_type" id="email-item-type">
                    <input type="hidden" name="item_id" id="email-item-id">
                    <input type="hidden" name="item_name" id="email-item-name">
                    
                    <div class="form-group">
                        <label for="email-name">Ad Soyad / Şirket Adı <span class="required">*</span></label>
                        <input type="text" id="email-name" name="name" required placeholder="Adınızı giriniz">
                    </div>
                    
                    <div class="form-group">
                        <label for="email-email">E-posta Adresi <span class="required">*</span></label>
                        <input type="email" id="email-email" name="email" required placeholder="ornek@email.com">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email-quantity">Adet <span class="required">*</span></label>
                            <input type="text" id="email-quantity" name="quantity" required placeholder="Örn: 1000">
                        </div>
                        
                        <div class="form-group">
                            <label for="email-dimensions">Ölçü <span class="required">*</span></label>
                            <input type="text" id="email-dimensions" name="dimensions" required placeholder="Örn: 5x3 cm">
                        </div>
                    </div>
                    
                    <div class="form-group form-checkbox">
                        <label>
                            <input type="checkbox" name="multiple_dimensions" value="1" id="email-multiple">
                            <span>Farklı ölçülerde etiketler istiyorum</span>
                        </label>
                    </div>
                    
                    <div class="form-group" id="email-notes-group" style="display:none;">
                        <label for="email-notes">Ölçü Detayları</label>
                        <textarea id="email-notes" name="notes" rows="3" placeholder="Farklı ölçülerinizi buraya yazabilirsiniz..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Teklif İste</span>
                        <span class="btn-loading" style="display:none;">Gönderiliyor...</span>
                    </button>
                </form>
                
                <!-- WhatsApp Formu -->
                <form class="quote-form" id="quote-form-whatsapp" data-type="whatsapp" style="display:none;">
                    <?= csrfInput() ?>
                    <?= RateLimiter::formFields() ?>
                    <input type="hidden" name="item_type" id="whatsapp-item-type">
                    <input type="hidden" name="item_id" id="whatsapp-item-id">
                    <input type="hidden" name="item_name" id="whatsapp-item-name">
                    
                    <div class="form-group">
                        <label for="whatsapp-name">Ad Soyad / Şirket Adı <span class="required">*</span></label>
                        <input type="text" id="whatsapp-name" name="name" required placeholder="Adınızı giriniz">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="whatsapp-quantity">Adet <span class="required">*</span></label>
                            <input type="text" id="whatsapp-quantity" name="quantity" required placeholder="Örn: 1000">
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp-dimensions">Ölçü <span class="required">*</span></label>
                            <input type="text" id="whatsapp-dimensions" name="dimensions" required placeholder="Örn: 5x3 cm">
                        </div>
                    </div>
                    
                    <div class="form-group form-checkbox">
                        <label>
                            <input type="checkbox" name="multiple_dimensions" value="1" id="whatsapp-multiple">
                            <span>Farklı ölçülerde etiketler istiyorum</span>
                        </label>
                    </div>
                    
                    <div class="form-group" id="whatsapp-notes-group" style="display:none;">
                        <label for="whatsapp-notes">Ölçü Detayları</label>
                        <textarea id="whatsapp-notes" name="notes" rows="3" placeholder="Farklı ölçülerinizi buraya yazabilirsiniz..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-block">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right:8px;">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span class="btn-text">WhatsApp'a Git</span>
                        <span class="btn-loading" style="display:none;">Yönlendiriliyor...</span>
                    </button>
                </form>
            </div>
            
            <!-- Başarı Mesajı -->
            <div class="quote-success" id="quote-success" style="display:none;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <h4>Talebiniz Alındı!</h4>
                <p id="quote-success-message">En kısa sürede size dönüş yapacağız.</p>
                <p class="quote-ref">Referans No: <strong id="quote-ref-no"></strong></p>
                <button type="button" class="btn btn-outline" data-modal-close>Kapat</button>
            </div>
            
            <!-- Önceki Talepler Uyarısı -->
            <div class="quote-existing" id="quote-existing" style="display:none;">
                <p><strong>Not:</strong> Bu iletişim bilgisi ile daha önce teklif talebinde bulunulmuş.</p>
            </div>
        </div>
    </div>
</div>