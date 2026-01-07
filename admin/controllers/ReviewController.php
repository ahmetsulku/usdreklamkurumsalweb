<?php
/**
 * Admin Müşteri Yorumları Controller
 */

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'ekle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            createReview();
        } else {
            showReviewForm();
        }
        break;
        
    case 'duzenle':
        $id = (int) ($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            updateReview($id);
        } else {
            showReviewForm($id);
        }
        break;
        
    case 'sil':
        $id = (int) ($_GET['id'] ?? 0);
        deleteReview($id);
        break;
        
    default:
        listReviews();
        break;
}

function listReviews() {
    $reviews = Database::fetchAll("SELECT * FROM reviews ORDER BY sort_order ASC, created_at DESC");
    $pageTitle = 'Müşteri Yorumları';
    
    ob_start();
    ?>
    <div class="page-header page-header-actions">
        <div>
            <h1>Müşteri Yorumları</h1>
            <p>Ana sayfada görünecek müşteri yorumlarını yönetin</p>
        </div>
        <a href="/panel/yorumlar/ekle" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Yeni Yorum
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (!empty($reviews)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Müşteri</th>
                        <th>Firma</th>
                        <th>Yorum</th>
                        <th>Puan</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><strong><?= e($review['customer_name']) ?></strong></td>
                        <td><?= e($review['company_name'] ?: '-') ?></td>
                        <td style="max-width:300px;"><?= e(truncate($review['review_text'], 100)) ?></td>
                        <td>
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>⭐<?php endfor; ?>
                        </td>
                        <td>
                            <?php if ($review['is_featured']): ?>
                            <span class="badge badge-success">Öne Çıkan</span>
                            <?php else: ?>
                            <span class="badge badge-info">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="/panel/yorumlar/duzenle?id=<?= $review['id'] ?>" class="action-btn edit">Düzenle</a>
                                <a href="/panel/yorumlar/sil?id=<?= $review['id'] ?>" class="action-btn delete" onclick="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <p>Henüz yorum eklenmemiş.</p>
                <a href="/panel/yorumlar/ekle" class="btn btn-primary" style="margin-top:15px;">İlk Yorumu Ekle</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function showReviewForm($id = null) {
    $review = null;
    $pageTitle = 'Yeni Yorum';
    
    if ($id) {
        $review = Database::fetchOne("SELECT * FROM reviews WHERE id = ?", [$id]);
        if (!$review) {
            Session::flash('error', 'Yorum bulunamadı.');
            redirect('/panel/yorumlar');
        }
        $pageTitle = 'Yorum Düzenle';
    }
    
    ob_start();
    ?>
    <div class="page-header">
        <h1><?= $pageTitle ?></h1>
        <p><a href="/panel/yorumlar">← Yorumlara Dön</a></p>
    </div>
    
    <div class="card" style="max-width: 600px;">
        <div class="card-body">
            <form action="" method="POST">
                <?= csrfInput() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Müşteri Adı *</label>
                        <input type="text" id="customer_name" name="customer_name" value="<?= e($review['customer_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="company_name">Firma Adı</label>
                        <input type="text" id="company_name" name="company_name" value="<?= e($review['company_name'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="review_text">Yorum Metni *</label>
                    <textarea id="review_text" name="review_text" rows="4" required><?= e($review['review_text'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rating">Puan</label>
                        <select id="rating" name="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($review['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= $i ?> Yıldız</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sıra</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= e($review['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="is_featured" value="1" <?= ($review['is_featured'] ?? 1) ? 'checked' : '' ?>>
                        <span>Ana sayfada göster (Öne Çıkan)</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary"><?= $id ? 'Güncelle' : 'Yorum Ekle' ?></button>
            </form>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function createReview() {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/yorumlar/ekle');
    }
    
    $data = [
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'company_name' => trim($_POST['company_name'] ?? ''),
        'review_text' => trim($_POST['review_text'] ?? ''),
        'rating' => (int) ($_POST['rating'] ?? 5),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
    ];
    
    Database::insert('reviews', $data);
    Session::flash('success', 'Yorum eklendi.');
    redirect('/panel/yorumlar');
}

function updateReview($id) {
    if (!CSRF::verifyRequest()) {
        Session::flash('error', 'Güvenlik doğrulaması başarısız.');
        redirect('/panel/yorumlar/duzenle?id=' . $id);
    }
    
    $data = [
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'company_name' => trim($_POST['company_name'] ?? ''),
        'review_text' => trim($_POST['review_text'] ?? ''),
        'rating' => (int) ($_POST['rating'] ?? 5),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    
    Database::update('reviews', $data, 'id = ?', [$id]);
    Session::flash('success', 'Yorum güncellendi.');
    redirect('/panel/yorumlar');
}

function deleteReview($id) {
    Database::delete('reviews', 'id = ?', [$id]);
    Session::flash('success', 'Yorum silindi.');
    redirect('/panel/yorumlar');
}