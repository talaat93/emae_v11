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
    $config = app_config();
    return (bool) ($config['installed'] ?? false);
}

function site_base_url(): string
{
    $config = app_config();
    return rtrim((string) ($config['site']['base_url'] ?? ''), '/');
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
        return $base !== '' ? $base : '/';
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
        return url_for('');
    }
    return url_for($slug);
}

function current_year(): string
{
    return date('Y');
}

function redirect_to(string $path): never
{
    if (!preg_match('#^https?://#i', $path)) {
        $path = url_for($path);
    }
    header('Location: ' . $path);
    exit;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_path();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }
    return '/' . ltrim($path, '/');
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
        if (function_exists('db_fetch_all')) {
            try {
                foreach (db_fetch_all('SELECT setting_key, setting_value FROM settings') as $row) {
                    $cache[$row['setting_key']] = $row['setting_value'];
                }
            } catch (Throwable $e) {
                $cache = [];
            }
        }
    }
    return isset($cache[$key]) && $cache[$key] !== '' ? (string) $cache[$key] : (string) $fallback;
}

function clear_settings_cache(): void
{
    $ref = new ReflectionFunction('setting');
    $static = $ref->getStaticVariables();
}

function set_setting(string $key, mixed $value): void
{
    $stringValue = is_scalar($value) || $value === null
        ? (string) $value
        : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
    if ($value === '') {
        return $fallback;
    }
    if ($value === 'auto') {
        return 'auto';
    }
    if (preg_match('/^-?\d+(\.\d+)?$/', $value)) {
        return $value . 'px';
    }
    if (preg_match('/^-?\d+(\.\d+)?(px|rem|em|%|vh|vw)$/', $value)) {
        return $value;
    }
    return $fallback;
}

function upload_image_field(string $field, string $dir = 'gallery'): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmp = $_FILES[$field]['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return null;
    }
    $mime = mime_content_type($tmp) ?: '';
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];
    if (!isset($allowed[$mime])) {
        return null;
    }
    $uploadDir = __DIR__ . '/../storage/uploads/' . trim($dir, '/');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $target = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($tmp, $target)) {
        return null;
    }
    $path = 'storage/uploads/' . trim($dir, '/') . '/' . $filename;
    db_execute('INSERT INTO media (file_path, alt_text, category) VALUES (?, ?, ?)', [$path, '', $dir]);
    return $path;
}

function company_name(): string { return setting('company_name', 'EMAE'); }
function company_phone(): string { return setting('company_phone', '06 67 83 03 76'); }
function company_phone_link(): string { return setting('company_phone_link', 'tel:+33667830376'); }
function company_email(): string { return setting('company_email', 'emaeentreprisemultitechniqueavance@gmail.com'); }
function company_regions(): string { return setting('company_regions', 'Île-de-France et Occitanie'); }
function company_hours(): string { return setting('company_hours', '24h/24 - 7j/7'); }
function company_address(): string { return setting('company_address', 'Île-de-France et Occitanie'); }
function company_siret(): string { return setting('company_siret', ''); }

function site_logo_path(): string
{
    return setting('site_logo', 'assets/img/logo-emae-default.png');
}

function site_logo_url(): string
{
    return asset_url(site_logo_path());
}

function site_logo_width(): string
{
    return css_value(setting('site_logo_width', '180'), '180px');
}

function site_logo_height(): string
{
    return css_value(setting('site_logo_height', 'auto'), 'auto');
}

function site_logo_position(): string
{
    $position = setting('site_logo_position', 'left');
    return in_array($position, ['left', 'center', 'right'], true) ? $position : 'left';
}

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
    ];
    $css = ':root{';
    foreach ($vars as $key => $value) {
        $css .= $key . ':' . $value . ';';
    }
    $css .= '}';
    return '<style>' . $css . '</style>';
}

