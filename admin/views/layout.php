<?php
/**
 * Admin Panel Layout
 */
$pageTitle = $pageTitle ?? 'Panel';
$admin = getAdmin();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(setting('site_name')) ?> Panel</title>
    <meta name="robots" content="noindex, nofollow">
    <?= CSRF::meta() ?>
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/panel" class="sidebar-logo">
                    <strong><?= e(setting('site_name')) ?></strong>
                    <small>Yönetim Paneli</small>
                </a>
                <button class="sidebar-close" id="sidebar-close">&times;</button>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>">
                        <a href="/panel/dashboard">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-title">İçerik Yönetimi</li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/kategoriler') !== false ? 'active' : '' ?>">
                        <a href="/panel/kategoriler">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                            Kategoriler
                        </a>
                    </li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/urunler') !== false ? 'active' : '' ?>">
                        <a href="/panel/urunler">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            Ürünler
                        </a>
                    </li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/hizmetler') !== false ? 'active' : '' ?>">
                        <a href="/panel/hizmetler">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            Hizmetler
                        </a>
                    </li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/blog') !== false ? 'active' : '' ?>">
                        <a href="/panel/blog">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Blog
                        </a>
                    </li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/sayfalar') !== false ? 'active' : '' ?>">
                        <a href="/panel/sayfalar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            Sayfalar
                        </a>
                    </li>
                    
                    <li class="nav-title">Site Öğeleri</li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/slider') !== false ? 'active' : '' ?>">
                        <a href="/panel/slider">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="15" rx="2" ry="2"/>
                                <polyline points="17 2 12 7 7 2"/>
                            </svg>
                            Slider
                        </a>
                    </li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/yorumlar') !== false ? 'active' : '' ?>">
                        <a href="/panel/yorumlar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            Yorumlar
                        </a>
                    </li>
                    
                    <li class="nav-title">Talepler</li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/teklif-talepleri') !== false ? 'active' : '' ?>">
                        <a href="/panel/teklif-talepleri">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            Teklif Talepleri
                            <?php 
                            $newQuotes = Database::fetchOne("SELECT COUNT(*) as c FROM quote_requests WHERE is_read = 0")['c'];
                            if ($newQuotes > 0): 
                            ?>
                            <span class="badge"><?= $newQuotes ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-title">Sistem</li>
                    
                    <li class="<?= strpos($_SERVER['REQUEST_URI'], '/ayarlar') !== false ? 'active' : '' ?>">
                        <a href="/panel/ayarlar">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            Ayarlar
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/" target="_blank" class="btn btn-sm btn-outline-light">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Siteyi Görüntüle
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <button class="menu-toggle" id="menu-toggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                
                <div class="header-right">
                    <div class="admin-user">
                        <span><?= e($admin['full_name'] ?? $admin['username']) ?></span>
                        <div class="user-dropdown">
                            <a href="/panel/sifre-degistir">Şifre Değiştir</a>
                            <a href="/panel/cikis">Çıkış Yap</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="admin-content">
                <?php 
                $flash = Session::getAllFlash();
                if (!empty($flash)): 
                ?>
                <div class="flash-messages">
                    <?php if (isset($flash['success'])): ?>
                    <div class="alert alert-success"><?= e($flash['success']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($flash['error'])): ?>
                    <div class="alert alert-error"><?= e($flash['error']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($flash['warning'])): ?>
                    <div class="alert alert-warning"><?= e($flash['warning']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    
    <script src="/admin/assets/admin.js"></script>
</body>
</html>