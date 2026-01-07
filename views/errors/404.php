<?php
/**
 * 404 Hata Sayfası
 */

$seo = [
    'title' => 'Sayfa Bulunamadı - ' . setting('site_name'),
    'description' => 'Aradığınız sayfa bulunamadı.',
    'is_indexed' => false
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php partial('head', ['seo' => $seo]); ?>
    <style>
        .error-page {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .error-content h1 {
            font-size: 120px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
            line-height: 1;
        }
        .error-content h2 {
            font-size: 24px;
            margin: 20px 0;
            color: var(--text);
        }
        .error-content p {
            color: var(--text-light);
            margin-bottom: 30px;
        }
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <?php partial('header'); ?>
    
    <main class="error-page">
        <div class="error-content">
            <h1>404</h1>
            <h2>Sayfa Bulunamadı</h2>
            <p>Aradığınız sayfa taşınmış, silinmiş veya hiç var olmamış olabilir.</p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">Ana Sayfaya Dön</a>
                <a href="/iletisim" class="btn btn-outline">İletişime Geç</a>
            </div>
        </div>
    </main>
    
    <?php partial('footer'); ?>
</body>
</html>