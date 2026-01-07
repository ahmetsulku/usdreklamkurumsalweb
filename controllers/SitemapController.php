<?php
/**
 * Sitemap ve Robots.txt Controller
 */

class SitemapController
{
    /**
     * sitemap.xml
     */
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Ana sayfa
        $this->addUrl(SITE_URL, date('Y-m-d'), 'daily', '1.0');
        
        // Statik sayfalar
        $this->addUrl(url('hakkimizda'), null, 'monthly', '0.8');
        $this->addUrl(url('iletisim'), null, 'monthly', '0.8');
        $this->addUrl(url('urunler'), null, 'weekly', '0.9');
        $this->addUrl(url('hizmetler'), null, 'weekly', '0.9');
        $this->addUrl(url('blog'), null, 'daily', '0.8');
        
        // Kategoriler
        $categories = Database::fetchAll(
            "SELECT slug, updated_at FROM categories WHERE is_active = 1 AND is_indexed = 1"
        );
        foreach ($categories as $cat) {
            $this->addUrl(url($cat['slug']), $cat['updated_at'], 'weekly', '0.8');
        }
        
        // Ürünler
        $products = Database::fetchAll(
            "SELECT p.slug, p.updated_at, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_active = 1 AND p.is_indexed = 1"
        );
        foreach ($products as $prod) {
            $this->addUrl(url($prod['category_slug'] . '/' . $prod['slug']), $prod['updated_at'], 'weekly', '0.7');
        }
        
        // Hizmetler
        $services = Database::fetchAll(
            "SELECT slug, updated_at FROM services WHERE is_active = 1 AND is_indexed = 1"
        );
        foreach ($services as $srv) {
            $this->addUrl(url('hizmetler/' . $srv['slug']), $srv['updated_at'], 'monthly', '0.7');
        }
        
        // Blog yazıları
        $posts = Database::fetchAll(
            "SELECT slug, updated_at FROM blog_posts WHERE status = 'published' AND is_indexed = 1"
        );
        foreach ($posts as $post) {
            $this->addUrl(url('blog/' . $post['slug']), $post['updated_at'], 'monthly', '0.6');
        }
        
        echo '</urlset>';
        exit;
    }
    
    /**
     * URL ekle
     */
    private function addUrl(string $loc, ?string $lastmod, string $changefreq, string $priority): void
    {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
        if ($lastmod) {
            echo "    <lastmod>" . date('Y-m-d', strtotime($lastmod)) . "</lastmod>\n";
        }
        echo "    <changefreq>{$changefreq}</changefreq>\n";
        echo "    <priority>{$priority}</priority>\n";
        echo "  </url>\n";
    }
    
    /**
     * robots.txt
     */
    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "\n";
        echo "# Admin paneli engelle\n";
        echo "Disallow: /panel/\n";
        echo "Disallow: /admin/\n";
        echo "\n";
        echo "# API endpoints\n";
        echo "Disallow: /api/\n";
        echo "\n";
        echo "# Sitemap\n";
        echo "Sitemap: " . SITE_URL . "/sitemap.xml\n";
        
        exit;
    }
}