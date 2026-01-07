-- USD Reklam Veritabanı Şeması
-- SQLite uyumlu (MySQL için küçük değişiklikler gerekebilir)

-- =====================================================
-- AYARLAR
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_settings_key ON settings(setting_key);
CREATE INDEX idx_settings_group ON settings(setting_group);

-- =====================================================
-- KULLANICILAR (Admin)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    must_change_password TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- KATEGORİLER (Ürün Kategorileri - Nested)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    og_title VARCHAR(70),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    is_indexed TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE INDEX idx_categories_parent ON categories(parent_id);
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_active ON categories(is_active);

-- =====================================================
-- ÜRÜNLER
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    short_description TEXT,
    full_description TEXT,
    main_image VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    view_count INTEGER DEFAULT 0,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    og_title VARCHAR(70),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    is_indexed TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_featured ON products(is_featured);

-- =====================================================
-- ÜRÜN GÖRSELLERİ (Galeri)
-- =====================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(150),
    is_gallery TINYINT(1) DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX idx_product_images_product ON product_images(product_id);

-- =====================================================
-- HİZMETLER
-- =====================================================
CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    short_description TEXT,
    full_description TEXT,
    icon VARCHAR(50),
    main_image VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    view_count INTEGER DEFAULT 0,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    og_title VARCHAR(70),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    is_indexed TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_services_slug ON services(slug);
CREATE INDEX idx_services_active ON services(is_active);

-- =====================================================
-- HİZMET GÖRSELLERİ
-- =====================================================
CREATE TABLE IF NOT EXISTS service_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(150),
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

CREATE INDEX idx_service_images_service ON service_images(service_id);

-- =====================================================
-- BLOG KATEGORİLERİ
-- =====================================================
CREATE TABLE IF NOT EXISTS blog_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_blog_categories_slug ON blog_categories(slug);

-- =====================================================
-- BLOG YAZILARI
-- =====================================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT,
    featured_image VARCHAR(255),
    author_name VARCHAR(100) DEFAULT 'Admin',
    status VARCHAR(20) DEFAULT 'draft',
    published_at DATETIME,
    view_count INTEGER DEFAULT 0,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    og_title VARCHAR(70),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    is_indexed TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

CREATE INDEX idx_blog_posts_slug ON blog_posts(slug);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);
CREATE INDEX idx_blog_posts_category ON blog_posts(category_id);
CREATE INDEX idx_blog_posts_published ON blog_posts(published_at);

-- =====================================================
-- STATİK SAYFALAR
-- =====================================================
CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    content TEXT,
    template VARCHAR(50) DEFAULT 'default',
    is_active TINYINT(1) DEFAULT 1,
    -- SEO Alanları
    seo_title VARCHAR(70),
    meta_description VARCHAR(160),
    og_title VARCHAR(70),
    og_description VARCHAR(200),
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    is_indexed TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_pages_slug ON pages(slug);

-- =====================================================
-- SLIDER
-- =====================================================
CREATE TABLE IF NOT EXISTS sliders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(150),
    subtitle TEXT,
    button_text VARCHAR(50),
    button_url VARCHAR(255),
    image_desktop VARCHAR(255) NOT NULL,
    image_mobile VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    start_date DATETIME,
    end_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sliders_active ON sliders(is_active);
CREATE INDEX idx_sliders_order ON sliders(sort_order);

-- =====================================================
-- MENÜ
-- =====================================================
CREATE TABLE IF NOT EXISTS menu_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER DEFAULT NULL,
    menu_location VARCHAR(50) DEFAULT 'header',
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255),
    target VARCHAR(20) DEFAULT '_self',
    icon VARCHAR(50),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

CREATE INDEX idx_menu_location ON menu_items(menu_location);
CREATE INDEX idx_menu_parent ON menu_items(parent_id);

-- =====================================================
-- FOOTER
-- =====================================================
CREATE TABLE IF NOT EXISTS footer_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section VARCHAR(50) DEFAULT 'links',
    title VARCHAR(100),
    content TEXT,
    url VARCHAR(255),
    icon VARCHAR(50),
    sort_order INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_footer_section ON footer_items(section);

-- =====================================================
-- MÜŞTERİ YORUMLARI
-- =====================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    review_text TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    customer_image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_reviews_featured ON reviews(is_featured);
CREATE INDEX idx_reviews_active ON reviews(is_active);

-- =====================================================
-- TEKLİF TALEPLERİ
-- =====================================================
CREATE TABLE IF NOT EXISTS quote_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference_no VARCHAR(20) UNIQUE NOT NULL,
    request_type VARCHAR(20) NOT NULL,
    -- Müşteri Bilgileri
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    company_name VARCHAR(100),
    -- Ürün/Hizmet Bilgisi
    item_type VARCHAR(20),
    item_id INTEGER,
    item_name VARCHAR(150),
    -- Teklif Detayları
    quantity VARCHAR(100),
    dimensions VARCHAR(255),
    has_multiple_dimensions TINYINT(1) DEFAULT 0,
    additional_notes TEXT,
    -- Durum
    status VARCHAR(20) DEFAULT 'new',
    is_read TINYINT(1) DEFAULT 0,
    admin_notes TEXT,
    -- Takip
    ip_address VARCHAR(45),
    user_agent TEXT,
    -- Zaman
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_quotes_reference ON quote_requests(reference_no);
CREATE INDEX idx_quotes_type ON quote_requests(request_type);
CREATE INDEX idx_quotes_status ON quote_requests(status);
CREATE INDEX idx_quotes_email ON quote_requests(email);
CREATE INDEX idx_quotes_phone ON quote_requests(phone);
CREATE INDEX idx_quotes_created ON quote_requests(created_at);

