<?php
/**
 * Head bölümü - Meta, CSS, Schema.org
 */

$seo = $seo ?? [];
$schema = $schema ?? null;

$title = $seo['title'] ?? setting('site_name');
$description = $seo['description'] ?? setting('site_description');
$canonical = $seo['canonical'] ?? SITE_URL . $_SERVER['REQUEST_URI'];
$ogTitle = $seo['og_title'] ?? $title;
$ogDescription = $seo['og_description'] ?? $description;
$ogImage = isset($seo['og_image']) && $seo['og_image'] ? url($seo['og_image']) : asset('images/static/og-default.jpg');
$isIndexed = $seo['is_indexed'] ?? true;
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- SEO Meta -->
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<?php if (!$isIndexed): ?>
<meta name="robots" content="noindex, nofollow">
<?php else: ?>
<meta name="robots" content="index, follow">
<?php endif; ?>
<link rel="canonical" href="<?= e($canonical) ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($ogTitle) ?>">
<meta property="og:description" content="<?= e($ogDescription) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:site_name" content="<?= e(setting('site_name')) ?>">
<meta property="og:locale" content="tr_TR">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($ogTitle) ?>">
<meta name="twitter:description" content="<?= e($ogDescription) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?= asset('images/static/favicon.ico') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/static/apple-touch-icon.png') ?>">

<!-- CSRF Meta (AJAX için) -->
<?= CSRF::meta() ?>

<!-- Kritik CSS (inline) -->
<style>
<?php include __DIR__ . '/../../public/css/critical.css'; ?>
</style>

<!-- Ana CSS -->
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">

<!-- Schema.org - Organization (her sayfada) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?= e(setting('site_name')) ?>",
    "url": "<?= SITE_URL ?>",
    "logo": "<?= asset('images/static/logo.png') ?>",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= e(setting('site_phone')) ?>",
        "contactType": "customer service",
        "availableLanguage": "Turkish"
    },
    "sameAs": [
        <?php if ($fb = setting('facebook_url')): ?>"<?= e($fb) ?>"<?php endif; ?>
        <?php if ($ig = setting('instagram_url')): ?><?= $fb ? ',' : '' ?>"<?= e($ig) ?>"<?php endif; ?>
    ]
}
</script>

<!-- Schema.org - WebSite (ana sayfada arama kutusu için) -->
<?php if (Router::currentUri() === '/'): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?= e(setting('site_name')) ?>",
    "url": "<?= SITE_URL ?>"
}
</script>
<?php endif; ?>

<!-- Sayfa özel Schema.org -->
<?php if ($schema): ?>
<script type="application/ld+json">
<?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>

<!-- Google Analytics (varsa) -->
<?php if ($ga = setting('google_analytics')): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?= e($ga) ?>');
</script>
<?php endif; ?>