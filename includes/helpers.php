<?php
declare(strict_types=1);

function boot_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $path = __DIR__ . '/../config/config.php';
        $config = file_exists($path) ? require $path : [];
    }
    return $config;
}

function app_installed(): bool
{
    return (bool) (app_config()['installed'] ?? false);
}

function site_base_url(): string
{
    return rtrim((string) (app_config()['site']['base_url'] ?? ''), '/');
}

function base_path(): string
{
    $baseUrl = site_base_url();
    if ($baseUrl !== '') {
        $path = parse_url($baseUrl, PHP_URL_PATH) ?: '';
        return rtrim((string) $path, '/');
    }
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
        return '';
    }
    if (str_ends_with($scriptDir, '/admin')) {
        $scriptDir = substr($scriptDir, 0, -6);
    }
    return rtrim($scriptDir, '/');
}

function url_for(string $path = ''): string
{
    $base = base_path();
    $clean = '/' . ltrim($path, '/');
    if ($clean === '/' || $clean === '') {
        return ($base !== '' ? $base : '') . '/';
    }
    return ($base !== '' ? $base : '') . $clean;
}

function asset_url(string $path): string
{
    return url_for($path);
}

function route_url(string $slug = ''): string
{
    if ($slug === '' || $slug === 'home') {
        return url_for('index.php');
    }
    return url_for('index.php?route=' . rawurlencode($slug));
}

function current_year(): string
{
    return date('Y');
}

function redirect_to(string $path): never
{
    if (!preg_match('#^(https?:|tel:|mailto:)#i', $path)) {
        $path = url_for($path);
    }
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    boot_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    boot_session();
    $token = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
        http_response_code(419);
        exit('Jeton CSRF invalide');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    boot_session();
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function rate_limit_passed(string $name, int $seconds = 10): bool
{
    boot_session();
    $key = 'rate_limit_' . $name;
    $last = (int) ($_SESSION[$key] ?? 0);
    if ((time() - $last) < $seconds) {
        return false;
    }
    $_SESSION[$key] = time();
    return true;
}

function setting(string $key, ?string $fallback = null): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            if (function_exists('db_fetch_all')) {
                foreach (db_fetch_all('SELECT setting_key, setting_value FROM settings') as $row) {
                    $cache[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
                }
            }
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    $value = $cache[$key] ?? null;
    return ($value === null || $value === '') ? (string) ($fallback ?? '') : (string) $value;
}

function site_setting(string $key, ?string $fallback = null): string
{
    return setting($key, $fallback);
}

function set_setting(string $key, mixed $value): void
{
    $stringValue = is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $exists = db_fetch('SELECT id FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        db_execute('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$stringValue, $key]);
    } else {
        db_execute('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $stringValue]);
    }
}

function setting_bool(string $key, bool $fallback = false): bool
{
    $value = strtolower(setting($key, $fallback ? '1' : '0'));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
}

function get_json_setting(string $key, array $fallback = []): array
{
    $decoded = json_decode(setting($key, ''), true);
    return is_array($decoded) ? $decoded : $fallback;
}

