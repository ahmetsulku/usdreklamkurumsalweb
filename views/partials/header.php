<?php
/**
 * Site Header
 */

// Menü öğelerini al
$menuItems = Database::fetchAll(
    "SELECT * FROM menu_items WHERE menu_location = 'header' AND is_active = 1 AND parent_id IS NULL ORDER BY sort_order ASC"
);

// Alt menüleri al
foreach ($menuItems as &$item) {
    $item['children'] = Database::fetchAll(
        "SELECT * FROM menu_items WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order ASC",
        [$item['id']]
    );
}
?>
<header class="site-header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-contact">
                    <?php if ($phone = setting('site_phone')): ?>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phone) ?>" class="header-phone">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <?= e($phone) ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($email = setting('site_email')): ?>
                    <a href="mailto:<?= e($email) ?>" class="header-email">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <?= e($email) ?>
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="header-social">
                    <?php if ($whatsapp = setting('site_whatsapp')): ?>
                    <a href="<?= whatsappLink($whatsapp) ?>" target="_blank" rel="noopener" class="social-link whatsapp" title="WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($instagram = setting('instagram_url')): ?>
                    <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" class="social-link instagram" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($facebook = setting('facebook_url')): ?>
                    <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" class="social-link facebook" title="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container">
            <div class="header-main-content">
                <a href="/" class="logo">
                    <img src="<?= asset('images/static/logo.png') ?>" alt="<?= e(setting('site_name')) ?>" width="180" height="50">
                </a>
                
                <nav class="main-nav" id="main-nav">
                    <ul class="nav-list">
                        <?php foreach ($menuItems as $item): ?>
                        <li class="nav-item<?= !empty($item['children']) ? ' has-dropdown' : '' ?><?= Router::isActive($item['url']) ? ' active' : '' ?>">
                            <a href="<?= e($item['url']) ?>"<?= $item['target'] !== '_self' ? ' target="' . e($item['target']) . '"' : '' ?>>
                                <?= e($item['title']) ?>
                                <?php if (!empty($item['children'])): ?>
                                <svg class="dropdown-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                                <?php endif; ?>
                            </a>
                            
                            <?php if (!empty($item['children'])): ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($item['children'] as $child): ?>
                                <li>
                                    <a href="<?= e($child['url']) ?>"><?= e($child['title']) ?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <a href="/iletisim" class="btn btn-primary header-cta">Teklif Al</a>
                    
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Menüyü aç/kapat">
                        <span class="hamburger"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>