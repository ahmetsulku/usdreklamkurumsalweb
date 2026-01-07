<?php
/**
 * Breadcrumb (Ekmek Kırıntısı)
 */

$breadcrumbs = $breadcrumbs ?? [];
if (empty($breadcrumbs)) return;

// Schema.org BreadcrumbList
$schemaItems = [];
foreach ($breadcrumbs as $i => $crumb) {
    $schemaItems[] = [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $crumb['title'],
        'item' => $crumb['url'] ? url(ltrim($crumb['url'], '/')) : null
    ];
}
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <div class="container">
        <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <li class="breadcrumb-item<?= $crumb['url'] === null ? ' active' : '' ?>" 
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($crumb['url']): ?>
                    <a href="<?= e($crumb['url']) ?>" itemprop="item">
                        <span itemprop="name"><?= e($crumb['title']) ?></span>
                    </a>
                <?php else: ?>
                    <span itemprop="name"><?= e($crumb['title']) ?></span>
                <?php endif; ?>
                <meta itemprop="position" content="<?= $i + 1 ?>">
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>