<?php
/**
 * Hizmetler Listesi
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Hizmetlerimiz</h1>
            <p class="section-subtitle">Profesyonel baskı ve üretim hizmetlerimizi keşfedin</p>
        </div>
        
        <?php if (!empty($services)): ?>
        <div class="grid grid-3">
            <?php foreach ($services as $service): ?>
            <article class="card">
                <a href="/hizmetler/<?= e($service['slug']) ?>" class="card-image">
                    <?php if ($service['main_image']): ?>
                    <img src="<?= asset($service['main_image']) ?>" alt="<?= e($service['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($service['name']) ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h2 class="card-title">
                        <a href="/hizmetler/<?= e($service['slug']) ?>"><?= e($service['name']) ?></a>
                    </h2>
                    <?php if ($service['short_description']): ?>
                    <p class="card-text"><?= e(truncate($service['short_description'], 120)) ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/hizmetler/<?= e($service['slug']) ?>" class="btn btn-sm btn-outline">Detaylı Bilgi</a>
                    <button type="button" class="btn btn-sm btn-primary" 
                            data-quote-modal
                            data-item-type="service"
                            data-item-id="<?= $service['id'] ?>"
                            data-item-name="<?= e($service['name']) ?>">
                        Teklif Al
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center" style="color: var(--text-light);">Henüz hizmet eklenmemiş.</p>
        <?php endif; ?>
    </div>
</section>