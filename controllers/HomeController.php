<?php
/**
 * Ana Sayfa Controller
 */

class HomeController
{
    public function index(): void
    {
        // Slider
        $sliders = Database::fetchAll(
            "SELECT * FROM sliders WHERE is_active = 1 
             AND (start_date IS NULL OR start_date <= datetime('now'))
             AND (end_date IS NULL OR end_date >= datetime('now'))
             ORDER BY sort_order ASC"
        );
        
        // Öne çıkan ürünler
        $featuredProducts = Database::fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_active = 1 AND p.is_featured = 1 
             ORDER BY p.sort_order ASC 
             LIMIT 8"
        );
        
        // Tüm kategoriler ve ürünleri (ana sayfa için)
        $categories = Database::fetchAll(
            "SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order ASC"
        );
        
        // Her kategorinin alt kategorilerini ve ürünlerini al
        foreach ($categories as &$category) {
            $category['subcategories'] = Database::fetchAll(
                "SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order ASC",
                [$category['id']]
            );
            
            // Kategorideki ürünler
            $categoryIds = [$category['id']];
            foreach ($category['subcategories'] as $sub) {
                $categoryIds[] = $sub['id'];
            }
            
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $category['products'] = Database::fetchAll(
                "SELECT * FROM products WHERE category_id IN ({$placeholders}) AND is_active = 1 ORDER BY sort_order ASC LIMIT 6",
                $categoryIds
            );
        }
        
        // Hizmetler
        $services = Database::fetchAll(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6"
        );
        
        // Müşteri yorumları
        $reviews = Database::fetchAll(
            "SELECT * FROM reviews WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT 6"
        );
        
        // Son blog yazıları
        $latestPosts = Database::fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
             FROM blog_posts bp 
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
             WHERE bp.status = 'published' AND bp.published_at <= datetime('now')
             ORDER BY bp.published_at DESC 
             LIMIT 3"
        );
        
        // SEO verileri
        $seo = [
            'title' => setting('site_name') . ' - ' . setting('site_slogan'),
            'description' => setting('site_description'),
            'canonical' => SITE_URL
        ];
        
        // View'a gönder
        view('layouts.main', [
            'content' => 'home.index',
            'sliders' => $sliders,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'services' => $services,
            'reviews' => $reviews,
            'latestPosts' => $latestPosts,
            'seo' => $seo
        ]);
    }
}