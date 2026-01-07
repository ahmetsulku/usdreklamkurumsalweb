<?php
/**
 * Ana Layout
 */

// Varsayılan değerler
$seo = $seo ?? [];
$schema = $schema ?? null;
$breadcrumbs = $breadcrumbs ?? [];
$content = $content ?? '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php 
    $headFile = __DIR__ . '/../partials/head.php';
    if (file_exists($headFile)) {
        include $headFile;
    }
    ?>
</head>
<body>
    <?php 
    $headerFile = __DIR__ . '/../partials/header.php';
    if (file_exists($headerFile)) {
        include $headerFile;
    }
    ?>
    
    <?php 
    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    $currentUri = strtok($currentUri, '?');
    
    if (!empty($breadcrumbs) && $currentUri !== '/'): 
        $breadcrumbFile = __DIR__ . '/../partials/breadcrumb.php';
        if (file_exists($breadcrumbFile)) {
            include $breadcrumbFile;
        }
    endif; 
    ?>
    
    <main id="main-content">
        <?php 
        // Flash mesajları
        if (class_exists('Session')) {
            $flash = Session::getAllFlash();
            if (!empty($flash)): 
        ?>
        <div class="container" style="padding-top: 20px;">
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
        <?php 
            endif;
        }
        ?>
        
        <?php 
        // İçerik view'ını yükle
        if (!empty($content)) {
            $contentPath = __DIR__ . '/../' . str_replace('.', '/', $content) . '.php';
            if (file_exists($contentPath)) {
                include $contentPath;
            } else {
                echo '<div class="container"><p>View bulunamadı: ' . htmlspecialchars($content) . '</p></div>';
            }
        }
        ?>
    </main>
    
    <?php 
    $footerFile = __DIR__ . '/../partials/footer.php';
    if (file_exists($footerFile)) {
        include $footerFile;
    }
    ?>
    
    <?php 
    $modalFile = __DIR__ . '/../partials/quote-modal.php';
    if (file_exists($modalFile)) {
        include $modalFile;
    }
    ?>
    
    <script src="/js/main.js"></script>
</body>
</html>