<?php
/**
 * Hizmet Controller
 */

class ServiceController
{
    /**
     * Hizmetler listesi
     */
    public function index(): void
    {
        $services = Database::fetchAll(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC"
        );
        
        $seo = [
            'title' => 'Hizmetlerimiz - ' . setting('site_name'),
            'description' => 'Lazer kesim, serigrafi baskı ve UV baskı hizmetlerimizi keşfedin.',
            'canonical' => url('hizmetler')
        ];
        
        view('layouts.main', [
            'content' => 'services.index',
            'services' => $services,
            'seo' => $seo,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Hizmetler', 'url' => null]
            ]
        ]);
    }
    
    /**
     * Hizmet detay
     */
    public function detail(string $slug): void
    {
        $service = Database::fetchOne(
            "SELECT * FROM services WHERE slug = ? AND is_active = 1",
            [$slug]
        );
        
        if (!$service) {
            abort404();
        }
        
        // Görüntülenme artır
        Database::query(
            "UPDATE services SET view_count = view_count + 1 WHERE id = ?",
            [$service['id']]
        );
        
        // Hizmet görselleri
        $gallery = Database::fetchAll(
            "SELECT * FROM service_images WHERE service_id = ? ORDER BY sort_order ASC",
            [$service['id']]
        );
        
        // Diğer hizmetler
        $otherServices = Database::fetchAll(
            "SELECT * FROM services WHERE id != ? AND is_active = 1 ORDER BY sort_order ASC LIMIT 3",
            [$service['id']]
        );
        
        $seo = [
            'title' => ($service['seo_title'] ?: $service['name']) . ' - ' . setting('site_name'),
            'description' => $service['meta_description'] ?: truncate(strip_tags($service['short_description'] ?? ''), 155),
            'canonical' => $service['canonical_url'] ?: url('hizmetler/' . $slug),
            'og_title' => $service['og_title'] ?: $service['name'],
            'og_description' => $service['og_description'] ?: $service['meta_description'],
            'og_image' => $service['og_image'] ?: $service['main_image'],
            'is_indexed' => $service['is_indexed']
        ];
        
        // Schema.org Service
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service['name'],
            'description' => strip_tags($service['short_description'] ?? ''),
            'provider' => [
                '@type' => 'Organization',
                'name' => setting('site_name')
            ]
        ];
        
        view('layouts.main', [
            'content' => 'services.detail',
            'service' => $service,
            'gallery' => $gallery,
            'otherServices' => $otherServices,
            'seo' => $seo,
            'schema' => $schema,
            'breadcrumbs' => [
                ['title' => 'Ana Sayfa', 'url' => '/'],
                ['title' => 'Hizmetler', 'url' => '/hizmetler'],
                ['title' => $service['name'], 'url' => null]
            ]
        ]);
    }
}