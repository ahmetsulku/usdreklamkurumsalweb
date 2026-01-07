<?php
/**
 * Hizmet Detay Sayfası
 */
?>

<section class="section">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Sol: Görsel -->
            <div class="product-gallery">
                <div class="product-main-image" style="aspect-ratio: 16/10;">
                    <?php if ($service['main_image']): ?>
                    <img src="<?= asset($service['main_image']) ?>" alt="<?= e($service['name']) ?>" style="object-fit: cover; width: 100%; height: 100%;">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($service['name']) ?>" style="object-fit: cover; width: 100%; height: 100%;">
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($gallery)): ?>
                <div class="product-thumbnails" style="margin-top: 16px;">
                    <?php foreach ($gallery as $image): ?>
                    <div class="product-thumb">
                        <img src="<?= asset($image['image_path']) ?>" alt="<?= e($image['alt_text'] ?: $service['name']) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sağ: Bilgiler -->
            <div class="product-info">
                <h1><?= e($service['name']) ?></h1>
                
                <?php if ($service['short_description']): ?>
                <p class="product-short-desc"><?= e($service['short_description']) ?></p>
                <?php endif; ?>
                
                <div class="product-actions">
                    <button type="button" class="btn btn-primary btn-lg" 
                            data-quote-modal
                            data-item-type="service"
                            data-item-id="<?= $service['id'] ?>"
                            data-item-name="<?= e($service['name']) ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        Fiyat Teklifi Al
                    </button>
                    
                    <?php if ($phone = setting('site_phone')): ?>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phone) ?>" class="btn btn-outline btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        Arayın
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($whatsapp = setting('site_whatsapp')): ?>
                    <a href="<?= whatsappLink($whatsapp, $service['name'] . ' hizmeti hakkında bilgi almak istiyorum.') ?>" 
                       target="_blank" 
                       class="btn btn-lg" 
                       style="background: #25d366; color: #fff;">
                        WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
                
                <?php if ($service['full_description']): ?>
                <div class="product-full-desc">
                    <h3>Hizmet Detayları</h3>
                    <div class="content">
                        <?= $service['full_description'] ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Diğer Hizmetler -->
        <?php if (!empty($otherServices)): ?>
        <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border);">
            <h3 style="margin-bottom: 25px;">Diğer Hizmetlerimiz</h3>
            <div class="grid grid-3">
                <?php foreach ($otherServices as $other): ?>
                <a href="/hizmetler/<?= e($other['slug']) ?>" class="card" style="text-decoration: none;">
                    <div class="card-image">
                        <?php if ($other['main_image']): ?>
                        <img src="<?= asset($other['main_image']) ?>" alt="<?= e($other['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($other['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title"><?= e($other['name']) ?></h4>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>