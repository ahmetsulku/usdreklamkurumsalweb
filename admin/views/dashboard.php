<?php
/**
 * Admin Dashboard
 */
$pageTitle = 'Dashboard';

ob_start();
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Hoş geldiniz, <?= e(getAdmin()['full_name']) ?></p>
</div>

<!-- İstatistik Kartları -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3b82f6;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <div class="stat-info">
            <h3><?= $stats['products'] ?></h3>
            <p>Ürün</p>
        </div>
        <a href="/panel/urunler" class="stat-link">Görüntüle →</a>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #8b5cf6;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9"/>
            </svg>
        </div>
        <div class="stat-info">
            <h3><?= $stats['services'] ?></h3>
            <p>Hizmet</p>
        </div>
        <a href="/panel/hizmetler" class="stat-link">Görüntüle →</a>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #10b981;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <div class="stat-info">
            <h3><?= $stats['blog_posts'] ?></h3>
            <p>Blog Yazısı</p>
        </div>
        <a href="/panel/blog" class="stat-link">Görüntüle →</a>
    </div>
    
    <div class="stat-card <?= $stats['quotes_new'] > 0 ? 'highlight' : '' ?>">
        <div class="stat-icon" style="background: #f59e0b;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div class="stat-info">
            <h3><?= $stats['quotes_total'] ?></h3>
            <p>Teklif Talebi</p>
            <?php if ($stats['quotes_new'] > 0): ?>
            <span class="badge badge-warning"><?= $stats['quotes_new'] ?> yeni</span>
            <?php endif; ?>
        </div>
        <a href="/panel/teklif-talepleri" class="stat-link">Görüntüle →</a>
    </div>
</div>

<!-- Son Teklif Talepleri -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3>Son Teklif Talepleri</h3>
        <a href="/panel/teklif-talepleri" class="btn btn-sm btn-outline">Tümünü Gör</a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentQuotes)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Ref. No</th>
                    <th>Ad</th>
                    <th>Tür</th>
                    <th>Ürün/Hizmet</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentQuotes as $quote): ?>
                <tr class="<?= !$quote['is_read'] ? 'unread' : '' ?>">
                    <td><strong><?= e($quote['reference_no']) ?></strong></td>
                    <td><?= e($quote['name']) ?></td>
                    <td>
                        <?php
                        $typeLabels = ['call' => 'Telefon', 'email' => 'E-posta', 'whatsapp' => 'WhatsApp'];
                        echo $typeLabels[$quote['request_type']] ?? $quote['request_type'];
                        ?>
                    </td>
                    <td><?= e($quote['item_name'] ?: '-') ?></td>
                    <td><?= formatDate($quote['created_at'], 'd.m.Y H:i') ?></td>
                    <td>
                        <?php if (!$quote['is_read']): ?>
                        <span class="badge badge-warning">Yeni</span>
                        <?php else: ?>
                        <span class="badge badge-success">Okundu</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: #6b7280; text-align: center; padding: 20px;">Henüz teklif talebi yok.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>