<?php
/**
 * Admin Slider Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createSlider();
        } else {
            showSliderForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updateSlider($id);
        } else {
            showSliderForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteSlider($id);
        break;
        
    default:
        listSliders();
        break;
}

function listSliders() {
    $sliders = Database::fetchAll("SELECT * FROM sliders ORDER BY sort_order ASC");
    $pageTitle = 'Slider';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Slider Yönetimi</h1>
            <p>Ana sayfa slider görsellerini yönetin</p>
        </div>
        <a href="/panel/slider/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Slide
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($sliders)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:120px;">Görsel</th>
                        <th>Başlık</th>
                        <th>Buton</th>
                        <th>Sıra</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sliders as $slider): ?>
                    <tr>
                        <td>
                            <?php if ($slider['image_desktop']): ?>
                            <img src="<?= asset($slider['image_desktop']) ?>" alt="" style="width:100px;height:60px;object-fit:cover;border-radius:4px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($slider['title'] ?: '-') ?></strong>
                            <?php if ($slider['subtitle']): ?>
                            <br><small style="color:#9ca3af;"><?= e(truncate($slider['subtitle'], 50)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($slider['button_text']): ?>
                            <?= e($slider['button_text']) ?>
                            <br><small style="color:#9ca3af;"><?= e($slider['button_url']) ?></small>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td><?= $slider['sort_order'] ?></td>
                        <td>
                            <?php if ($slider['is_active']): ?>
                            <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge badge-error">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/panel/slider/duzenle?id=<?= $slider['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/slider/sil?id=<?= $slider['id'] ?>" class="action-btn delete" onclick="return confirm('Bu slide\'ı silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz slider eklenmemiş.</p>
                <a href="/panel/slider/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Slide'ı Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showSliderForm($id = null) {
    $slider = null;
    $pageTitle = 'Yeni Slide';
    
    if ($id) {
        $slider = Database::fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
        if (!$slider) {
            Session::flash('error', 'Slide bulunamadı.');
            redirect('/panel/slider');
        }
        $pageTitle = 'Slide Düzenle';
    }
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/slider">← Slider'a Dön</a></p>
    </div>
    
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <?= csrfInput() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" id="title" name="title" value="<?= e($slider['title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sıra</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= e($slider['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subtitle">Alt Başlık</label>
                    <input type="text" id="subtitle" name="subtitle" value="<?= e($slider['subtitle'] ?? '') ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="button_text">Buton Metni</label>
                        <input type="text" id="button_text" name="button_text" value="<?= e($slider['button_text'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="button_url">Buton URL</label>
                        <input type="text" id="button_url" name="button_url" value="<?= e($slider['button_url'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Desktop Görsel (1920x600)</label>
                        <div class="file-upload">
                            <input type="file" name="image_desktop" accept="image/*">
                            <p>Görsel yükle</p>
                            <?php if (!empty($slider['image_desktop'])): ?>
                            <div class="file-preview"><img src="<?= asset($slider['image_desktop']) ?>" alt=""></div>
                            <?php else: ?>
                            <div class="file-preview"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mobil Görsel (768x500)</label>
                        <div class="file-upload">
                            <input type="file" name="image_mobile" accept="image/*">
                            <p>Görsel yükle (opsiyonel)</p>
                            <?php if (!empty($slider['image_mobile'])): ?>
                            <div class="file-preview"><img src="<?= asset($slider['image_mobile']) ?>" alt=""></div>
                            <?php else: ?>
                            <div class="file-preview"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" <?= ($slider['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span>Aktif</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary"><?= $id ? 'Güncelle' : 'Slide Ekle' ?></button>
            </form>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createSlider() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/slider/ekle');
    }
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'button_text' => trim($_POST['button_text'] ?? ''),
        'button_url' => trim($_POST['button_url'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    if (!empty($_FILES['image_desktop']['name'])) {
        $data['image_desktop'] = uploadImage($_FILES['image_desktop'], 'sliders');
    }
    if (!empty($_FILES['image_mobile']['name'])) {
        $data['image_mobile'] = uploadImage($_FILES['image_mobile'], 'sliders');
    }
    
    Database::insert('sliders', $data);
    Session::flash('success', 'Slide eklendi.');
    redirect('/panel/slider');
}

function updateSlider($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/slider/duzenle?id=' . $id);
    }
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'button_text' => trim($_POST['button_text'] ?? ''),
        'button_url' => trim($_POST['button_url'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    if (!empty($_FILES['image_desktop']['name'])) {
        $data['image_desktop'] = uploadImage($_FILES['image_desktop'], 'sliders');
    }
    if (!empty($_FILES['image_mobile']['name'])) {
        $data['image_mobile'] = uploadImage($_FILES['image_mobile'], 'sliders');
    }
    
    Database::update('sliders', $data, 'id = ?', [$id]);
    Session::flash('success', 'Slide güncellendi.');
    redirect('/panel/slider');
}

function deleteSlider($id) {
    Database::delete('sliders', 'id = ?', [$id]);
    Session::flash('success', 'Slide silindi.');
    redirect('/panel/slider');
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