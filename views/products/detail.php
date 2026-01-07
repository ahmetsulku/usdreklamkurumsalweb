<?php
/**
 * Ürün Detay Sayfası
 */
?>

<section class="product-detail">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Sol: Görseller -->
            <div class="product-gallery">
                <div class="product-main-image">
                    <?php if ($product['main_image']): ?>
                    <img src="<?= asset($product['main_image']) ?>" alt="<?= e($product['name']) ?>" id="main-product-image">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($product['name']) ?>" id="main-product-image">
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($gallery)): ?>
                <div class="product-thumbnails">
                    <?php if ($product['main_image']): ?>
                    <div class="product-thumb active">
                        <img src="<?= asset($product['main_image']) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                    <?php endif; ?>
                    <?php foreach ($gallery as $image): ?>
                    <div class="product-thumb">
                        <img src="<?= asset($image['image_path']) ?>" alt="<?= e($image['alt_text'] ?: $product['name']) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sağ: Bilgiler -->
            <div class="product-info">
                <h1><?= e($product['name']) ?></h1>
                
                <?php if ($product['short_description']): ?>
                <p class="product-short-desc"><?= e($product['short_description']) ?></p>
                <?php endif; ?>
                
                <div class="product-actions">
                    <button type="button" class="btn btn-primary btn-lg" 
                            data-quote-modal
                            data-item-type="product"
                            data-item-id="<?= $product['id'] ?>"
                            data-item-name="<?= e($product['name']) ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
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
                </div>
                
                <?php if ($product['full_description']): ?>
                <div class="product-full-desc">
                    <h3>Ürün Detayları</h3>
                    <div class="content">
                        <?= $product['full_description'] ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Yaptığımız İşler Galerisi -->
        <?php 
        $workGallery = array_filter($gallery ?? [], fn($img) => $img['is_gallery']);
        if (!empty($workGallery)): 
        ?>
        <div class="work-gallery">
            <h3>Yaptığımız İşlerden Örnekler</h3>
            <div class="work-gallery-grid">
                <?php foreach ($workGallery as $image): ?>
                <div class="work-gallery-item">
                    <img src="<?= asset($image['image_path']) ?>" alt="<?= e($image['alt_text'] ?: $product['name'] . ' örnek çalışma') ?>" loading="lazy">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- İlgili Ürünler -->
        <?php if (!empty($relatedProducts)): ?>
        <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border);">
            <h3 style="margin-bottom: 25px;">İlgili Ürünler</h3>
            <div class="grid grid-4">
                <?php foreach ($relatedProducts as $related): ?>
                <article class="card product-card">
                    <a href="/<?= e($related['category_slug'] ?? $category['slug']) ?>/<?= e($related['slug']) ?>" class="card-image">
                        <?php if ($related['main_image']): ?>
                        <img src="<?= asset($related['main_image']) ?>" alt="<?= e($related['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($related['name']) ?>">
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <h4 class="card-title">
                            <a href="/<?= e($related['category_slug'] ?? $category['slug']) ?>/<?= e($related['slug']) ?>">
                                <?= e($related['name']) ?>
                            </a>
                        </h4>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>