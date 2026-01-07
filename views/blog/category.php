<?php
/**
 * Blog Kategori Sayfası
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title"><?= e($category['name']) ?></h1>
            <?php if ($category['description']): ?>
            <p class="section-subtitle"><?= e($category['description']) ?></p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="/blog" class="btn btn-sm btn-outline">← Tüm Yazılar</a>
        </div>
        
        <?php if (!empty($posts)): ?>
        <div class="grid grid-3">
            <?php foreach ($posts as $post): ?>
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
                    </div>
                    <h2 class="card-title">
                        <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                    </h2>
                    <?php if ($post['excerpt']): ?>
                    <p class="card-text"><?= e(truncate($post['excerpt'], 120)) ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <span style="font-size: 13px; color: var(--text-light);"><?= number_format($post['view_count']) ?> görüntülenme</span>
                    <a href="/blog/<?= e($post['slug']) ?>" class="btn btn-sm btn-outline">Oku</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center" style="color: var(--text-light); padding: 40px 0;">
            Bu kategoride henüz yazı bulunmuyor.
        </p>
        <?php endif; ?>
    </div>
</section>