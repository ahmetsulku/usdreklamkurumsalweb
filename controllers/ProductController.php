<?php
/**
 * Ürün Controller
 */

class ProductController
{
    /**
     * Tüm ürünler sayfası
     */
    public function index(): void
    {
        $categories = Database::fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) as product_count
             FROM categories c 
             WHERE c.parent_id IS NULL AND c.is_active = 1 
             ORDER BY c.sort_order ASC"
        );
        
        foreach ($categories as &$category) {
            $category['subcategories'] = Database::fetchAll(
                "SELECT sc.*, 
                        (SELECT COUNT(*) FROM products p WHERE p.category_id = sc.id AND p.is_active = 1) as product_count
                 FROM categories sc 
                 WHERE sc.parent_id = ? AND sc.is_active = 1 
                 ORDER BY sc.sort_order ASC",
                [$category['id']]
            );
        }
        
        $seo = [
            'title' => 'Ürünlerimiz - ' . setting('site_name'),
            'description' => 'Metal etiket, plakat, madalyon ve dijital baskı ürünlerimizi inceleyin.',
            'canonical' => url('urunler')
        ];
        
        view('layouts.main', [
            'content' => 'products.index',
            'categories' => $categories,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Ürünler', 'url' => null]
            ]
        ]);
    }
    
    /**
     * Kategori sayfası
     */
    public function category(string $categorySlug): void
    {
        // Önce ana kategori mi alt kategori mi kontrol et
        $category = Database::fetchOne(
            "SELECT * FROM categories WHERE slug = ? AND is_active = 1",
            [$categorySlug]
        );
        
        if (!$category) {
            abort404();
        }
        
        // Alt kategoriler
        $subcategories = Database::fetchAll(
            "SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order ASC",
            [$category['id']]
        );
        
        // Ürünler - bu kategoride veya alt kategorilerinde
        $categoryIds = [$category['id']];
        foreach ($subcategories as $sub) {
            $categoryIds[] = $sub['id'];
        }
        
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $products = Database::fetchAll(
            "SELECT p.*, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id IN ({$placeholders}) AND p.is_active = 1 
             ORDER BY p.sort_order ASC",
            $categoryIds
        );
        
        // Üst kategori (breadcrumb için)
        $parentCategory = null;
        if ($category['parent_id']) {
            $parentCategory = Database::fetchOne(
                "SELECT * FROM categories WHERE id = ?",
                [$category['parent_id']]
            );
        }
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Ana Sayfa', 'url' => '/'],
            ['title' => 'Ürünler', 'url' => '/urunler']
        ];
        
        if ($parentCategory) {
            $breadcrumbs[] = ['title' => $parentCategory['name'], 'url' => '/' . $parentCategory['slug']];
        }
        $breadcrumbs[] = ['title' => $category['name'], 'url' => null];
        
        // SEO
        $seo = [
            'title' => ($category['seo_title'] ?: $category['name']) . ' - ' . setting('site_name'),
            'description' => $category['meta_description'] ?: truncate(strip_tags($category['description'] ?? ''), 155),
            'canonical' => url($categorySlug),
            'og_title' => $category['og_title'] ?: $category['name'],
            'og_description' => $category['og_description'] ?: $category['meta_description'],
            'og_image' => $category['og_image'] ?: $category['image'],
            'is_indexed' => $category['is_indexed']
        ];
        
        view('layouts.main', [
            'content' => 'products.category',
            'category' => $category,
            'subcategories' => $subcategories,
            'products' => $products,
            'parentCategory' => $parentCategory,
            'seo' => $seo,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
    
    /**
     * Ürün detay sayfası
     */
    public function detail(string $categorySlug, string $productSlug): void
    {
        // Kategori
        $category = Database::fetchOne(
            "SELECT * FROM categories WHERE slug = ? AND is_active = 1",
            [$categorySlug]
        );
        
        if (!$category) {
            abort404();
        }
        
        // Ürün
        $product = Database::fetchOne(
            "SELECT * FROM products WHERE slug = ? AND is_active = 1",
            [$productSlug]
        );
        
        if (!$product) {
            abort404();
        }
        
        // Görüntülenme sayısını artır
        Database::query(
            "UPDATE products SET view_count = view_count + 1 WHERE id = ?",
            [$product['id']]
        );
        
        // Ürün görselleri (galeri)
        $gallery = Database::fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC",
            [$product['id']]
        );
        
        // Üst kategori
        $parentCategory = null;
        if ($category['parent_id']) {
            $parentCategory = Database::fetchOne(
                "SELECT * FROM categories WHERE id = ?",
                [$category['parent_id']]
            );
        }
        
        // İlgili ürünler
        $relatedProducts = Database::fetchAll(
            "SELECT p.*, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
             ORDER BY RANDOM() 
             LIMIT 4",
            [$product['category_id'], $product['id']]
        );
        
        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Ana Sayfa', 'url' => '/'],
            ['title' => 'Ürünler', 'url' => '/urunler']
        ];
        
        if ($parentCategory) {
            $breadcrumbs[] = ['title' => $parentCategory['name'], 'url' => '/' . $parentCategory['slug']];
        }
        $breadcrumbs[] = ['title' => $category['name'], 'url' => '/' . $category['slug']];
        $breadcrumbs[] = ['title' => $product['name'], 'url' => null];
        
        // SEO
        $seo = [
            'title' => ($product['seo_title'] ?: $product['name']) . ' - ' . setting('site_name'),
            'description' => $product['meta_description'] ?: truncate(strip_tags($product['short_description'] ?? ''), 155),
            'canonical' => $product['canonical_url'] ?: url($categorySlug . '/' . $productSlug),
            'og_title' => $product['og_title'] ?: $product['name'],
            'og_description' => $product['og_description'] ?: $product['meta_description'],
            'og_image' => $product['og_image'] ?: $product['main_image'],
            'is_indexed' => $product['is_indexed']
        ];
        
        // Schema.org Product
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => strip_tags($product['short_description'] ?? ''),
            'image' => $product['main_image'] ? url($product['main_image']) : null,
            'brand' => [
                '@type' => 'Brand',
                'name' => setting('site_name')
            ]
        ];
        
        view('layouts.main', [
            'content' => 'products.detail',
            'product' => $product,
            'category' => $category,
            'parentCategory' => $parentCategory,
            'gallery' => $gallery,
            'relatedProducts' => $relatedProducts,
            'seo' => $seo,
            'schema' => $schema,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}