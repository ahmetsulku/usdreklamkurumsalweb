<?php
/**
 * Teklif Bildirimi E-posta Template'i
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Teklif Talebi</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #2563eb; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">
                                Yeni Teklif Talebi
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Referans No -->
                            <div style="background-color: #f0f9ff; border-left: 4px solid #2563eb; padding: 15px 20px; margin-bottom: 30px;">
                                <p style="margin: 0; font-size: 14px; color: #64748b;">Referans Numarası</p>
                                <p style="margin: 5px 0 0; font-size: 20px; font-weight: 600; color: #1e40af;"><?= e($reference_no ?? '-') ?></p>
                            </div>
                            
                            <!-- Talep Türü -->
                            <div style="margin-bottom: 25px;">
                                <p style="margin: 0 0 5px; font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Talep Türü</p>
                                <p style="margin: 0; font-size: 16px; color: #1f2937; font-weight: 500;"><?= e($request_type ?? '-') ?></p>
                            </div>
                            
                            <!-- Müşteri Bilgileri -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; width: 140px;">
                                        <span style="font-size: 14px; color: #64748b;">Ad / Firma</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;"><?= e($name ?? '-') ?></span>
                                    </td>
                                </tr>
                                
                                <?php if (!empty($phone)): ?>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #64748b;">Telefon</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <a href="tel:<?= e($phone) ?>" style="font-size: 14px; color: #2563eb; text-decoration: none; font-weight: 500;"><?= e($phone) ?></a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if (!empty($email)): ?>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #64748b;">E-posta</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <a href="mailto:<?= e($email) ?>" style="font-size: 14px; color: #2563eb; text-decoration: none; font-weight: 500;"><?= e($email) ?></a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #64748b;">Ürün / Hizmet</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;"><?= e($item_name ?? '-') ?></span>
                                    </td>
                                </tr>
                                
                                <?php if (!empty($quantity)): ?>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #64748b;">Adet</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #1f2937;"><?= e($quantity) ?></span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if (!empty($dimensions)): ?>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #64748b;">Ölçü</span>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <span style="font-size: 14px; color: #1f2937;"><?= e($dimensions) ?></span>
                                        <?php if (!empty($has_multiple_dimensions)): ?>
                                        <br><span style="font-size: 12px; color: #f59e0b;">(Farklı ölçüler isteniyor)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="font-size: 14px; color: #64748b;">Tarih</span>
                                    </td>
                                    <td style="padding: 12px 0;">
                                        <span style="font-size: 14px; color: #1f2937;"><?= e($date ?? date('d.m.Y H:i')) ?></span>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($notes)): ?>
                            <!-- Notlar -->
                            <div style="background-color: #fefce8; border: 1px solid #fde68a; border-radius: 6px; padding: 15px; margin-bottom: 25px;">
                                <p style="margin: 0 0 8px; font-size: 12px; text-transform: uppercase; color: #92400e; letter-spacing: 0.5px;">Ek Notlar</p>
                                <p style="margin: 0; font-size: 14px; color: #78350f;"><?= nl2br(e($notes)) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- CTA -->
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="<?= SITE_URL ?>/panel/teklif-talepleri" 
                                   style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-size: 14px; font-weight: 500;">
                                    Panelde Görüntüle
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 25px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #6b7280;">
                                <?= e(setting('site_name')) ?>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                Bu e-posta <?= SITE_URL ?> adresinden otomatik olarak gönderilmiştir.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>