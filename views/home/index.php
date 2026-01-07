<?php
/**
 * Ana Sayfa
 */
?>

<!-- Hero Slider -->
<?php if (!empty($sliders)): ?>
<section class="hero-slider">
    <div class="slider-container">
        <?php foreach ($sliders as $slide): ?>
        <div class="slide">
            <div class="slide-overlay"></div>
            <picture>
                <?php if ($slide['image_mobile']): ?>
                <source media="(max-width: 768px)" srcset="<?= asset($slide['image_mobile']) ?>">
                <?php endif; ?>
                <img src="<?= asset($slide['image_desktop']) ?>" alt="<?= e($slide['title']) ?>" class="slide-image">
            </picture>
            <div class="slide-content">
                <?php if ($slide['title']): ?>
                <h2 class="slide-title"><?= e($slide['title']) ?></h2>
                <?php endif; ?>
                <?php if ($slide['subtitle']): ?>
                <p class="slide-subtitle"><?= e($slide['subtitle']) ?></p>
                <?php endif; ?>
                <?php if ($slide['button_text'] && $slide['button_url']): ?>
                <a href="<?= e($slide['button_url']) ?>" class="btn btn-primary btn-lg"><?= e($slide['button_text']) ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (count($sliders) > 1): ?>
    <button class="slider-nav slider-prev" aria-label="Önceki">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
    <button class="slider-nav slider-next" aria-label="Sonraki">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>
    <div class="slider-dots"></div>
    <?php endif; ?>
</section>
<?php else: ?>
<!-- Slider yoksa basit hero -->
<section class="hero-simple" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 15px;"><?= e(setting('site_name')) ?></h1>
        <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 25px;"><?= e(setting('site_slogan')) ?></p>
        <a href="/iletisim" class="btn btn-white btn-lg">Teklif Al</a>
    </div>
</section>
<?php endif; ?>