function hero_settings(): array
{
    return [
        'eyebrow' => setting('home_eyebrow', 'EMAE - INTERVENTION RAPIDE 24H/24 7J/7'),
        'title' => setting('home_title', 'Dépannage et rénovation en électricité, climatisation, PAC, ventilation et chauffage'),
        'lead' => setting('home_lead', 'Intervention moyenne en moins de 2h sur l’Île-de-France et l’Occitanie. Appel immédiat, devis rapide, urgence 24h/24 7j/7.'),
        'bullets' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', setting('home_bullets', "Intervention moyenne sous 2h\nDevis gratuit et sans engagement\nZone couverte : Île-de-France et Occitanie")) ?: []))),
        'button1_label' => setting('home_button1_label', 'Appeler maintenant'),
        'button1_url' => setting('home_button1_url', company_phone_link()),
        'button2_label' => setting('home_button2_label', 'Demander un devis'),
        'button2_url' => setting('home_button2_url', route_url('quote')),
        'quote_title' => setting('home_quote_title', 'Obtenir un rappel rapide'),
        'quote_service_label' => setting('home_quote_service_label', 'Service'),
        'quote_city_label' => setting('home_quote_city_label', 'Ville'),
        'quote_city_placeholder' => setting('home_quote_city_placeholder', 'Ex : Meaux, Paris, Toulouse'),
        'quote_button_label' => setting('home_quote_button_label', 'Continuer'),
        'quote_meta' => setting('home_quote_meta', 'Artisans disponibles • devis gratuit • réponse rapide'),
    ];
}

function service_cards_settings(): array
{
    $default = [
        ['title' => 'Électricité', 'image' => 'storage/uploads/services/service-electricite.jpg', 'link' => 'electricite-meaux'],
        ['title' => 'Plomberie', 'image' => 'storage/uploads/services/service-plomberie.jpg', 'link' => 'plombier-meaux'],
        ['title' => 'CVC', 'image' => 'storage/uploads/services/service-cvc.jpg', 'link' => 'climatisation-meaux'],
        ['title' => 'Énergies renouvelables', 'image' => 'storage/uploads/services/service-energies.jpg', 'link' => 'depannage-paris'],
    ];
    $cards = get_json_setting('home_service_cards', $default);
    return $cards ?: $default;
}

function seo_defaults(string $route = 'home', ?array $page = null): array
{
    if ($page) {
        return [
            'title' => $page['meta_title'] ?: ($page['title'] . ' | ' . company_name()),
            'description' => $page['meta_description'] ?: ($page['excerpt'] ?: company_name()),
            'canonical' => route_url($page['slug']),
        ];
    }
    if ($route === 'home') {
        return [
            'title' => setting('home_meta_title', company_name() . ' | Dépannage, climatisation, PAC et électricité'),
            'description' => setting('home_meta_description', 'Dépannage et rénovation en électricité, climatisation, PAC, ventilation et chauffage en Île-de-France et Occitanie.'),
            'canonical' => route_url(''),
        ];
    }
    return [
        'title' => company_name(),
        'description' => setting('home_meta_description', company_name()),
        'canonical' => route_url($route),
    ];
}

function schema_local_business_json(): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => company_name(),
        'telephone' => company_phone(),
        'email' => company_email(),
        'description' => setting('company_description', 'Entreprise multitechnique avancée.'),
        'areaServed' => array_map('trim', explode(',', setting('company_regions', 'Île-de-France, Occitanie'))),
        'openingHours' => company_hours(),
        'url' => site_base_url() !== '' ? site_base_url() : route_url(''),
    ];
    if (company_siret() !== '') {
        $data['identifier'] = company_siret();
    }
    $ratingValue = setting('schema_rating_value', '');
    $reviewCount = setting('schema_review_count', '');
    if ($ratingValue !== '' && $reviewCount !== '') {
        $data['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (float) $ratingValue,
            'reviewCount' => (int) $reviewCount,
        ];
    }
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function nav_items(): array
{
    return [
        ['label' => setting('nav_home', 'Accueil'), 'url' => route_url('')],
        ['label' => setting('nav_services', 'Services'), 'url' => route_url('services')],
        ['label' => setting('nav_realisations', 'Réalisations'), 'url' => route_url('realisations')],
        ['label' => setting('nav_faq', 'FAQ'), 'url' => route_url('faq')],
        ['label' => setting('nav_contact', 'Contact'), 'url' => route_url('contact')],
    ];
}

function quote_form_options(): array
{
    return [
        'success_message' => setting('form_success_message', 'Votre demande a bien été envoyée.'),
        'submit_label' => setting('form_submit_label', 'Envoyer ma demande'),
        'mail_to' => setting('form_email_to', company_email()),
    ];
}
