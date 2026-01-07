<?php
/**
 * Tüm Ürünler Sayfası
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Ürünlerimiz</h1>
            <p class="section-subtitle">Tüm ürün kategorilerimizi keşfedin</p>
        </div>
        
        <?php if (!empty($categories)): ?>
        <div class="grid grid-3">
            <?php foreach ($categories as $category): ?>
            <div class="card">
                <a href="/<?= e($category['slug']) ?>" class="card-image">
                    <?php if ($category['image']): ?>
                    <img src="<?= asset($category['image']) ?>" alt="<?= e($category['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($category['name']) ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h2 class="card-title">
                        <a href="/<?= e($category['slug']) ?>"><?= e($category['name']) ?></a>
                    </h2>
                    <?php if ($category['description']): ?>
                    <p class="card-text"><?= e(truncate(strip_tags($category['description']), 100)) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($category['subcategories'])): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-light);">
                        <p style="font-size: 13px; color: var(--text-light); margin-bottom: 8px;">Alt Kategoriler:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <?php foreach ($category['subcategories'] as $sub): ?>
                            <a href="/<?= e($sub['slug']) ?>" 
                               style="font-size: 12px; padding: 4px 10px; background: var(--bg-alt); border-radius: 20px; color: var(--text);">
                                <?= e($sub['name']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <span style="font-size: 13px; color: var(--text-light);">
                        <?= $category['product_count'] ?? 0 ?> ürün
                    </span>
                    <a href="/<?= e($category['slug']) ?>" class="btn btn-sm btn-primary">İncele</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center" style="color: var(--text-light);">Henüz ürün kategorisi eklenmemiş.</p>
        <?php endif; ?>
    </div>
</section>