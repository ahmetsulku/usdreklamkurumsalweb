<?php
/**
 * Blog Yazı Detay
 */
?>

<article class="blog-detail">
    <div class="container">
        <header class="blog-detail-header">
            <div class="blog-meta" style="justify-content: center; margin-bottom: 20px;">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <?= formatDateTurkish($post['published_at']) ?>
                </span>
                <?php if ($post['category_name']): ?>
                <a href="/blog/kategori/<?= e($post['category_slug']) ?>" style="color: var(--primary);">
                    <?= e($post['category_name']) ?>
                </a>
                <?php endif; ?>
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <?= number_format($post['view_count']) ?> görüntülenme
                </span>
            </div>
            <h1><?= e($post['title']) ?></h1>
            <?php if ($post['author_name']): ?>
            <p style="color: var(--text-light);">Yazar: <?= e($post['author_name']) ?></p>
            <?php endif; ?>
        </header>
        
        <?php if ($post['featured_image']): ?>
        <figure class="blog-featured-image">
            <img src="<?= asset($post['featured_image']) ?>" alt="<?= e($post['title']) ?>">
        </figure>
        <?php endif; ?>
        
        <div class="blog-content">
            <?= $post['content'] ?>
        </div>
        
        <!-- Paylaşım Butonları -->
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border); text-align: center;">
            <p style="margin-bottom: 15px; color: var(--text-light);">Bu yazıyı paylaşın:</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('blog/' . $post['slug'])) ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #1877f2; color: #fff;">
                    Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url('blog/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #1da1f2; color: #fff;">
                    Twitter
                </a>
                <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . url('blog/' . $post['slug'])) ?>" 
                   target="_blank" 
                   class="btn btn-sm" 
                   style="background: #25d366; color: #fff;">
                    WhatsApp
                </a>
            </div>
        </div>
        
        <!-- İlgili Yazılar -->
        <?php if (!empty($relatedPosts)): ?>
        <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border);">
            <h3 style="margin-bottom: 25px; text-align: center;">İlgili Yazılar</h3>
            <div class="grid grid-3">
                <?php foreach ($relatedPosts as $related): ?>
                <article class="card blog-card">
                    <a href="/blog/<?= e($related['slug']) ?>" class="card-image">
                        <?php if ($related['featured_image']): ?>
                        <img src="<?= asset($related['featured_image']) ?>" alt="<?= e($related['title']) ?>" loading="lazy">
                        <?php else: ?>
                        <img src="<?= asset('images/static/placeholder.jpg') ?>" alt="<?= e($related['title']) ?>">
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <h4 class="card-title">
                            <a href="/blog/<?= e($related['slug']) ?>"><?= e($related['title']) ?></a>
                        </h4>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</article>