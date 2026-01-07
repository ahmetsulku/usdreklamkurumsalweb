<?php
/**
 * Admin Blog Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createPost();
        } else {
            showPostForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updatePost($id);
        } else {
            showPostForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deletePost($id);
        break;
        
    default:
        listPosts();
        break;
}

function listPosts() {
    $posts = Database::fetchAll(
        "SELECT p.*, c.name as category_name 
         FROM blog_posts p 
         LEFT JOIN blog_categories c ON p.category_id = c.id 
         ORDER BY p.created_at DESC"
    );
    $pageTitle = 'Blog Yazıları';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Blog Yazıları</h1>
            <p><?= count($posts) ?> yazı listeleniyor</p>
        </div>
        <a href="/panel/blog/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Yazı
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($posts)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:60px;">Görsel</th>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Görüntülenme</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <?php if ($post['featured_image']): ?>
                            <img src="<?= asset($post['featured_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                            <div style="width:50px;height:50px;background:#f3f4f6;border-radius:4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($post['title']) ?></strong>
                            <br><small style="color:#9ca3af;"><?= e($post['slug']) ?></small>
                        </td>
                        <td><?= e($post['category_name'] ?? '-') ?></td>
                        <td><?= number_format($post['view_count']) ?></td>
                        <td><?= formatDate($post['published_at'] ?? $post['created_at'], 'd.m.Y') ?></td>
                        <td>
                            <?php if ($post['is_published']): ?>
                            <span class="badge badge-success">Yayında</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Taslak</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/panel/blog/duzenle?id=<?= $post['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/blog/sil?id=<?= $post['id'] ?>" class="action-btn delete" onclick="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz blog yazısı eklenmemiş.</p>
                <a href="/panel/blog/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Yazıyı Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showPostForm($id = null) {
    $post = null;
    $pageTitle = 'Yeni Blog Yazısı';
    
    if ($id) {
        $post = Database::fetchOne("SELECT * FROM blog_posts WHERE id = ?", [$id]);
        if (!$post) {
            Session::flash('error', 'Yazı bulunamadı.');
            redirect('/panel/blog');
        }
        $pageTitle = 'Yazı Düzenle';
    }
    
    $categories = Database::fetchAll("SELECT * FROM blog_categories ORDER BY name");
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/blog">← Blog Yazılarına Dön</a></p>
    </div>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <?= csrfInput() ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <div>
                <div class="card">
                    <div class="card-header"><h3>Yazı İçeriği</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Başlık *</label>
                            <input type="text" id="title" name="title" value="<?= e($post['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug *</label>
                            <input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="excerpt">Özet</label>
                            <textarea id="excerpt" name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
                            <small>Yazı listelerinde görünecek kısa özet</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">İçerik *</label>
                            <textarea id="content" name="content" rows="15"><?= e($post['content'] ?? '') ?></textarea>
                            <small>HTML kullanabilirsiniz</small>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header"><h3>SEO Ayarları</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="seo_title">SEO Başlık</label>
                            <input type="text" id="seo_title" name="seo_title" value="<?= e($post['seo_title'] ?? '') ?>" maxlength="70">
                        </div>
                        <div class="form-group">
                            <label for="meta_description">Meta Açıklama</label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="160"><?= e($post['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card">
                    <div class="card-header"><h3>Yayın Ayarları</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select id="category_id" name="category_id">
                                <option value="">Kategori Seçin</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="published_at">Yayın Tarihi</label>
                            <input type="datetime-local" id="published_at" name="published_at" value="<?= $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_published" value="1" <?= ($post['is_published'] ?? 0) ? 'checked' : '' ?>>
                                <span>Yayında</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header"><h3>Öne Çıkan Görsel</h3></div>
                    <div class="card-body">
                        <div class="file-upload">
                            <input type="file" name="featured_image" accept="image/*">
                            <p>Görsel yüklemek için tıklayın</p>
                            <?php if (!empty($post['featured_image'])): ?>
                            <div class="file-preview"><img src="<?= asset($post['featured_image']) ?>" alt=""></div>
                            <?php else: ?>
                            <div class="file-preview"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    <?= $id ? 'Güncelle' : 'Yazı Ekle' ?>
                </button>
            </div>
        </div>
    </form>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createPost() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/blog/ekle');
    }
    
    $admin = getAdmin();
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
        'author_id' => $admin['id'],
        'published_at' => $_POST['published_at'] ?? date('Y-m-d H:i:s'),
        'is_published' => isset($_POST['is_published']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
    ];
    
    if (!empty($_FILES['featured_image']['name'])) {
        $data['featured_image'] = uploadImage($_FILES['featured_image'], 'blog');
    }
    
    Database::insert('blog_posts', $data);
    Session::flash('success', 'Yazı başarıyla eklendi.');
    redirect('/panel/blog');
}

function updatePost($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/blog/duzenle?id=' . $id);
    }
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
        'published_at' => $_POST['published_at'] ?? date('Y-m-d H:i:s'),
        'is_published' => isset($_POST['is_published']) ? 1 : 0,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    if (!empty($_FILES['featured_image']['name'])) {
        $data['featured_image'] = uploadImage($_FILES['featured_image'], 'blog');
    }
    
    Database::update('blog_posts', $data, 'id = ?', [$id]);
    Session::flash('success', 'Yazı güncellendi.');
    redirect('/panel/blog');
}

function deletePost($id) {
    Database::delete('blog_posts', 'id = ?', [$id]);
    Session::flash('success', 'Yazı silindi.');
    redirect('/panel/blog');
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