<!-- Öne Çıkan Ürünler -->
<?php if (!empty($featuredProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Öne Çıkan Ürünlerimiz</h2>
            <p class="section-subtitle">En çok tercih edilen ürünlerimiz</p>
        </div>
        
        <div class="grid grid-4">
            <?php foreach ($featuredProducts as $product): ?>
            <article class="card product-card">
                <a href="/<?= e($product['category_slug']) ?>/<?= e($product['slug']) ?>" class="card-image">
                    <?php if ($product['main_image']): ?>
                    <img src="<?= asset($product['main_image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($product['name']) ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="/<?= e($product['category_slug']) ?>/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                    </h3>
                    <?php if ($product['short_description']): ?>
                    <p class="card-text"><?= e(truncate($product['short_description'], 80)) ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/<?= e($product['category_slug']) ?>/<?= e($product['slug']) ?>" class="btn btn-sm btn-outline">Detay</a>
                    <button type="button" class="btn btn-sm btn-primary" 
                            data-quote-modal
                            data-item-type="product"
                            data-item-id="<?= $product['id'] ?>"
                            data-item-name="<?= e($product['name']) ?>">
                        Teklif Al
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Kategoriler ve Ürünler -->
<?php if (!empty($categories)): ?>
<?php foreach ($categories as $category): ?>
<section class="section <?= $loop ?? '' ?>" style="background-color: <?= ($category['sort_order'] % 2 == 0) ? 'var(--bg-alt)' : '#fff' ?>;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= e($category['name']) ?></h2>
            <?php if ($category['description']): ?>
            <p class="section-subtitle"><?= e(truncate(strip_tags($category['description']), 120)) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($category['subcategories'])): ?>
        <!-- Alt kategoriler varsa onları göster -->
        <div class="grid grid-3">
            <?php foreach ($category['subcategories'] as $subcat): ?>
            <a href="/<?= e($subcat['slug']) ?>" class="card" style="text-decoration: none;">
                <div class="card-image">
                    <?php if ($subcat['image']): ?>
                    <img src="<?= asset($subcat['image']) ?>" alt="<?= e($subcat['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($subcat['name']) ?>">
                    <?php endif; ?>
                </div>
                <div class="card-body text-center">
                    <h3 class="card-title" style="margin-bottom: 0;"><?= e($subcat['name']) ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php elseif (!empty($category['products'])): ?>
        <!-- Alt kategori yoksa direkt ürünleri göster -->
        <div class="grid grid-3">
            <?php foreach ($category['products'] as $product): ?>
            <article class="card product-card">
                <a href="/<?= e($category['slug']) ?>/<?= e($product['slug']) ?>" class="card-image">
                    <?php if ($product['main_image']): ?>
                    <img src="<?= asset($product['main_image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($product['name']) ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="/<?= e($category['slug']) ?>/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                    </h3>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="/<?= e($category['slug']) ?>" class="btn btn-outline">Tümünü Gör</a>
        </div>
    </div>
</section>
<?php endforeach; ?>
<?php endif; ?>

<!-- Hizmetler -->
<?php if (!empty($services)): ?>
<section class="section" style="background: linear-gradient(135deg, var(--bg-dark) 0%, #1e3a5f 100%); color: #fff;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title" style="color: #fff;">Hizmetlerimiz</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Profesyonel baskı ve üretim hizmetleri</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($services as $service): ?>
            <a href="/hizmetler/<?= e($service['slug']) ?>" class="card" style="text-decoration: none;">
                <div class="card-image">
                    <?php if ($service['main_image']): ?>
                    <img src="<?= asset($service['main_image']) ?>" alt="<?= e($service['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($service['name']) ?>">
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= e($service['name']) ?></h3>
                    <?php if ($service['short_description']): ?>
                    <p class="card-text"><?= e(truncate($service['short_description'], 100)) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-3">
            <a href="/hizmetler" class="btn btn-white">Tüm Hizmetler</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Müşteri Yorumları -->
<?php if (!empty($reviews)): ?>
<section class="section reviews-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Müşteri Yorumları</h2>
            <p class="section-subtitle">Müşterilerimizin bizim hakkımızda düşünceleri</p>
        </div>
        
        <div class="reviews-grid">
            <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <div class="review-rating">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= $i < $review['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <?php endfor; ?>
                </div>
                <p class="review-text">"<?= e($review['review_text']) ?>"</p>
                <div class="review-author">
                    <div class="review-avatar">
                        <?= strtoupper(mb_substr($review['customer_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="review-name"><?= e($review['customer_name']) ?></div>
                        <?php if ($review['company_name']): ?>
                        <div class="review-company"><?= e($review['company_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Son Blog Yazıları -->
<?php if (!empty($latestPosts)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Blog</h2>
            <p class="section-subtitle">Sektörden haberler ve bilgiler</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($latestPosts as $post): ?>
            <article class="card blog-card">
                <a href="/blog/<?= e($post['slug']) ?>" class="card-image">
                    <?php if ($post['featured_image']): ?>
                    <img src="<?= asset($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($post['title']) ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <div class="blog-meta">
                        <span><?= formatDateTurkish($post['published_at']) ?></span>
                        <?php if ($post['category_name']): ?>
                        <span><?= e($post['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="card-title">
                        <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                    </h3>
                    <?php if ($post['excerpt']): ?>
                    <p class="card-text"><?= e(truncate($post['excerpt'], 100)) ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-3">
            <a href="/blog" class="btn btn-outline">Tüm Yazılar</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section style="background: var(--primary); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 15px; color: #fff;">Projeniz İçin Teklif Alın</h2>
        <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Etiket, plakat, madalyon ve dijital baskı ihtiyaçlarınız için hemen bizimle iletişime geçin.
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/iletisim" class="btn btn-white btn-lg">İletişime Geç</a>
            <?php if ($whatsapp = setting('site_whatsapp')): ?>
            <a href="<?= whatsappLink($whatsapp, 'Merhaba, teklif almak istiyorum.') ?>" target="_blank" class="btn btn-lg" style="background: #25d366; color: #fff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>