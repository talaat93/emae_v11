<?php
declare(strict_types=1);

function page_by_slug(string $slug): ?array
{
    return db_fetch('SELECT * FROM pages WHERE slug = ? AND status = ? LIMIT 1', [$slug, 'published']);
}

function all_pages(): array
{
    return db_fetch_all('SELECT * FROM pages ORDER BY sort_order ASC, title ASC');
}

function visible_reviews(int $limit = 6): array
{
    return db_fetch_all('SELECT * FROM reviews WHERE is_visible = 1 ORDER BY sort_order ASC, id DESC LIMIT ' . max(1, (int) $limit));
}

function all_quotes(): array
{
    return db_fetch_all('SELECT * FROM quotes ORDER BY created_at DESC');
}

function home_cards(): array
{
    return service_cards_settings();
}

function render_head(array $meta): void
{
    $gaId = setting('google_analytics_id', '');
    echo '<!DOCTYPE html><html lang="fr"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . e($meta['title']) . '</title>';
    echo '<meta name="description" content="' . e($meta['description']) . '">';
    echo '<link rel="canonical" href="' . e($meta['canonical']) . '">';
    echo '<meta name="theme-color" content="' . e(setting('color_secondary', '#0b1641')) . '">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=' . rawurlencode(setting('font_heading', 'Montserrat')) . ':wght@400;500;600;700;800&family=' . rawurlencode(setting('font_body', 'Inter')) . ':wght@400;500;600;700;800&display=swap">';
    echo '<link rel="stylesheet" href="' . e(asset_url('assets/css/style.css')) . '">';
    echo theme_css_variables();
    echo '<script type="application/ld+json">' . schema_local_business_json() . '</script>';
    if ($gaId !== '') {
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . e($gaId) . '"></script>';
        echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . e($gaId) . '");</script>';
    }
    echo '<script defer src="' . e(asset_url('assets/js/site.js')) . '"></script>';
    echo '</head><body>';
}

function render_header(string $active = ''): void
{
    $ctaLabel = setting('header_cta_label', 'Devis gratuit');
    $ctaUrl = setting('header_cta_url', 'quote');
    if (!preg_match('#^(https?:|tel:|mailto:|/)#i', $ctaUrl)) {
        $ctaUrl = route_url($ctaUrl);
    }
    $showTopbar = setting_bool('topbar_visible', true);
    $logoPosition = site_logo_position();

    $navWrapStyle = 'display:flex;align-items:center;gap:1rem;';
    $brandStyle = 'display:inline-flex;align-items:center;';
    $navStyle = '';
    $toggleStyle = '';

    if ($logoPosition === 'right') {
        $brandStyle .= 'order:3;margin-left:auto;';
        $toggleStyle = 'order:2;';
        $navStyle = 'order:1;';
    } elseif ($logoPosition === 'center') {
        $navWrapStyle .= 'flex-wrap:wrap;justify-content:space-between;';
        $brandStyle .= 'order:1;width:100%;justify-content:center;';
        $toggleStyle = 'order:2;margin-left:auto;';
        $navStyle = 'order:3;width:100%;justify-content:center;';
    } else {
        $brandStyle .= 'margin-right:auto;';
    }

    if ($showTopbar) {
        echo '<div class="topbar ' . (setting_bool('topbar_sticky', false) ? 'is-sticky' : '') . '"><div class="container topbar__inner">';
        echo '<div class="topbar__left">';
        echo '<a href="' . e(company_phone_link()) . '">' . e(company_phone()) . '</a>';
        echo '<a href="mailto:' . e(company_email()) . '">' . e(company_email()) . '</a>';
        echo '</div>';
        echo '<div class="topbar__right">';
        echo '<span>' . e(company_regions()) . '</span>';
        echo '<span>' . e(company_hours()) . '</span>';
        echo '</div>';
        echo '</div></div>';
    }

    echo '<header class="site-header"><div class="container nav-wrap" style="' . e($navWrapStyle) . '">';
    echo '<a class="brand" href="' . e(route_url('')) . '" style="' . e($brandStyle) . '"><img src="' . e(site_logo_url()) . '" alt="' . e(company_name()) . '" style="width:' . e(site_logo_width()) . ';height:' . e(site_logo_height()) . ';max-width:100%;object-fit:contain;display:block;"></a>';
    echo '<button class="nav-toggle" type="button" aria-expanded="false" style="' . e($toggleStyle) . '">☰</button>';
    echo '<nav class="site-nav" style="' . e($navStyle) . '">';
    foreach (nav_items() as $item) {
        $class = ($active === $item['url']) ? 'is-active' : '';
        echo '<a class="' . e($class) . '" href="' . e($item['url']) . '">' . e($item['label']) . '</a>';
    }
    echo '<a class="btn btn--small btn--primary" href="' . e($ctaUrl) . '">' . e($ctaLabel) . '</a>';
    echo '</nav></div></header>';

    if ($msg = flash('success')) {
        echo '<div class="container flash flash--success">' . e($msg) . '</div>';
    }
    if ($msg = flash('error')) {
        echo '<div class="container flash flash--error">' . e($msg) . '</div>';
    }
}

function render_footer(): void
{
    $footerCols = [
        ['title' => 'Entreprise', 'items' => [company_name(), company_regions(), company_hours()]],
        ['title' => 'Contact', 'items' => [company_phone(), company_email(), company_address()]],
        ['title' => 'Navigation', 'links' => nav_items()],
    ];
    echo '<footer class="site-footer"><div class="container footer-grid">';
    foreach ($footerCols as $col) {
        echo '<div class="footer-col"><h3>' . e($col['title']) . '</h3>';
        if (isset($col['items'])) {
            foreach ($col['items'] as $item) {
                echo '<p>' . e($item) . '</p>';
            }
        }
        if (isset($col['links'])) {
            echo '<ul class="footer-links">';
            foreach ($col['links'] as $link) {
                echo '<li><a href="' . e($link['url']) . '">' . e($link['label']) . '</a></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }
    echo '</div><div class="footer-bottom"><div class="container">© ' . e(current_year()) . ' ' . e(company_name()) . ' — Tous droits réservés.</div></div></footer>';

    echo '<div class="mobile-cta-bar">';
    echo '<a href="' . e(company_phone_link()) . '">Appeler</a>';
    echo '<a href="' . e(route_url('quote')) . '">Devis</a>';
    echo '<a href="' . e(route_url('contact')) . '">Urgence</a>';
    echo '</div>';

    echo '</body></html>';
}
