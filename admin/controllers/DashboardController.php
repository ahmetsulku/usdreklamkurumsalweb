<?php
/**
 * Admin Dashboard Controller
 */

// İstatistikler
$stats = [
    'products' => Database::fetchOne("SELECT COUNT(*) as count FROM products")['count'],
    'services' => Database::fetchOne("SELECT COUNT(*) as count FROM services")['count'],
    'categories' => Database::fetchOne("SELECT COUNT(*) as count FROM categories")['count'],
    'blog_posts' => Database::fetchOne("SELECT COUNT(*) as count FROM blog_posts")['count'],
    'quotes_total' => Database::fetchOne("SELECT COUNT(*) as count FROM quote_requests")['count'],
    'quotes_new' => Database::fetchOne("SELECT COUNT(*) as count FROM quote_requests WHERE is_read = 0")['count'],
];

// Son teklif talepleri
$recentQuotes = Database::fetchAll(
    "SELECT * FROM quote_requests ORDER BY created_at DESC LIMIT 5"
);

// Son blog yazıları
$recentPosts = Database::fetchAll(
    "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5"
);

require __DIR__ . '/../views/dashboard.php';