-- =====================================================
-- RATE LIMITING
-- =====================================================
CREATE TABLE IF NOT EXISTS rate_limits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    attempts INTEGER DEFAULT 1,
    first_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_rate_ip_action ON rate_limits(ip_address, action_type);

-- =====================================================
-- 301 YÖNLENDİRMELER
-- =====================================================
CREATE TABLE IF NOT EXISTS redirects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    old_url VARCHAR(255) UNIQUE NOT NULL,
    new_url VARCHAR(255) NOT NULL,
    redirect_type INTEGER DEFAULT 301,
    hit_count INTEGER DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_redirects_old ON redirects(old_url);

-- =====================================================
-- VARSAYILAN VERİLER (SEED)
-- =====================================================

-- Admin Kullanıcı (şifre: 123456)
INSERT INTO users (username, password_hash, email, full_name, must_change_password) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'usdreklam@gmail.com', 'Site Yöneticisi', 1);

-- Genel Ayarlar
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'USD Reklam', 'general'),
('site_slogan', 'Kaliteli Etiket ve Reklam Çözümleri', 'general'),
('site_description', 'Metal etiket, plakat, madalyon ve dijital baskı çözümleri', 'general'),
('site_email', 'usdreklam@gmail.com', 'contact'),
('site_phone', '+90 555 123 4567', 'contact'),
('site_whatsapp', '905551234567', 'contact'),
('site_address', 'İstanbul, Türkiye', 'contact'),
('working_hours', 'Pazartesi - Cumartesi: 09:00 - 18:00', 'contact'),
('bank_info', 'Banka bilgileri buraya eklenecek', 'contact'),
('invoice_info', 'Fatura bilgileri buraya eklenecek', 'contact'),
('smtp_host', '', 'smtp'),
('smtp_port', '587', 'smtp'),
('smtp_user', '', 'smtp'),
('smtp_pass', '', 'smtp'),
('smtp_from_name', 'USD Reklam', 'smtp'),
('smtp_from_email', 'usdreklam@gmail.com', 'smtp'),
('smtp_encryption', 'tls', 'smtp'),
('google_analytics', '', 'seo'),
('facebook_url', '', 'social'),
('instagram_url', '', 'social'),
('twitter_url', '', 'social'),
('linkedin_url', '', 'social');

-- Ana Kategoriler
INSERT INTO categories (id, parent_id, name, slug, sort_order) VALUES
(1, NULL, 'Etiketler', 'etiketler', 1),
(2, NULL, 'Plaketler', 'plaketler', 2),
(3, NULL, 'Madalyonlar', 'madalyonlar', 3),
(4, NULL, 'Dijital Baskı Ürünleri', 'dijital-baski', 4),
(5, NULL, 'Uyarı Levhaları', 'uyari-levhalari', 5);

-- Alt Kategoriler (Etiketler)
INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
(1, 'Metal Etiket', 'metal-etiket', 1),
(1, 'Parfüm Etiket', 'parfum-etiket', 2),
(1, 'Leksan Etiket', 'leksan-etiket', 3),
(1, 'Sticker/PVC Etiket', 'sticker-pvc-etiket', 4),
(1, 'Panel Etiket', 'panel-etiket', 5),
(1, 'Deri/Jakron Etiket', 'deri-jakron-etiket', 6);

-- Alt Kategoriler (Plaketler)
INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
(2, 'Albüm Plaket', 'album-plaket', 1),
(2, 'Kristal Plaket', 'kristal-plaket', 2);

-- Alt Kategoriler (Dijital Baskı)
INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
(4, 'Branda Baskı', 'branda-baski', 1),
(4, 'Yer Folyosu', 'yer-folyosu', 2),
(4, 'Forex Dekota Levha Sıvama', 'forex-dekota-levha-sivama', 3);

-- Hizmetler
INSERT INTO services (name, slug, short_description, sort_order) VALUES
('Lazer Kesim / Kazıma', 'lazer-kesim-kazima', 'Hassas lazer kesim ve kazıma hizmetleri', 1),
('Serigrafi Baskı', 'serigrafi-baski', 'Profesyonel serigrafi baskı çözümleri', 2),
('UV Baskı', 'uv-baski', 'Yüksek kaliteli UV baskı hizmetleri', 3);

-- Örnek Statik Sayfalar
INSERT INTO pages (title, slug, template, content) VALUES
('Hakkımızda', 'hakkimizda', 'about', 'Hakkımızda içeriği buraya eklenecek...'),
('İletişim', 'iletisim', 'contact', 'İletişim sayfası içeriği...');

-- Örnek Blog Kategorisi
INSERT INTO blog_categories (name, slug, sort_order) VALUES
('Haberler', 'haberler', 1),
('Sektör', 'sektor', 2);

-- Örnek Menü (Header)
INSERT INTO menu_items (menu_location, title, url, sort_order) VALUES
('header', 'Ana Sayfa', '/', 1),
('header', 'Ürünler', '/urunler', 2),
('header', 'Hizmetler', '/hizmetler', 3),
('header', 'Blog', '/blog', 4),
('header', 'Hakkımızda', '/hakkimizda', 5),
('header', 'İletişim', '/iletisim', 6);

-- Örnek Yorum
INSERT INTO reviews (customer_name, company_name, review_text, rating, is_featured) VALUES
('Ahmet Yılmaz', 'ABC Şirketi', 'Çok kaliteli ve hızlı hizmet aldık. Teşekkürler!', 5, 1),
('Mehmet Kaya', 'XYZ Ltd.', 'Etiketlerimiz tam istediğimiz gibi oldu.', 5, 1);