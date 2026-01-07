<?php
/**
 * Şifre Değiştirme Sayfası
 */
$admin = getAdmin();
$mustChange = $admin['must_change_password'] ?? false;
$pageTitle = 'Şifre Değiştir';

ob_start();
?>

<div class="page-header">
    <h1>Şifre Değiştir</h1>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <?php if ($mustChange): ?>
        <div class="alert alert-warning" style="margin-bottom: 20px;">
            <strong>Dikkat!</strong> Güvenliğiniz için varsayılan şifrenizi değiştirmeniz gerekmektedir.
        </div>
        <?php endif; ?>
        
        <form action="/panel/sifre-degistir" method="POST">
            <?= csrfInput() ?>
            
            <?php if (!$mustChange): ?>
            <div class="form-group">
                <label for="current_password">Mevcut Şifre</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="new_password">Yeni Şifre</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
                <small style="color: #6b7280;">En az 6 karakter</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Şifreyi Değiştir</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>