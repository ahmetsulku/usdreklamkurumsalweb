<?php
/**
 * Admin Teklif Talepleri Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'goruntule':
        $id = (int) ($_GET['id'] ?? 0);
        viewQuote($id);
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteQuote($id);
        break;
        
    default:
        listQuotes();
        break;
}

function listQuotes() {
    $statusFilter = $_GET['durum'] ?? '';
    $typeFilter = $_GET['tur'] ?? '';
    
    $where = "1=1";
    $params = [];
    
    if ($statusFilter === 'yeni') {
        $where .= " AND is_read = 0";
    } elseif ($statusFilter === 'okundu') {
        $where .= " AND is_read = 1";
    }
    
    if ($typeFilter) {
        $where .= " AND request_type = ?";
        $params[] = $typeFilter;
    }
    
    $quotes = Database::fetchAll(
        "SELECT * FROM quote_requests WHERE {$where} ORDER BY created_at DESC",
        $params
    );
    
    $pageTitle = 'Teklif Talepleri';
    
    ob_start();
    ?>
    <div class="page-header">
        <h1>Teklif Talepleri</h1>
        <p><?= count($quotes) ?> talep listeleniyor</p>
    </div>
    
    <!-- Filtreler -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 15px;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <select name="durum" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius);">
                    <option value="">Tüm Durumlar</option>
                    <option value="yeni" <?= $statusFilter === 'yeni' ? 'selected' : '' ?>>Yeni</option>
                    <option value="okundu" <?= $statusFilter === 'okundu' ? 'selected' : '' ?>>Okundu</option>
                </select>
                
                <select name="tur" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius);">
                    <option value="">Tüm Türler</option>
                    <option value="call" <?= $typeFilter === 'call' ? 'selected' : '' ?>>Telefon</option>
                    <option value="email" <?= $typeFilter === 'email' ? 'selected' : '' ?>>E-posta</option>
                    <option value="whatsapp" <?= $typeFilter === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                </select>
                
                <?php if ($statusFilter || $typeFilter): ?>
                <a href="/panel/teklif-talepleri" style="font-size: 13px;">Filtreyi Temizle</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($quotes)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ref. No</th>
                        <th>Ad / Firma</th>
                        <th>İletişim</th>
                        <th>Tür</th>
                        <th>Ürün/Hizmet</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                    <tr class="<?= !$quote['is_read'] ? 'unread' : '' ?>">
                        <td><strong><?= e($quote['reference_no']) ?></strong></td>
                        <td><?= e($quote['name']) ?></td>
                        <td>
                            <?php if ($quote['phone']): ?>
                            <a href="tel:<?= e($quote['phone']) ?>"><?= e($quote['phone']) ?></a><br>
                            <?php endif; ?>
                            <?php if ($quote['email']): ?>
                            <a href="mailto:<?= e($quote['email']) ?>" style="font-size:12px;"><?= e($quote['email']) ?></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $typeLabels = ['call' => 'Telefon', 'email' => 'E-posta', 'whatsapp' => 'WhatsApp'];
                            $typeBadges = ['call' => 'info', 'email' => 'warning', 'whatsapp' => 'success'];
                            ?>
                            <span class="badge badge-<?= $typeBadges[$quote['request_type']] ?? 'info' ?>">
                                <?= $typeLabels[$quote['request_type']] ?? $quote['request_type'] ?>
                            </span>
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
                        <td>
                            <div class="actions">
                                <a href="/panel/teklif-talepleri/goruntule?id=<?= $quote['id'] ?>" class="action-btn view">Detay</a>
                                <a href="/panel/teklif-talepleri/sil?id=<?= $quote['id'] ?>" class="action-btn delete" onclick="return confirm('Bu talebi silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Teklif talebi bulunmuyor.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function viewQuote($id) {
    $quote = Database::fetchOne("SELECT * FROM quote_requests WHERE id = ?", [$id]);
    
    if (!$quote) {
        Session::flash('error', 'Talep bulunamadı.');
        redirect('/panel/teklif-talepleri');
    }
    
    // Okundu olarak işaretle
    if (!$quote['is_read']) {
        Database::query("UPDATE quote_requests SET is_read = 1 WHERE id = ?", [$id]);
    }
    
    // Aynı müşteriden önceki talepler
    $previousQuotes = [];
    if ($quote['email']) {
        $previousQuotes = Database::fetchAll(
            "SELECT * FROM quote_requests WHERE email = ? AND id != ? ORDER BY created_at DESC LIMIT 5",
            [$quote['email'], $id]
        );
    } elseif ($quote['phone']) {
        $previousQuotes = Database::fetchAll(
            "SELECT * FROM quote_requests WHERE phone LIKE ? AND id != ? ORDER BY created_at DESC LIMIT 5",
            ['%' . substr(preg_replace('/[^0-9]/', '', $quote['phone']), -10), $id]
        );
    }
    
    $pageTitle = 'Teklif Detayı - ' . $quote['reference_no'];
    
    ob_start();
    ?>
    <div class="page-header">
        <h1>Teklif Detayı</h1>
        <p><a href="/panel/teklif-talepleri">← Taleplere Dön</a></p>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <div class="card">
            <div class="card-header">
                <h3><?= e($quote['reference_no']) ?></h3>
                <?php if (!$quote['is_read']): ?>
                <span class="badge badge-warning">Yeni</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 10px 0; width: 150px; color: var(--text-light);">Talep Türü</td>
                        <td style="padding: 10px 0;">
                            <?php
                            $typeLabels = ['call' => 'Telefon ile Arayın', 'email' => 'E-posta ile Teklif', 'whatsapp' => 'WhatsApp ile Teklif'];
                            echo $typeLabels[$quote['request_type']] ?? $quote['request_type'];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Ad / Firma</td>
                        <td style="padding: 10px 0;"><strong><?= e($quote['name']) ?></strong></td>
                    </tr>
                    <?php if ($quote['phone']): ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Telefon</td>
                        <td style="padding: 10px 0;">
                            <a href="tel:<?= e($quote['phone']) ?>" class="btn btn-sm btn-primary"><?= e($quote['phone']) ?></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($quote['email']): ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">E-posta</td>
                        <td style="padding: 10px 0;">
                            <a href="mailto:<?= e($quote['email']) ?>"><?= e($quote['email']) ?></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Ürün / Hizmet</td>
                        <td style="padding: 10px 0;"><?= e($quote['item_name'] ?: '-') ?></td>
                    </tr>
                    <?php if ($quote['quantity']): ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Adet</td>
                        <td style="padding: 10px 0;"><?= e($quote['quantity']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($quote['dimensions']): ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Ölçü</td>
                        <td style="padding: 10px 0;">
                            <?= e($quote['dimensions']) ?>
                            <?php if ($quote['has_multiple_dimensions']): ?>
                            <span class="badge badge-warning" style="margin-left: 5px;">Farklı ölçüler</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($quote['additional_notes']): ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Notlar</td>
                        <td style="padding: 10px 0;"><?= nl2br(e($quote['additional_notes'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">Tarih</td>
                        <td style="padding: 10px 0;"><?= formatDate($quote['created_at'], 'd.m.Y H:i:s') ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: var(--text-light);">IP Adresi</td>
                        <td style="padding: 10px 0;"><code><?= e($quote['ip_address']) ?></code></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Önceki Talepler -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Önceki Talepler</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($previousQuotes)): ?>
                    <ul style="list-style: none;">
                        <?php foreach ($previousQuotes as $prev): ?>
                        <li style="padding: 10px 0; border-bottom: 1px solid var(--border);">
                            <a href="/panel/teklif-talepleri/goruntule?id=<?= $prev['id'] ?>">
                                <strong><?= e($prev['reference_no']) ?></strong>
                            </a>
                            <br>
                            <small style="color: var(--text-light);">
                                <?= e($prev['item_name'] ?: 'Genel') ?> - <?= formatDate($prev['created_at'], 'd.m.Y') ?>
                            </small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p style="color: var(--text-light);">Bu müşteriden başka talep yok.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Hızlı İşlemler -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Hızlı İşlemler</h3>
                </div>
                <div class="card-body">
                    <?php if ($quote['phone']): ?>
                    <a href="tel:<?= e($quote['phone']) ?>" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">
                        📞 Ara
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($quote['email']): ?>
                    <a href="mailto:<?= e($quote['email']) ?>?subject=Re: Teklif Talebi <?= e($quote['reference_no']) ?>" class="btn btn-outline" style="width: 100%; margin-bottom: 10px;">
                        ✉️ E-posta Gönder
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($quote['phone']): ?>
                    <a href="<?= whatsappLink(preg_replace('/[^0-9]/', '', $quote['phone']), 'Merhaba, ' . $quote['reference_no'] . ' numaralı teklif talebiniz için dönüş yapıyoruz.') ?>" target="_blank" class="btn" style="width: 100%; background: #25d366; color: #fff;">
                        💬 WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function deleteQuote($id) {
    Database::delete('quote_requests', 'id = ?', [$id]);
    Session::flash('success', 'Talep silindi.');
    redirect('/panel/teklif-talepleri');
}