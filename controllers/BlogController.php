<?php
/**
 * Blog Controller
 */

class BlogController
{
    /**
     * Blog listesi
     */
    public function index(): void
    {
        $page = (int) ($_GET['sayfa'] ?? 1);
        $perPage = 9;
        $offset = ($page - 1) * $perPage;
        
        // Toplam yazı sayısı
        $total = Database::fetchOne(
            "SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published' AND published_at <= datetime('now')"
        )['count'];
        
        $totalPages = ceil($total / $perPage);
        
        // Yazılar
        $posts = Database::fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
             FROM blog_posts bp 
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
             WHERE bp.status = 'published' AND bp.published_at <= datetime('now')
             ORDER BY bp.published_at DESC 
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
        // Kategoriler (sidebar için)
        $categories = Database::fetchAll(
            "SELECT bc.*, COUNT(bp.id) as post_count 
             FROM blog_categories bc 
             LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.status = 'published'
             WHERE bc.is_active = 1 
             GROUP BY bc.id 
             ORDER BY bc.sort_order ASC"
        );
        
        $seo = [
            'title' => 'Blog - ' . setting('site_name'),
            'description' => 'Etiket, baskı ve reklam sektöründen haberler ve bilgiler.',
            'canonical' => url('blog')
        ];
        
        view('layouts.main', [
            'content' => 'blog.index',
            'posts' => $posts,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Blog', 'url' => null]
            ]
        ]);
    }
    
    /**
     * Blog kategori
     */
    public function category(string $slug): void
    {
        $category = Database::fetchOne(
            "SELECT * FROM blog_categories WHERE slug = ? AND is_active = 1",
            [$slug]
        );
        
        if (!$category) {
            abort404();
        }
        
        $posts = Database::fetchAll(
            "SELECT * FROM blog_posts 
             WHERE category_id = ? AND status = 'published' AND published_at <= datetime('now')
             ORDER BY published_at DESC",
            [$category['id']]
        );
        
        $seo = [
            'title' => $category['name'] . ' - Blog - ' . setting('site_name'),
            'description' => $category['description'] ?: $category['name'] . ' kategorisindeki blog yazıları.',
            'canonical' => url('blog/kategori/' . $slug)
        ];
        
        view('layouts.main', [
            'content' => 'blog.category',
            'category' => $category,
            'posts' => $posts,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Blog', 'url' => '/blog'],
                ['title' => $category['name'], 'url' => null]
            ]
        ]);
    }
    
    /**
     * Blog detay
     */
    public function detail(string $slug): void
    {
        $post = Database::fetchOne(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
             FROM blog_posts bp 
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
             WHERE bp.slug = ? AND bp.status = 'published' AND bp.published_at <= datetime('now')",
            [$slug]
        );
        
        if (!$post) {
            abort404();
        }
        
        // Görüntülenme artır
        Database::query(
            "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?",
            [$post['id']]
        );
        
        // İlgili yazılar
        $relatedPosts = Database::fetchAll(
            "SELECT * FROM blog_posts 
             WHERE category_id = ? AND id != ? AND status = 'published' 
             ORDER BY published_at DESC LIMIT 3",
            [$post['category_id'], $post['id']]
        );
        
        $seo = [
            'title' => ($post['seo_title'] ?: $post['title']) . ' - ' . setting('site_name'),
            'description' => $post['meta_description'] ?: truncate(strip_tags($post['excerpt'] ?? $post['content']), 155),
            'canonical' => $post['canonical_url'] ?: url('blog/' . $slug),
            'og_title' => $post['og_title'] ?: $post['title'],
            'og_description' => $post['og_description'] ?: $post['meta_description'],
            'og_image' => $post['og_image'] ?: $post['featured_image'],
            'is_indexed' => $post['is_indexed']
        ];
        
        // Schema.org Article
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post['title'],
            'description' => strip_tags($post['excerpt'] ?? ''),
            'image' => $post['featured_image'] ? url($post['featured_image']) : null,
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name']
            ],
            'datePublished' => $post['published_at'],
            'dateModified' => $post['updated_at']
        ];
        
        view('layouts.main', [
            'content' => 'blog.detail',
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'seo' => $seo,
            'schema' => $schema,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Blog', 'url' => '/blog'],
                ['title' => $post['category_name'] ?? 'Genel', 'url' => $post['category_slug'] ? '/blog/kategori/' . $post['category_slug'] : null],
                ['title' => $post['title'], 'url' => null]
            ]
        ]);
    }
}