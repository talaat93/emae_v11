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


function services_builder_resolve_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    if (!preg_match('#^(https?:|mailto:|tel:|/)#i', $url)) {
        $url = route_url($url);
    }

    return $url;
}

function services_builder_section_style(array $block): string
{
    $styles = [];

    if (trim((string) ($block['background'] ?? '')) !== '') {
        $styles[] = 'background:' . trim((string) $block['background']);
    }

    if (trim((string) ($block['text_color'] ?? '')) !== '') {
        $styles[] = 'color:' . trim((string) $block['text_color']);
    }

    return implode(';', $styles);
}

function render_services_builder_page(array $config): void
{
    $page = $config['page'] ?? [];
    $blocks = is_array($config['blocks'] ?? null) ? $config['blocks'] : [];

    $eyebrow = trim((string) ($page['eyebrow'] ?? ''));
    $title = trim((string) ($page['title'] ?? 'Services'));
    $subtitle = trim((string) ($page['subtitle'] ?? ''));
    $heroBackground = trim((string) ($page['hero_background'] ?? '#0b1641'));
    $heroTextColor = trim((string) ($page['hero_text_color'] ?? '#ffffff'));
    $heroImage = trim((string) ($page['hero_image'] ?? ''));
    $primaryLabel = trim((string) ($page['primary_label'] ?? ''));
    $primaryUrl = services_builder_resolve_url((string) ($page['primary_url'] ?? ''));
    $secondaryLabel = trim((string) ($page['secondary_label'] ?? ''));
    $secondaryUrl = services_builder_resolve_url((string) ($page['secondary_url'] ?? ''));

    $heroStyles = [];
    if ($heroBackground !== '') {
        $heroStyles[] = 'background:' . $heroBackground;
    }
    if ($heroTextColor !== '') {
        $heroStyles[] = 'color:' . $heroTextColor;
    }
    if ($heroImage !== '') {
        $heroStyles[] = 'background-image:linear-gradient(135deg, rgba(11,22,65,.88), rgba(11,22,65,.68)), url("' . asset_url($heroImage) . '")';
        $heroStyles[] = 'background-size:cover';
        $heroStyles[] = 'background-position:center';
    }

    echo '<section class="page-hero services-hero" style="' . e(implode(';', $heroStyles)) . '">';
    echo '<div class="container services-hero__inner">';
    echo '<div class="services-hero__content">';

    if ($eyebrow !== '') {
        echo '<p class="eyebrow">' . e($eyebrow) . '</p>';
    }

    echo '<h1>' . e($title) . '</h1>';

    if ($subtitle !== '') {
        echo '<p class="services-hero__lead">' . e($subtitle) . '</p>';
    }

    if (($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '')) {
        echo '<div class="services-hero__actions">';
        if ($primaryLabel !== '' && $primaryUrl !== '') {
            echo '<a class="btn btn--primary" href="' . e($primaryUrl) . '">' . e($primaryLabel) . '</a>';
        }
        if ($secondaryLabel !== '' && $secondaryUrl !== '') {
            echo '<a class="btn btn--secondary" href="' . e($secondaryUrl) . '">' . e($secondaryLabel) . '</a>';
        }
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
    echo '</section>';

    foreach ($blocks as $index => $block) {
        render_services_builder_block($block, (int) $index);
    }
}

function render_services_builder_block(array $block, int $index = 0): void
{
    $type = trim((string) ($block['type'] ?? 'text'));
    $anchor = trim((string) ($block['anchor'] ?? ''));
    $eyebrow = trim((string) ($block['eyebrow'] ?? ''));
    $title = trim((string) ($block['title'] ?? ''));
    $subtitle = trim((string) ($block['subtitle'] ?? ''));
    $text = (string) ($block['text'] ?? '');
    $html = (string) ($block['html'] ?? '');
    $image = trim((string) ($block['image'] ?? ''));
    $layout = trim((string) ($block['layout'] ?? 'image_right'));
    $buttonLabel = trim((string) ($block['button_label'] ?? ''));
    $buttonUrl = services_builder_resolve_url((string) ($block['button_url'] ?? ''));
    $button2Label = trim((string) ($block['button2_label'] ?? ''));
    $button2Url = services_builder_resolve_url((string) ($block['button2_url'] ?? ''));
    $items = is_array($block['items'] ?? null) ? $block['items'] : [];

    $style = services_builder_section_style($block);
    $sectionId = $anchor !== '' ? ' id="' . e($anchor) . '"' : '';
    $styleAttr = $style !== '' ? ' style="' . e($style) . '"' : '';

    echo '<section class="section services-builder-section services-builder-section--' . e($type) . '"' . $sectionId . $styleAttr . '>';
    echo '<div class="container">';

    $renderHeader = !in_array($type, ['cta'], true);

    if ($renderHeader && ($eyebrow !== '' || $title !== '' || $subtitle !== '')) {
        echo '<div class="services-builder-section__head">';
        if ($eyebrow !== '') {
            echo '<p class="eyebrow">' . e($eyebrow) . '</p>';
        }
        if ($title !== '') {
            echo '<h2>' . e($title) . '</h2>';
        }
        if ($subtitle !== '') {
            echo '<p>' . e($subtitle) . '</p>';
        }
        echo '</div>';
    }

    if ($type === 'text' || $type === 'image_text') {
        $hasImage = $image !== '';
        if ($type === 'image_text' && $hasImage) {
            echo '<div class="services-split ' . ($layout === 'image_left' ? 'services-split--image-left' : 'services-split--image-right') . '">';
            echo '<div class="services-copy">';
            if (trim(strip_tags($text)) !== '') {
                echo '<div class="rich-content services-richtext">' . $text . '</div>';
            }
            if ($buttonLabel !== '' && $buttonUrl !== '') {
                echo '<div class="services-block__actions"><a class="btn btn--primary" href="' . e($buttonUrl) . '">' . e($buttonLabel) . '</a></div>';
            }
            echo '</div>';
            echo '<div class="services-media"><img src="' . e(asset_url($image)) . '" alt="' . e($title !== '' ? $title : 'Service') . '"></div>';
            echo '</div>';
        } else {
            echo '<div class="services-copy services-copy--narrow">';
            if (trim(strip_tags($text)) !== '') {
                echo '<div class="rich-content services-richtext">' . $text . '</div>';
            }
            if ($buttonLabel !== '' && $buttonUrl !== '') {
                echo '<div class="services-block__actions"><a class="btn btn--primary" href="' . e($buttonUrl) . '">' . e($buttonLabel) . '</a></div>';
            }
            echo '</div>';
        }
    } elseif ($type === 'cards') {
        echo '<div class="services-cards">';
        foreach ($items as $item) {
            $itemTitle = trim((string) ($item['title'] ?? ''));
            $itemText = trim((string) ($item['text'] ?? ''));
            $itemImage = trim((string) ($item['image'] ?? ''));
            $itemButtonLabel = trim((string) ($item['button_label'] ?? ''));
            $itemButtonUrl = services_builder_resolve_url((string) ($item['button_url'] ?? ''));

            echo '<article class="services-card">';
            if ($itemImage !== '') {
                echo '<img src="' . e(asset_url($itemImage)) . '" alt="' . e($itemTitle !== '' ? $itemTitle : 'Service') . '">';
            }
            if ($itemTitle !== '') {
                echo '<h3>' . e($itemTitle) . '</h3>';
            }
            if ($itemText !== '') {
                echo '<p>' . e($itemText) . '</p>';
            }
            if ($itemButtonLabel !== '' && $itemButtonUrl !== '') {
                echo '<div class="services-card__actions"><a class="btn btn--secondary btn--small" href="' . e($itemButtonUrl) . '">' . e($itemButtonLabel) . '</a></div>';
            }
            echo '</article>';
        }
        echo '</div>';
    } elseif ($type === 'faq') {
        echo '<div class="services-faq">';
        foreach ($items as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = (string) ($item['answer'] ?? '');

            echo '<details class="services-faq__item"' . ($index === 0 ? ' open' : '') . '>';
            echo '<summary>' . e($question) . '</summary>';
            echo '<div class="rich-content services-richtext">' . $answer . '</div>';
            echo '</details>';
        }
        echo '</div>';
    } elseif ($type === 'cta') {
        echo '<div class="services-cta">';
        echo '<div class="services-cta__content">';
        if ($eyebrow !== '') {
            echo '<p class="eyebrow">' . e($eyebrow) . '</p>';
        }
        if ($title !== '') {
            echo '<h2>' . e($title) . '</h2>';
        }
        if (trim(strip_tags($text)) !== '') {
            echo '<div class="services-richtext">' . $text . '</div>';
        }
        echo '</div>';

        if (($buttonLabel !== '' && $buttonUrl !== '') || ($button2Label !== '' && $button2Url !== '')) {
            echo '<div class="services-cta__actions">';
            if ($buttonLabel !== '' && $buttonUrl !== '') {
                echo '<a class="btn btn--primary" href="' . e($buttonUrl) . '">' . e($buttonLabel) . '</a>';
            }
            if ($button2Label !== '' && $button2Url !== '') {
                echo '<a class="btn btn--secondary" href="' . e($button2Url) . '">' . e($button2Label) . '</a>';
            }
            echo '</div>';
        }

        echo '</div>';
    } elseif ($type === 'html') {
        echo $html;
    }

    echo '</div>';
    echo '</section>';
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