function set_json_setting(string $key, array $value): void
{
    set_setting($key, json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function css_value(string $value, string $fallback = ''): string
{
    $value = trim($value);
    if ($value === '') return $fallback;
    if ($value === 'auto') return 'auto';
    if (preg_match('/^-?\d+(\.\d+)?$/', $value)) return $value . 'px';
    if (preg_match('/^-?\d+(\.\d+)?(px|rem|em|%|vh|vw)$/', $value)) return $value;
    return $fallback;
}

function upload_image_field(string $field, string $dir = 'gallery'): ?string
{
    if (empty($_FILES[$field]['name'])) return null;
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
    $tmp = $_FILES[$field]['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) return null;
    $mime = mime_content_type($tmp) ?: '';
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/svg+xml'=>'svg'];
    if (!isset($allowed[$mime])) return null;
    $uploadDir = __DIR__ . '/../storage/uploads/' . trim($dir, '/');
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $target = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($tmp, $target)) return null;
    $path = 'storage/uploads/' . trim($dir, '/') . '/' . $filename;
    db_execute('INSERT INTO media (file_path, alt_text, category) VALUES (?, ?, ?)', [$path, '', $dir]);
    return $path;
}

function company_name(): string { return setting('company_name', 'EMAE'); }
function company_phone(): string { return setting('company_phone', '06 67 83 03 76'); }
function company_phone_link(): string { return setting('company_phone_link', 'tel:+33667830376'); }
function company_email(): string { return setting('company_email', 'contact@emae.fr'); }
function company_regions(): string { return setting('company_regions', 'Île-de-France et Occitanie'); }
function company_hours(): string { return setting('company_hours', '24h/24 - 7j/7'); }
function company_address(): string { return setting('company_address', 'Île-de-France et Occitanie'); }
function company_siret(): string { return setting('company_siret', ''); }
function site_logo_path(): string { return setting('site_logo', 'storage/uploads/logos/logo-emae-default.svg'); }
function site_logo_url(): string { return asset_url(site_logo_path()); }
function site_logo_width(): string { return css_value(setting('site_logo_width', '180'), '180px'); }
function site_logo_height(): string { return css_value(setting('site_logo_height', 'auto'), 'auto'); }
function site_logo_position(): string { $position = setting('site_logo_position', 'left'); return in_array($position, ['left','center','right'], true) ? $position : 'left'; }

function theme_css_variables(): string
{
    $vars = [
        '--site-primary' => setting('color_primary', '#ee7d1a'),
        '--site-primary-hover' => setting('color_primary_hover', '#c95f0b'),
        '--site-secondary' => setting('color_secondary', '#0b1641'),
        '--site-secondary-2' => setting('color_secondary_2', '#31447f'),
        '--site-bg' => setting('color_site_bg', '#f5f7fc'),
        '--site-text' => setting('color_text', '#1b2440'),
        '--site-text-muted' => setting('color_text_muted', '#64708f'),
        '--site-surface' => setting('color_surface', '#ffffff'),
        '--site-border' => setting('color_border', 'rgba(11,22,65,.12)'),
        '--font-heading' => '"' . setting('font_heading', 'Montserrat') . '", Arial, sans-serif',
        '--font-body' => '"' . setting('font_body', 'Inter') . '", Arial, sans-serif',
        '--hero-bg-from' => setting('hero_bg_from', '#08102b'),
        '--hero-bg-to' => setting('hero_bg_to', '#1b2f71'),
        '--hero-glow-left' => setting('hero_glow_left', '#ee7d1a'),
        '--hero-glow-right' => setting('hero_glow-right', setting('hero_glow_right', '#78a3ff')),
        '--hero-eyebrow-color' => setting('home_eyebrow_color', '#7f94d6'),
        '--hero-title-color' => setting('home_title_color', '#ffffff'),
        '--hero-lead-color' => setting('home_lead_color', '#d9e2ff'),
        '--hero-eyebrow-size' => setting('home_eyebrow_size', '0.95rem'),
        '--hero-title-size' => setting('home_title_size', 'clamp(3.9rem, 7vw, 6.15rem)'),
        '--hero-lead-size' => setting('home_lead_size', '1.18rem'),
        '--hero-chip-size' => setting('home_chip_size', '1rem'),
        '--hero-button-size' => setting('home_button_size', '1rem'),
        '--hero-feature-size' => setting('home_feature_size', '0.98rem'),
    ];
    $css = ':root{';
    foreach ($vars as $k=>$v) $css .= $k . ':' . $v . ';';
    $css .= '}';
    return '<style>' . $css . '</style>';
}

function hero_settings(): array
{
    return [
        'eyebrow' => setting('home_eyebrow', 'Entreprise multitechnique avancée'),
        'title' => setting('home_title', 'Le partenaire technique de vos bâtiments en Île-de-France et en Occitanie'),
        'lead' => setting('home_lead', 'EMAE aide à capter des clients pour le dépannage, l’entretien et les besoins techniques en électricité, plomberie, chauffage, climatisation, CVC et pompes à chaleur.'),
        'bullets' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', setting('home_bullets', '')) ?: []))),
        'chips' => array_values(array_filter([setting('home_chip_1','Électricité'),setting('home_chip_2','Plomberie'),setting('home_chip_3','CVC'),setting('home_chip_4','Climatisation'),setting('home_chip_5','Chauffage'),setting('home_chip_6','PAC')], static fn($v)=>trim((string)$v) !== '')),
        'button1_label' => setting('home_button1_label', 'Demander un devis'),
        'button1_url' => setting('home_button1_url', 'quote'),
        'button2_label' => setting('home_button2_label', 'Domaines d’intervention'),
        'button2_url' => setting('home_button2_url', 'services'),
        'feature_1_title' => setting('home_feature_1_title', 'Mobile first'),
        'feature_1_text' => setting('home_feature_1_text', 'Prêt pour Google Ads local'),
        'feature_2_title' => setting('home_feature_2_title', 'Conversion rapide'),
        'feature_2_text' => setting('home_feature_2_text', 'Appel, devis, contact, espace client'),
        'feature_3_title' => setting('home_feature_3_title', 'Image premium'),
        'feature_3_text' => setting('home_feature_3_text', 'Corporate, claire et rassurante'),
        'quote_eyebrow' => setting('home_quote_eyebrow', 'DEMANDE DE DEVIS GRATUITE'),
        'quote_title' => setting('home_quote_title', 'Obtenir un rappel rapide'),
        'quote_service_label' => setting('home_quote_service_label', 'Service'),
        'quote_city_label' => setting('home_quote_city_label', 'Ville'),
        'quote_city_placeholder' => setting('home_quote_city_placeholder', 'Ex : Meaux, Paris, Toulouse'),
        'quote_button_label' => setting('home_quote_button_label', 'Continuer'),
        'quote_meta' => setting('home_quote_meta', 'Artisans disponibles • devis gratuit • réponse rapide'),
    ];
}

function hero_feature_cards(array $hero): array
{
    $features = [
        ['title' => trim((string) ($hero['feature_1_title'] ?? '')), 'text' => trim((string) ($hero['feature_1_text'] ?? ''))],
        ['title' => trim((string) ($hero['feature_2_title'] ?? '')), 'text' => trim((string) ($hero['feature_2_text'] ?? ''))],
        ['title' => trim((string) ($hero['feature_3_title'] ?? '')), 'text' => trim((string) ($hero['feature_3_text'] ?? ''))],
    ];
    return array_values(array_filter($features, static fn(array $f): bool => $f['title'] !== '' || $f['text'] !== ''));
}

function service_cards_settings(): array
{
    $default = [
        ['title'=>'Électricité','image'=>'storage/uploads/services/service-electricite.svg','link'=>'electricien-meaux'],
        ['title'=>'Plomberie','image'=>'storage/uploads/services/service-plomberie.svg','link'=>'plombier-meaux'],
        ['title'=>'Chauffage & climatisation','image'=>'storage/uploads/services/service-cvc.svg','link'=>'climatisation-meaux'],
        ['title'=>'Maintenance & modernisation','image'=>'storage/uploads/services/service-maintenance.svg','link'=>'depannage-paris'],
    ];
    $cards = get_json_setting('home_service_cards', $default);
    return $cards ?: $default;
}

function seo_defaults(string $route = 'home', ?array $page = null): array
{
    if ($page) {
        return ['title'=>$page['meta_title'] ?: ($page['title'] . ' | ' . company_name()), 'description'=>$page['meta_description'] ?: ($page['excerpt'] ?: company_name()), 'canonical'=>route_url($page['slug'])];
    }
    if ($route === 'home' || $route === '') {
        return ['title'=>setting('home_meta_title', company_name() . ' | Dépannage, climatisation, PAC et électricité'), 'description'=>setting('home_meta_description', 'Dépannage et rénovation en électricité, climatisation, PAC, ventilation et chauffage en Île-de-France et Occitanie.'), 'canonical'=>route_url('')];
    }
    return ['title'=>company_name(), 'description'=>setting('home_meta_description', company_name()), 'canonical'=>route_url($route)];
}

function schema_local_business_json(): string
{
    $data = ['@context'=>'https://schema.org','@type'=>'LocalBusiness','name'=>company_name(),'telephone'=>company_phone(),'email'=>company_email(),'description'=>setting('company_description','Entreprise multitechnique avancée.'),'areaServed'=>array_map('trim', explode(',', setting('company_regions', 'Île-de-France, Occitanie'))),'openingHours'=>company_hours(),'url'=>site_base_url() !== '' ? site_base_url() : route_url('')];
    if (company_siret() !== '') $data['identifier'] = company_siret();
    $ratingValue = setting('schema_rating_value', '');
    $reviewCount = setting('schema_review_count', '');
    if ($ratingValue !== '' && $reviewCount !== '') $data['aggregateRating'] = ['@type'=>'AggregateRating','ratingValue'=>(float)$ratingValue,'reviewCount'=>(int)$reviewCount];
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function nav_items(): array
{
    return [
        ['label'=>setting('nav_home','Accueil'),'url'=>route_url('')],
        ['label'=>setting('nav_services','Services'),'url'=>route_url('services')],
        ['label'=>setting('nav_realisations','Réalisations'),'url'=>route_url('realisations')],
        ['label'=>setting('nav_faq','FAQ'),'url'=>route_url('faq')],
        ['label'=>setting('nav_contact','Contact'),'url'=>route_url('contact')],
    ];
}

function quote_form_options(): array
{
    return ['success_message'=>setting('form_success_message','Votre demande a bien été envoyée.'), 'submit_label'=>setting('form_submit_label','Envoyer ma demande'), 'mail_to'=>setting('form_email_to', company_email())];
}
