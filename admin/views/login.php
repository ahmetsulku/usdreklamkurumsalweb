<?php
/**
 * Admin Giriş Sayfası
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - <?= e(setting('site_name')) ?> Panel</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #111827 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .login-header p {
            color: #6b7280;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 14px;
            color: #374151;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 500;
            color: #fff;
            background: #2563eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
            text-decoration: none;
        }
        .back-link:hover {
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <h1><?= e(setting('site_name')) ?></h1>
            <p>Yönetim Paneli</p>
        </div>
        
        <?php 
        $flash = Session::getAllFlash();
        if (isset($flash['error'])): 
        ?>
        <div class="alert alert-error"><?= e($flash['error']) ?></div>
        <?php endif; ?>
        
        <?php if (isset($flash['warning'])): ?>
        <div class="alert alert-warning"><?= e($flash['warning']) ?></div>
        <?php endif; ?>
        
        <form action="/panel/giris-yap" method="POST">
            <?= csrfInput() ?>
            
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Giriş Yap</button>
        </form>
        
        <a href="/" class="back-link">← Siteye Dön</a>
    </div>
</body>
</html>