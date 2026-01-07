<?php
/**
 * Ürün Kategori Sayfası
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title"><?= e($category['name']) ?></h1>
            <?php if ($category['description']): ?>
            <p class="section-subtitle"><?= e(truncate(strip_tags($category['description']), 150)) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($subcategories)): ?>
        <!-- Alt Kategoriler -->
        <div style="margin-bottom: 40px;">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px;">Alt Kategoriler</h2>
            <div class="grid grid-4">
                <?php foreach ($subcategories as $subcat): ?>
                <a href="/<?= e($subcat['slug']) ?>" class="card" style="text-decoration: none;">
                    <div class="card-image" style="aspect-ratio: 3/2;">
                        <?php if ($subcat['image']): ?>
                        <img src="<?= asset($subcat['image']) ?>" alt="<?= e($subcat['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($subcat['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="card-title" style="font-size: 15px; margin-bottom: 0;"><?= e($subcat['name']) ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($products)): ?>
        <!-- Ürünler -->
        <div>
            <?php if (!empty($subcategories)): ?>
            <h2 style="font-size: 1.25rem; margin-bottom: 20px;">Tüm Ürünler</h2>
            <?php endif; ?>
            
            <div class="grid grid-4">
                <?php foreach ($products as $product): ?>
                <article class="card product-card">
                    <a href="/<?= e($product['category_slug'] ?? $category['slug']) ?>/<?= e($product['slug']) ?>" class="card-image">
                        <?php if ($product['main_image']): ?>
                        <img src="<?= asset($product['main_image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($product['name']) ?>">
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="/<?= e($product['category_slug'] ?? $category['slug']) ?>/<?= e($product['slug']) ?>">
                                <?= e($product['name']) ?>
                            </a>
                        </h3>
                        <?php if ($product['short_description']): ?>
                        <p class="card-text"><?= e(truncate($product['short_description'], 80)) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="/<?= e($product['category_slug'] ?? $category['slug']) ?>/<?= e($product['slug']) ?>" class="btn btn-sm btn-outline">Detay</a>
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
        <?php elseif (empty($subcategories)): ?>
        <p class="text-center" style="color: var(--text-light); padding: 40px 0;">
            Bu kategoride henüz ürün bulunmuyor.
        </p>
        <?php endif; ?>
    </div>
</section>