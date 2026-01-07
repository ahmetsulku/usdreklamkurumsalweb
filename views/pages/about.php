<?php
/**
 * Hakkımızda Sayfası
 */
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title"><?= e($page['title']) ?></h1>
        </div>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="content" style="font-size: 17px; line-height: 1.8;">
                <?= $page['content'] ?>
            </div>
            
            <!-- Neden Biz -->
            <div style="margin-top: 50px; padding-top: 40px; border-top: 1px solid var(--border);">
                <h2 style="text-align: center; margin-bottom: 30px;">Neden USD Reklam?</h2>
                <div class="grid grid-3" style="gap: 30px;">
                    <div style="text-align: center;">
                        <div style="width: 70px; height: 70px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #fff;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="7"/>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 18px; margin-bottom: 10px;">Kaliteli Üretim</h3>
                        <p style="color: var(--text-light); font-size: 14px;">En son teknoloji ile yüksek kaliteli ürünler üretiyoruz.</p>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 70px; height: 70px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #fff;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 18px; margin-bottom: 10px;">Hızlı Teslimat</h3>
                        <p style="color: var(--text-light); font-size: 14px;">Siparişlerinizi en kısa sürede teslim ediyoruz.</p>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 70px; height: 70px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #fff;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 18px; margin-bottom: 10px;">Müşteri Memnuniyeti</h3>
                        <p style="color: var(--text-light); font-size: 14px;">Müşteri memnuniyeti bizim için her şeyden önemli.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA -->
        <div style="text-align: center; margin-top: 50px; padding: 40px; background: var(--bg-alt); border-radius: var(--radius-lg);">
            <h3 style="margin-bottom: 15px;">Projeleriniz İçin Bize Ulaşın</h3>
            <p style="color: var(--text-light); margin-bottom: 20px;">Etiket, plakat ve baskı ihtiyaçlarınız için hemen teklif alın.</p>
            <a href="/iletisim" class="btn btn-primary btn-lg">İletişime Geçin</a>
        </div>
    </div>
</section>