<?php
/**
 * Blog Listesi
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Blog</h1>
            <p class="section-subtitle">Sektörden haberler, ipuçları ve bilgiler</p>
        </div>
        
        <?php if (!empty($categories)): ?>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 40px;">
            <a href="/blog" class="btn btn-sm <?= !isset($currentCategory) ? 'btn-primary' : 'btn-outline' ?>">Tümü</a>
            <?php foreach ($categories as $cat): ?>
            <a href="/blog/kategori/<?= e($cat['slug']) ?>" 
               class="btn btn-sm <?= (isset($currentCategory) && $currentCategory['id'] == $cat['id']) ? 'btn-primary' : 'btn-outline' ?>">
                <?= e($cat['name']) ?>
                <span style="opacity: 0.7; margin-left: 4px;">(<?= $cat['post_count'] ?>)</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
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
                        <span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?= formatDateTurkish($post['published_at']) ?>
                        </span>
                        <?php if ($post['category_name'] ?? null): ?>
                        <span><?= e($post['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h2 class="card-title">
                        <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                    </h2>
                    <?php if ($post['excerpt']): ?>
                    <p class="card-text"><?= e(truncate($post['excerpt'], 120)) ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <span style="font-size: 13px; color: var(--text-light);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <?= number_format($post['view_count']) ?>
                    </span>
                    <a href="/blog/<?= e($post['slug']) ?>" class="btn btn-sm btn-outline">Devamını Oku</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($currentPage > 1): ?>
            <a href="?sayfa=<?= $currentPage - 1 ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $currentPage): ?>
                <span class="active"><?= $i ?></span>
                <?php else: ?>
                <a href="?sayfa=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <a href="?sayfa=<?= $currentPage + 1 ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <p class="text-center" style="color: var(--text-light); padding: 40px 0;">
            Henüz blog yazısı eklenmemiş.
        </p>
        <?php endif; ?>
    </div>
</section>