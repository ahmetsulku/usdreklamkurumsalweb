<?php
/**
 * Admin Statik Sayfalar Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updatePage($id);
        } else {
            showPageForm($id);
        }
        break;
        
    default:
        listPages();
        break;
}

function listPages() {
    $pages = Database::fetchAll("SELECT * FROM pages ORDER BY title");
    $pageTitle = 'Sayfalar';
    
    ob_start();
    ?>
    <div class="page-header">
        <h1>Statik Sayfalar</h1>
        <p>Hakkımızda, İletişim gibi sabit sayfaları düzenleyin</p>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($pages)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sayfa Adı</th>
                        <th>Slug</th>
                        <th>Son Güncelleme</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><strong><?= e($page['title']) ?></strong></td>
                        <td><code><?= e($page['slug']) ?></code></td>
                        <td><?= formatDate($page['updated_at'] ?? $page['created_at'], 'd.m.Y H:i') ?></td>
                        <td>
                            <div class="actions">
                                <a href="/panel/sayfalar/duzenle?id=<?= $page['id'] ?>" class="action-btn edit">Düzenle</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz sayfa eklenmemiş.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showPageForm($id) {
    $page = Database::fetchOne("SELECT * FROM pages WHERE id = ?", [$id]);
    if (!$page) {
        Session::flash('error', 'Sayfa bulunamadı.');
        redirect('/panel/sayfalar');
    }
    
    $pageTitle = 'Sayfa Düzenle: ' . $page['title'];
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= e($page['title']) ?> Düzenle</h1>
        <p><a href="/panel/sayfalar">← Sayfalara Dön</a></p>
    </div>
    
    <form action="" method="POST">
        <?= csrfInput() ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <div>
                <div class="card">
                    <div class="card-header"><h3>Sayfa İçeriği</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Sayfa Başlığı *</label>
                            <input type="text" id="title" name="title" value="<?= e($page['title']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">İçerik</label>
                            <textarea id="content" name="content" rows="15"><?= e($page['content']) ?></textarea>
                            <small>HTML kullanabilirsiniz</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card">
                    <div class="card-header"><h3>SEO Ayarları</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="seo_title">SEO Başlık</label>
                            <input type="text" id="seo_title" name="seo_title" value="<?= e($page['seo_title'] ?? '') ?>" maxlength="70">
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">Meta Açıklama</label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($page['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Kaydet</button>
            </div>
        </div>
    </form>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function updatePage($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/sayfalar/duzenle?id=' . $id);
    }
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    Database::update('pages', $data, 'id = ?', [$id]);
    Session::flash('success', 'Sayfa güncellendi.');
    redirect('/panel/sayfalar');
}