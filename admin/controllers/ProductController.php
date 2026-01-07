<?php
/**
 * Admin Ürün Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createProduct();
        } else {
            showProductForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updateProduct($id);
        } else {
            showProductForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteProduct($id);
        break;
        
    default:
        listProducts();
        break;
}

function listProducts() {
    $categoryFilter = $_GET['kategori'] ?? '';
    
    $where = "1=1";
    $params = [];
    
    if ($categoryFilter) {
        $where .= " AND p.category_id = ?";
        $params[] = $categoryFilter;
    }
    
    $products = Database::fetchAll(
        "SELECT p.*, c.name as category_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id 
         WHERE {$where}
         ORDER BY p.sort_order ASC, p.created_at DESC",
        $params
    );
    
    $categories = Database::fetchAll("SELECT id, name FROM categories ORDER BY name");
    
    $pageTitle = 'Ürünler';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Ürünler</h1>
            <p><?= count($products) ?> ürün listeleniyor</p>
        </div>
        <a href="/panel/urunler/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Ürün
        </a>
    </div>
    
    <!-- Filtre -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 15px;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center;">
                <select name="kategori" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius);">
                    <option value="">Tüm Kategoriler</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($categoryFilter): ?>
                <a href="/panel/urunler" style="font-size: 13px;">Filtreyi Temizle</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($products)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:60px;">Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Sıra</th>
                        <th>Görüntülenme</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if ($product['main_image']): ?>
                            <img src="<?= asset($product['main_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                            <div style="width:50px;height:50px;background:#f3f4f6;border-radius:4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($product['name']) ?></strong>
                            <?php if ($product['is_featured']): ?>
                            <span class="badge badge-info" style="margin-left:5px;">Öne Çıkan</span>
                            <?php endif; ?>
                            <br>
                            <small style="color:#9ca3af;"><?= e($product['slug']) ?></small>
                        </td>
                        <td><?= e($product['category_name'] ?? '-') ?></td>
                        <td><?= $product['sort_order'] ?></td>
                        <td><?= number_format($product['view_count']) ?></td>
                        <td>
                            <?php if ($product['is_active']): ?>
                            <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge badge-error">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/panel/urunler/duzenle?id=<?= $product['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/urunler/sil?id=<?= $product['id'] ?>" class="action-btn delete" onclick="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz ürün eklenmemiş.</p>
                <a href="/panel/urunler/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Ürünü Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showProductForm($id = null) {
    $product = null;
    $pageTitle = 'Yeni Ürün';
    $gallery = [];
    
    if ($id) {
        $product = Database::fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
        if (!$product) {
            Session::flash('error', 'Ürün bulunamadı.');
            redirect('/panel/urunler');
        }
        $pageTitle = 'Ürün Düzenle';
        $gallery = Database::fetchAll("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order", [$id]);
    }
    
    $categories = Database::fetchAll(
        "SELECT c.id, c.name, p.name as parent_name 
         FROM categories c 
         LEFT JOIN categories p ON c.parent_id = p.id 
         ORDER BY p.name NULLS FIRST, c.name"
    );
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/urunler">← Ürünlere Dön</a></p>
    </div>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrfInput() ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <!-- Sol Kolon -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h3>Ürün Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Ürün Adı *</label>
                                <input type="text" id="name" name="name" value="<?= e($product['name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="slug">Slug *</label>
                                <input type="text" id="slug" name="slug" value="<?= e($product['slug'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Kategori *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= $cat['parent_name'] ? e($cat['parent_name']) . ' → ' : '' ?><?= e($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="short_description">Kısa Açıklama</label>
                            <textarea id="short_description" name="short_description" rows="3"><?= e($product['short_description'] ?? '') ?></textarea>
                            <small>Ürün listelerinde görünecek kısa açıklama</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_description">Detaylı Açıklama</label>
                            <textarea id="full_description" name="full_description" rows="8"><?= e($product['full_description'] ?? '') ?></textarea>
                            <small>HTML kullanabilirsiniz</small>
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3>SEO Ayarları</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="seo_title">SEO Başlık</label>
                            <input type="text" id="seo_title" name="seo_title" value="<?= e($product['seo_title'] ?? '') ?>" maxlength="70">
                            <small>Boş bırakılırsa ürün adı kullanılır</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">Meta Açıklama</label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($product['meta_description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="og_title">OG Başlık</label>
                                <input type="text" id="og_title" name="og_title" value="<?= e($product['og_title'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="canonical_url">Canonical URL</label>
                                <input type="text" id="canonical_url" name="canonical_url" value="<?= e($product['canonical_url'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_indexed" value="1" <?= ($product['is_indexed'] ?? 1) ? 'checked' : '' ?>>
                                <span>Arama motorlarında indexlensin</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Kolon -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h3>Görsel</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Ana Ürün Görseli</label>
                            <div class="file-upload">
                                <input type="file" name="main_image" accept="image/*">
                                <p>Görsel yüklemek için tıklayın</p>
                                <?php if (!empty($product['main_image'])): ?>
                                <div class="file-preview">
                                    <img src="<?= asset($product['main_image']) ?>" alt="">
                                </div>
                                <?php else: ?>
                                <div class="file-preview"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <label>Galeri Görselleri</label>
                            <input type="file" name="gallery[]" accept="image/*" multiple style="margin-top:10px;">
                            <small>Birden fazla görsel seçebilirsiniz</small>
                            
                            <?php if (!empty($gallery)): ?>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 15px;">
                                <?php foreach ($gallery as $img): ?>
                                <div style="position: relative;">
                                    <img src="<?= asset($img['image_path']) ?>" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px;">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3>Ayarlar</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="sort_order">Sıra</label>
                            <input type="number" id="sort_order" name="sort_order" value="<?= e($product['sort_order'] ?? 0) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <span>Aktif</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                <span>Öne Çıkan Ürün</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <?= $id ? 'Güncelle' : 'Ürün Ekle' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createProduct() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/urunler/ekle');
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description' => $_POST['full_description'] ?? '',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'og_title' => trim($_POST['og_title'] ?? ''),
        'canonical_url' => trim($_POST['canonical_url'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
    ];
    
    // Slug kontrolü
    $existing = Database::fetchOne("SELECT id FROM products WHERE slug = ?", [$data['slug']]);
    if ($existing) {
        Session::flash('error', 'Bu slug zaten kullanılıyor.');
        redirect('/panel/urunler/ekle');
    }
    
    // Ana görsel
    if (!empty($_FILES['main_image']['name'])) {
        $data['main_image'] = uploadImage($_FILES['main_image'], 'products');
    }
    
    $productId = Database::insert('products', $data);
    
    // Galeri görselleri
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $i => $name) {
            if (!empty($name)) {
                $file = [
                    'name' => $_FILES['gallery']['name'][$i],
                    'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                    'error' => $_FILES['gallery']['error'][$i],
                ];
                $path = uploadImage($file, 'products/gallery');
                if ($path) {
                    Database::insert('product_images', [
                        'product_id' => $productId,
                        'image_path' => $path,
                        'is_gallery' => 1,
                        'sort_order' => $i
                    ]);
                }
            }
        }
    }
    
    Session::flash('success', 'Ürün başarıyla eklendi.');
    redirect('/panel/urunler');
}

function updateProduct($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/urunler/duzenle?id=' . $id);
    }
    
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description' => $_POST['full_description'] ?? '',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'og_title' => trim($_POST['og_title'] ?? ''),
        'canonical_url' => trim($_POST['canonical_url'] ?? ''),
        'is_indexed' => isset($_POST['is_indexed']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    // Slug kontrolü
    $existing = Database::fetchOne("SELECT id FROM products WHERE slug = ? AND id != ?", [$data['slug'], $id]);
    if ($existing) {
        Session::flash('error', 'Bu slug zaten kullanılıyor.');
        redirect('/panel/urunler/duzenle?id=' . $id);
    }
    
    // Ana görsel
    if (!empty($_FILES['main_image']['name'])) {
        $data['main_image'] = uploadImage($_FILES['main_image'], 'products');
    }
    
    Database::update('products', $data, 'id = ?', [$id]);
    
    // Galeri görselleri
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $i => $name) {
            if (!empty($name)) {
                $file = [
                    'name' => $_FILES['gallery']['name'][$i],
                    'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                    'error' => $_FILES['gallery']['error'][$i],
                ];
                $path = uploadImage($file, 'products/gallery');
                if ($path) {
                    Database::insert('product_images', [
                        'product_id' => $id,
                        'image_path' => $path,
                        'is_gallery' => 1,
                        'sort_order' => $i
                    ]);
                }
            }
        }
    }
    
    Session::flash('success', 'Ürün güncellendi.');
    redirect('/panel/urunler');
}

function deleteProduct($id) {
    // Galeri görsellerini sil
    Database::delete('product_images', 'product_id = ?', [$id]);
    
    // Ürünü sil
    Database::delete('products', 'id = ?', [$id]);
    
    Session::flash('success', 'Ürün silindi.');
    redirect('/panel/urunler');
}

// uploadImage fonksiyonu CategoryController'da tanımlı, burada tekrar tanımlamaya gerek yok
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