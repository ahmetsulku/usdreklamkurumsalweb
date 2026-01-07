<?php
/**
 * Admin Kategori Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createCategory();
        } else {
            showCategoryForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updateCategory($id);
        } else {
            showCategoryForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteCategory($id);
        break;
        
    default:
        listCategories();
        break;
}

function listCategories() {
    $categories = Database::fetchAll(
        "SELECT c.*, p.name as parent_name,
                (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
         FROM categories c 
         LEFT JOIN categories p ON c.parent_id = p.id 
         ORDER BY c.parent_id NULLS FIRST, c.sort_order ASC"
    );
    
    $pageTitle = 'Kategoriler';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Kategoriler</h1>
            <p>Ürün kategorilerini yönetin</p>
        </div>
        <a href="/panel/kategoriler/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Kategori
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($categories)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sıra</th>
                        <th>Ad</th>
                        <th>Üst Kategori</th>
                        <th>Slug</th>
                        <th>Ürün</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['sort_order'] ?></td>
                        <td>
                            <strong><?= e($cat['name']) ?></strong>
                        </td>
                        <td><?= $cat['parent_name'] ? e($cat['parent_name']) : '<span style="color:#9ca3af;">-</span>' ?></td>
                        <td><code style="font-size:12px;"><?= e($cat['slug']) ?></code></td>
                        <td><?= $cat['product_count'] ?></td>
                        <td>
                            <?php if ($cat['is_active']): ?>
                            <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge badge-error">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/<?= e($cat['slug']) ?>" target="_blank" class="action-btn view" title="Görüntüle">👁</a>
                                <a href="/panel/kategoriler/duzenle?id=<?= $cat['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/kategoriler/sil?id=<?= $cat['id'] ?>" class="action-btn delete" onclick="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz kategori eklenmemiş.</p>
                <a href="/panel/kategoriler/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Kategoriyi Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showCategoryForm($id = null) {
    $category = null;
    $pageTitle = 'Yeni Kategori';
    
    if ($id) {
        $category = Database::fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) {
            Session::flash('error', 'Kategori bulunamadı.');
            redirect('/panel/kategoriler');
        }
        $pageTitle = 'Kategori Düzenle';
    }
    
    // Üst kategoriler (sadece parent_id null olanlar)
    $parentCategories = Database::fetchAll(
        "SELECT id, name FROM categories WHERE parent_id IS NULL AND id != ? ORDER BY name",
        [$id ?: 0]
    );
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/kategoriler">← Kategorilere Dön</a></p>
    </div>
    
    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <?= csrfInput() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Kategori Adı *</label>
                        <input type="text" id="name" name="name" value="<?= e($category['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug *</label>
                        <input type="text" id="slug" name="slug" value="<?= e($category['slug'] ?? '') ?>" required>
                        <small>URL'de görünecek (örn: metal-etiket)</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_id">Üst Kategori</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">Ana Kategori (Üst kategori yok)</option>
                            <?php foreach ($parentCategories as $parent): ?>
                            <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                <?= e($parent['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">Sıra</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= e($category['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description" rows="3"><?= e($category['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Kategori Görseli</label>
                    <div class="file-upload">
                        <input type="file" id="image" name="image" accept="image/*">
                        <p>Görsel yüklemek için tıklayın</p>
                        <?php if (!empty($category['image'])): ?>
                        <div class="file-preview">
                            <img src="<?= asset($category['image']) ?>" alt="">
                        </div>
                        <?php else: ?>
                        <div class="file-preview"></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span>Aktif</span>
                    </label>
                </div>
                
                <!-- SEO Alanları -->
                <div class="seo-fields">
                    <h4>SEO Ayarları</h4>
                    
                    <div class="form-group">
                        <label for="seo_title">SEO Başlık</label>
                        <input type="text" id="seo_title" name="seo_title" value="<?= e($category['seo_title'] ?? '') ?>" maxlength="70">
                        <small>Boş bırakılırsa kategori adı kullanılır (max 70 karakter)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Açıklama</label>
                        <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($category['meta_description'] ?? '') ?></textarea>
                        <small>max 160 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_indexed" value="1" <?= ($category['is_indexed'] ?? 1) ? 'checked' : '' ?>>
                            <span>Arama motorlarında indexlensin</span>
                        </label>
                    </div>
                </div>
                
                <div style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">
                        <?= $id ? 'Güncelle' : 'Kategori Ekle' ?>
                    </button>
                    <a href="/panel/kategoriler" class="btn btn-outline" style="margin-left: 10px;">İptal</a>
                </div>
            </form>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createCategory() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/kategoriler/ekle');
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'parent_id' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
        'description' => trim($_POST['description'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
    ];
    
    // Slug benzersizlik kontrolü
    $existing = Database::fetchOne("SELECT id FROM categories WHERE slug = ?", [$data['slug']]);
    if ($existing) {
        Session::flash('error', 'Bu slug zaten kullanılıyor.');
        redirect('/panel/kategoriler/ekle');
    }
    
    // Görsel yükleme
    if (!empty($_FILES['image']['name'])) {
        $data['image'] = uploadImage($_FILES['image'], 'categories');
    }
    
    Database::insert('categories', $data);
    
    Session::flash('success', 'Kategori başarıyla eklendi.');
    redirect('/panel/kategoriler');
}

function updateCategory($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/kategoriler/duzenle?id=' . $id);
    }
    
    $category = Database::fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
    if (!$category) {
        Session::flash('error', 'Kategori bulunamadı.');
        redirect('/panel/kategoriler');
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'parent_id' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
        'description' => trim($_POST['description'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    // Slug benzersizlik kontrolü (kendi hariç)
    $existing = Database::fetchOne("SELECT id FROM categories WHERE slug = ? AND id != ?", [$data['slug'], $id]);
    if ($existing) {
        Session::flash('error', 'Bu slug zaten kullanılıyor.');
        redirect('/panel/kategoriler/duzenle?id=' . $id);
    }
    
    // Görsel yükleme
    if (!empty($_FILES['image']['name'])) {
        $data['image'] = uploadImage($_FILES['image'], 'categories');
    }
    
    Database::update('categories', $data, 'id = ?', [$id]);
    
    Session::flash('success', 'Kategori güncellendi.');
    redirect('/panel/kategoriler');
}

function deleteCategory($id) {
    // Ürün var mı kontrol et
    $productCount = Database::fetchOne("SELECT COUNT(*) as c FROM products WHERE category_id = ?", [$id])['c'];
    if ($productCount > 0) {
        Session::flash('error', 'Bu kategoride ürünler var. Önce ürünleri silin veya taşıyın.');
        redirect('/panel/kategoriler');
    }
    
    // Alt kategori var mı kontrol et
    $subCount = Database::fetchOne("SELECT COUNT(*) as c FROM categories WHERE parent_id = ?", [$id])['c'];
    if ($subCount > 0) {
        Session::flash('error', 'Bu kategorinin alt kategorileri var. Önce alt kategorileri silin.');
        redirect('/panel/kategoriler');
    }
    
    Database::delete('categories', 'id = ?', [$id]);
    
    Session::flash('success', 'Kategori silindi.');
    redirect('/panel/kategoriler');
}

// Yardımcı fonksiyon: Görsel yükleme
function uploadImage($file, $folder) {
    $uploadDir = __DIR__ . '/../../public/images/uploads/' . $folder . '/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($ext, $allowed)) {
        return null;
    }
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return 'images/uploads/' . $folder . '/' . $filename;
    }
    
    return null;
}