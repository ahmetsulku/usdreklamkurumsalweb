<?php
/**
 * Admin Hizmet Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createService();
        } else {
            showServiceForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updateService($id);
        } else {
            showServiceForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteService($id);
        break;
        
    default:
        listServices();
        break;
}

function listServices() {
    $services = Database::fetchAll("SELECT * FROM services ORDER BY sort_order ASC, created_at DESC");
    $pageTitle = 'Hizmetler';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Hizmetler</h1>
            <p><?= count($services) ?> hizmet listeleniyor</p>
        </div>
        <a href="/panel/hizmetler/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Hizmet
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($services)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:60px;">Görsel</th>
                        <th>Hizmet Adı</th>
                        <th>Slug</th>
                        <th>Sıra</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td>
                            <?php if ($service['main_image']): ?>
                            <img src="<?= asset($service['main_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                            <div style="width:50px;height:50px;background:#f3f4f6;border-radius:4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($service['name']) ?></strong></td>
                        <td><code style="font-size:12px;"><?= e($service['slug']) ?></code></td>
                        <td><?= $service['sort_order'] ?></td>
                        <td>
                            <?php if ($service['is_active']): ?>
                            <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge badge-error">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/panel/hizmetler/duzenle?id=<?= $service['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/hizmetler/sil?id=<?= $service['id'] ?>" class="action-btn delete" onclick="return confirm('Bu hizmeti silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz hizmet eklenmemiş.</p>
                <a href="/panel/hizmetler/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Hizmeti Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showServiceForm($id = null) {
    $service = null;
    $pageTitle = 'Yeni Hizmet';
    
    if ($id) {
        $service = Database::fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
        if (!$service) {
            Session::flash('error', 'Hizmet bulunamadı.');
            redirect('/panel/hizmetler');
        }
        $pageTitle = 'Hizmet Düzenle';
    }
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/hizmetler">← Hizmetlere Dön</a></p>
    </div>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrfInput() ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <div>
                <div class="card">
                    <div class="card-header"><h3>Hizmet Bilgileri</h3></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Hizmet Adı *</label>
                                <input type="text" id="name" name="name" value="<?= e($service['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="slug">Slug *</label>
                                <input type="text" id="slug" name="slug" value="<?= e($service['slug'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="short_description">Kısa Açıklama</label>
                            <textarea id="short_description" name="short_description" rows="3"><?= e($service['short_description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_description">Detaylı Açıklama</label>
                            <textarea id="full_description" name="full_description" rows="8"><?= e($service['full_description'] ?? '') ?></textarea>
                            <small>HTML kullanabilirsiniz</small>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header"><h3>SEO Ayarları</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="seo_title">SEO Başlık</label>
                            <input type="text" id="seo_title" name="seo_title" value="<?= e($service['seo_title'] ?? '') ?>" maxlength="70">
                        </div>
                        <div class="form-group">
                            <label for="meta_description">Meta Açıklama</label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($service['meta_description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_indexed" value="1" <?= ($service['is_indexed'] ?? 1) ? 'checked' : '' ?>>
                                <span>Arama motorlarında indexlensin</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card">
                    <div class="card-header"><h3>Görsel</h3></div>
                    <div class="card-body">
                        <div class="file-upload">
                            <input type="file" name="main_image" accept="image/*">
                            <p>Görsel yüklemek için tıklayın</p>
                            <?php if (!empty($service['main_image'])): ?>
                            <div class="file-preview"><img src="<?= asset($service['main_image']) ?>" alt=""></div>
                            <?php else: ?>
                            <div class="file-preview"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header"><h3>Ayarlar</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="sort_order">Sıra</label>
                            <input type="number" id="sort_order" name="sort_order" value="<?= e($service['sort_order'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" <?= ($service['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <span>Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    <?= $id ? 'Güncelle' : 'Hizmet Ekle' ?>
                </button>
            </div>
        </div>
    </form>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createService() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/hizmetler/ekle');
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description' => $_POST['full_description'] ?? '',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
    ];
    
    if (!empty($_FILES['main_image']['name'])) {
        $data['main_image'] = uploadImage($_FILES['main_image'], 'services');
    }
    
    Database::insert('services', $data);
    Session::flash('success', 'Hizmet başarıyla eklendi.');
    redirect('/panel/hizmetler');
}

function updateService($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/hizmetler/duzenle?id=' . $id);
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description' => $_POST['full_description'] ?? '',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    if (!empty($_FILES['main_image']['name'])) {
        $data['main_image'] = uploadImage($_FILES['main_image'], 'services');
    }
    
    Database::update('services', $data, 'id = ?', [$id]);
    Session::flash('success', 'Hizmet güncellendi.');
    redirect('/panel/hizmetler');
}

function deleteService($id) {
    Database::delete('service_images', 'service_id = ?', [$id]);
    Database::delete('services', 'id = ?', [$id]);
    Session::flash('success', 'Hizmet silindi.');
    redirect('/panel/hizmetler');
}

if (!function_exists('uploadImage')) {
    function uploadImage($file, $folder) {
        $uploadDir = __DIR__ . '/../../public/images/uploads/' . $folder . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return null;
        $filename = uniqid() . '_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return 'images/uploads/' . $folder . '/' . $filename;
        }
        return null;
    }
}