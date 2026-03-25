<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/render.php';

$route = trim((string) ($_GET['route'] ?? ''), '/');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'quote') {
    verify_csrf();
    if (!rate_limit_passed('quote_submit', 8)) {
        flash('error', 'Merci de patienter quelques secondes avant de renvoyer le formulaire.');
        redirect_to('index.php?route=quote');
    }
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        flash('error', 'Envoi bloqué.');
        redirect_to('index.php?route=quote');
    }
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $serviceType = trim((string) ($_POST['service_type'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $urgency = trim((string) ($_POST['urgency'] ?? 'Normale'));
    if ($fullName === '' || $phone === '' || $message === '') {
        flash('error', 'Merci de remplir les champs obligatoires.');
        redirect_to('index.php?route=quote');
    }
    db_execute('INSERT INTO quotes (full_name, phone, email, city, service_type, message, urgency, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$fullName, $phone, $email, $city, $serviceType, $message, $urgency, 'nouveau']);
    flash('success', quote_form_options()['success_message']);
    redirect_to('index.php?route=quote');
}

$meta = seo_defaults($route === '' ? 'home' : $route);

if ($route === '' || $route === 'home') {
    $hero = hero_settings();
    $cards = home_cards();
    $reviews = visible_reviews(3);
    $features = hero_feature_cards($hero);
    $button1Href = trim((string) $hero['button1_url']);
    if ($button1Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $button1Href)) {
        $button1Href = route_url($button1Href);
    }
    $button2Href = trim((string) $hero['button2_url']);
    if ($button2Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $button2Href)) {
        $button2Href = route_url($button2Href);
    }

    $heroChips = array_values(array_filter(
        array_map(static fn($chip): string => trim((string) $chip), (array) ($hero['chips'] ?? [])),
        static fn(string $chip): bool => $chip !== ''
    ));

    $heroRibbonItems = [];
    foreach ($features as $feature) {
        $title = trim((string) ($feature['title'] ?? ''));
        $text = trim((string) ($feature['text'] ?? ''));
        $item = trim($title . ($text !== '' ? ' ' . $text : ''));
        if ($item !== '') {
            $heroRibbonItems[] = $item;
        }
    }

    $quoteEyebrow = trim((string) ($hero['quote_eyebrow'] ?? ''));
    if ($quoteEyebrow === '' || strcasecmp($quoteEyebrow, 'Demande rapide') === 0) {
        $quoteEyebrow = "Demande d'appel rapide";
    }

    $quoteTitle = trim((string) ($hero['quote_title'] ?? ''));
    if ($quoteTitle === '' || strcasecmp($quoteTitle, 'Être rappelé') === 0 || strcasecmp($quoteTitle, 'Obtenir un rappel rapide') === 0) {
        $quoteTitle = 'Obtenir un rappel rapide';
    }

    $quoteButtonLabel = trim((string) ($hero['quote_button_label'] ?? ''));
    if ($quoteButtonLabel === '' || strcasecmp($quoteButtonLabel, 'Être rappelé') === 0) {
        $quoteButtonLabel = 'Être rappelé';
    }

    $quoteServiceLabel = trim((string) ($hero['quote_service_label'] ?? ''));
    if ($quoteServiceLabel === '') {
        $quoteServiceLabel = 'Service';
    }

    $quoteCityLabel = trim((string) ($hero['quote_city_label'] ?? ''));
    if ($quoteCityLabel === '') {
        $quoteCityLabel = 'Ville';
    }

    $quoteCityPlaceholder = trim((string) ($hero['quote_city_placeholder'] ?? ''));
    if ($quoteCityPlaceholder === '') {
        $quoteCityPlaceholder = 'Ex : Meaux, Paris, Toulouse';
    }

    $quoteMeta = trim((string) ($hero['quote_meta'] ?? ''));
    if ($quoteMeta === '') {
        $quoteMeta = 'Artisans disponibles • devis gratuit • réponse rapide';
    }

    $banner = function_exists('hero_banner_settings') ? hero_banner_settings() : [];
    $bannerButton1Href = trim((string) ($banner['button1_url'] ?? ''));
    if ($bannerButton1Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $bannerButton1Href)) {
        $bannerButton1Href = route_url($bannerButton1Href);
    }
    $bannerButton2Href = trim((string) ($banner['button2_url'] ?? ''));
    if ($bannerButton2Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $bannerButton2Href)) {
        $bannerButton2Href = route_url($bannerButton2Href);
    }
    $bannerLogoPath = trim((string) ($banner['logo_path'] ?? ''));
    $bannerHasLogo = $bannerLogoPath !== '';

    $serviceLinksByTitle = [];
    foreach ($cards as $card) {
        $serviceLinksByTitle[mb_strtolower(trim((string) ($card['title'] ?? '')), 'UTF-8')] = trim((string) ($card['link'] ?? ''));
    }

    $expertiseSection = function_exists('home_expertise_settings') ? home_expertise_settings() : ['eyebrow' => 'Expertise', 'title' => 'Notre expertise multitechnique', 'lead' => '', 'cards' => []];
    $expertiseBlocks = [];
    foreach ((array) ($expertiseSection['cards'] ?? []) as $block) {
        $items = [];
        foreach (['item_1', 'item_2', 'item_3'] as $itemKey) {
            $itemValue = trim((string) ($block[$itemKey] ?? ''));
            if ($itemValue !== '') {
                $items[] = $itemValue;
            }
        }

        $link = trim((string) ($block['link'] ?? 'services'));
        $titleKey = mb_strtolower(trim((string) ($block['title'] ?? '')), 'UTF-8');
        if ($link === '' && isset($serviceLinksByTitle[$titleKey])) {
            $link = $serviceLinksByTitle[$titleKey];
        }
        if ($link === '') {
            $link = 'services';
        }

        $expertiseBlocks[] = [
            'icon' => trim((string) ($block['icon'] ?? '🔧')) ?: '🔧',
            'title' => trim((string) ($block['title'] ?? '')),
            'lead' => trim((string) ($block['lead'] ?? '')),
            'items' => $items,
            'link' => $link,
        ];
    }

    $reviewsBlock = function_exists('home_reviews_block_settings') ? home_reviews_block_settings() : ['eyebrow' => 'Avis clients', 'title' => 'Des témoignages qui rassurent', 'lead' => ''];
    $quotePanelBlock = function_exists('home_quote_panel_settings') ? home_quote_panel_settings() : ['eyebrow' => 'Demande de devis', 'title' => 'Demande de devis', 'lead' => '', 'service_label' => 'Service', 'service_placeholder' => 'Choisir', 'message_label' => 'Votre besoin', 'urgency_label' => 'Urgence', 'button_label' => quote_form_options()['submit_label']];
    $zoneSection = function_exists('home_zone_settings') ? home_zone_settings() : ['eyebrow' => 'Zone d’intervention', 'title' => 'Une zone d’intervention claire et rassurante', 'lead' => '', 'badges' => [], 'button_label' => '', 'button_url' => '', 'cards' => []];
    $zoneButtonHref = trim((string) ($zoneSection['button_url'] ?? ''));
    if ($zoneButtonHref !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $zoneButtonHref)) {
        $zoneButtonHref = route_url($zoneButtonHref);
    }

    $servicePlaceholderSvg = static function (string $title): string {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000">
  <defs>
    <linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="#0a1742"/>
      <stop offset="52%" stop-color="#18367f"/>
      <stop offset="100%" stop-color="#ee7d1a"/>
    </linearGradient>
    <linearGradient id="shine" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="rgba(255,255,255,0.28)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
    </linearGradient>
  </defs>
  <rect width="800" height="1000" fill="url(#bg)"/>
  <circle cx="145" cy="155" r="170" fill="rgba(255,255,255,0.07)"/>
  <circle cx="665" cy="205" r="195" fill="rgba(255,255,255,0.08)"/>
  <rect x="70" y="640" width="660" height="190" rx="34" fill="rgba(7,17,41,0.35)" stroke="rgba(255,255,255,0.14)"/>
  <text x="85" y="745" fill="#ffffff" font-size="56" font-weight="700" font-family="Arial, Helvetica, sans-serif">{$safeTitle}</text>
  <text x="85" y="815" fill="#dce6ff" font-size="28" font-family="Arial, Helvetica, sans-serif">EMAE • Intervention • Installation • Maintenance</text>
  <path d="M90 160 L270 160 L198 265 L318 265 L140 510 L188 332 L90 332 Z" fill="rgba(255,255,255,0.12)"/>
  <rect x="0" y="0" width="800" height="1000" fill="url(#shine)"/>
</svg>
SVG;
        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    };

    render_head($meta);
    render_header(route_url(''));
    ?>
    <section class="hero hero--home">
        <div class="container hero__grid hero__grid--callback-right">
            <div class="hero__content">
                <?php if (trim((string) $hero['eyebrow']) !== ''): ?>
                    <p class="eyebrow"><?= e($hero['eyebrow']) ?></p>
                <?php endif; ?>

                <?php if (trim((string) $hero['title']) !== ''): ?>
                    <h1><?= nl2br(e($hero['title'])) ?></h1>
                <?php endif; ?>

                <?php if (trim((string) $hero['lead']) !== ''): ?>
                    <p class="hero__lead"><?= nl2br(e($hero['lead'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($heroChips)): ?>
                    <div class="hero__chips">
                        <?php foreach ($heroChips as $chip): ?>
                            <span><?= e($chip) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (trim((string) $hero['button1_label']) !== '' || trim((string) $hero['button2_label']) !== ''): ?>
                    <div class="hero__actions">
                        <?php if (trim((string) $hero['button1_label']) !== '' && $button1Href !== ''): ?>
                            <a class="btn btn--primary" href="<?= e($button1Href) ?>"><?= e($hero['button1_label']) ?></a>
                        <?php endif; ?>
                        <?php if (trim((string) $hero['button2_label']) !== '' && $button2Href !== ''): ?>
                            <a class="btn btn--outline" href="<?= e($button2Href) ?>"><?= e($hero['button2_label']) ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($heroRibbonItems)): ?>
                    <div class="hero__feature-ribbon" aria-label="Atouts EMAE">
                        <?php foreach ($heroRibbonItems as $index => $item): ?>
                            <span class="hero__feature-item"><strong><?= e($item) ?></strong></span>
                            <?php if ($index < count($heroRibbonItems) - 1): ?>
                                <span class="hero__feature-separator" aria-hidden="true">•</span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hero__visual hero__visual--hero-form">
                <div class="hero-quote-card hero-quote-card--hero">
                    <p class="hero-quote-card__eyebrow"><?= e($quoteEyebrow) ?></p>
                    <h2><?= e($quoteTitle) ?></h2>

                    <form action="<?= e(route_url('quote')) ?>" method="post" class="hero-quote-card__form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="form_type" value="quote">
                        <input type="text" name="website" value="" class="hp-field" tabindex="-1" autocomplete="off">

                        <div class="form-grid form-grid--hero">
                            <label>
                                Nom complet
                                <input type="text" name="full_name" required>
                            </label>
                            <label>
                                Téléphone
                                <input type="tel" name="phone" required>
                            </label>
                        </div>

                        <div class="form-grid form-grid--hero">
                            <label>
                                Email
                                <input type="email" name="email">
                            </label>
                            <label>
                                <?= e($quoteCityLabel) ?>
                                <input type="text" name="city" placeholder="<?= e($quoteCityPlaceholder) ?>">
                            </label>
                        </div>

                        <label>
                            <?= e($quoteServiceLabel) ?>
                            <select name="service_type">
                                <option value="">Choisir un service</option>
                                <?php foreach ($cards as $card): ?>
                                    <option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            Votre besoin
                            <textarea name="message" placeholder="Décrivez brièvement la panne ou le besoin." required></textarea>
                        </label>

                        <button class="btn btn--primary btn--block" type="submit">Envoyer ma demande</button>
                        <p class="hero-quote-card__meta"><?= e($quoteMeta) ?></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--tight service-band service-band--reference">
        <div class="container">
            <div class="service-showcase service-showcase--v10 service-showcase--reference">
                <?php foreach (array_slice($cards, 0, 4) as $card): ?>
                    <?php $fallbackImage = $servicePlaceholderSvg((string) $card['title']); ?>
                    <a class="service-media-card service-media-card--reference" href="<?= e(route_url($card['link'])) ?>">
                        <img src="<?= e(asset_url($card['image'])) ?>" alt="<?= e($card['title']) ?>" onerror="this.onerror=null;this.src='<?= e($fallbackImage) ?>';">
                        <div class="service-media-card__shade"></div>
                        <div class="service-media-card__body">
                            <h2><?= nl2br(e(str_replace(' ', "\n", (string) $card['title']))) ?></h2>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="urgency-banner-section">
        <div class="container">
            <div class="urgency-banner">
                <div class="urgency-banner__brand">
                    <?php if ($bannerHasLogo): ?>
                        <span class="urgency-banner__logo-wrap">
                            <img src="<?= e(asset_url($bannerLogoPath)) ?>" alt="<?= e(company_name()) ?>" class="urgency-banner__logo">
                        </span>
                    <?php else: ?>
                        <span class="urgency-banner__fallback">24/7</span>
                    <?php endif; ?>
                </div>

                <div class="urgency-banner__content">
                    <?php if (trim((string) ($banner['eyebrow'] ?? '')) !== ''): ?>
                        <p class="urgency-banner__eyebrow"><?= e($banner['eyebrow']) ?></p>
                    <?php endif; ?>
                    <?php if (trim((string) ($banner['title'] ?? '')) !== ''): ?>
                        <h2 class="urgency-banner__title"><?= e($banner['title']) ?></h2>
                    <?php endif; ?>
                    <?php if (trim((string) ($banner['lead'] ?? '')) !== ''): ?>
                        <p class="urgency-banner__lead"><?= e($banner['lead']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="urgency-banner__actions">
                    <?php if (trim((string) ($banner['button1_label'] ?? '')) !== '' && $bannerButton1Href !== ''): ?>
                        <a class="btn btn--primary urgency-banner__button" href="<?= e($bannerButton1Href) ?>"><?= e($banner['button1_label']) ?></a>
                    <?php endif; ?>
                    <?php if (trim((string) ($banner['button2_label'] ?? '')) !== '' && $bannerButton2Href !== ''): ?>
                        <a class="btn btn--outline urgency-banner__button urgency-banner__button--light" href="<?= e($bannerButton2Href) ?>"><?= e($banner['button2_label']) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section expertise-home-section">
        <div class="container">
            <div class="expertise-home-intro">
                <?php if (trim((string) ($expertiseSection['eyebrow'] ?? '')) !== ''): ?>
                    <p class="eyebrow"><?= e($expertiseSection['eyebrow']) ?></p>
                <?php endif; ?>
                <?php if (trim((string) ($expertiseSection['title'] ?? '')) !== ''): ?>
                    <h2><?= e($expertiseSection['title']) ?></h2>
                <?php endif; ?>
                <?php if (trim((string) ($expertiseSection['lead'] ?? '')) !== ''): ?>
                    <p><?= e($expertiseSection['lead']) ?></p>
                <?php endif; ?>
            </div>

            <div class="expertise-home-grid">
                <?php foreach ($expertiseBlocks as $block): ?>
                    <article class="card expertise-home-card">
                        <div class="expertise-home-card__icon"><?= e($block['icon']) ?></div>
                        <h3><?= e($block['title']) ?></h3>
                        <p class="expertise-home-card__lead"><?= e($block['lead']) ?></p>
                        <ul class="expertise-home-card__list">
                            <?php foreach ($block['items'] as $item): ?>
                                <li><?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="expertise-home-card__link" href="<?= e(route_url($block['link'])) ?>">En savoir plus</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container split-panel">
            <div class="card reviews-panel">
                <?php if (trim((string) ($reviewsBlock['eyebrow'] ?? '')) !== ''): ?>
                    <p class="eyebrow"><?= e($reviewsBlock['eyebrow']) ?></p>
                <?php endif; ?>
                <?php if (trim((string) ($reviewsBlock['title'] ?? '')) !== ''): ?>
                    <h2><?= e($reviewsBlock['title']) ?></h2>
                <?php endif; ?>
                <?php if (trim((string) ($reviewsBlock['lead'] ?? '')) !== ''): ?>
                    <p class="reviews-panel__lead"><?= e($reviewsBlock['lead']) ?></p>
                <?php endif; ?>
                <div class="reviews-grid">
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-card">
                            <div class="review-card__stars"><?= str_repeat('★', (int) $review['rating']) ?></div>
                            <h3><?= e($review['author_name']) ?></h3>
                            <p><?= e($review['content']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card form-card quote-panel">
                <?php if (trim((string) ($quotePanelBlock['eyebrow'] ?? '')) !== ''): ?>
                    <p class="eyebrow"><?= e($quotePanelBlock['eyebrow']) ?></p>
                <?php endif; ?>
                <?php if (trim((string) ($quotePanelBlock['title'] ?? '')) !== ''): ?>
                    <h2><?= e($quotePanelBlock['title']) ?></h2>
                <?php endif; ?>
                <?php if (trim((string) ($quotePanelBlock['lead'] ?? '')) !== ''): ?>
                    <p class="quote-panel__lead"><?= e($quotePanelBlock['lead']) ?></p>
                <?php endif; ?>

                <form action="<?= e(route_url('quote')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="form_type" value="quote">
                    <input type="text" name="website" value="" class="hp-field" tabindex="-1" autocomplete="off">

                    <div class="form-grid">
                        <label>Nom complet<input type="text" name="full_name" required></label>
                        <label>Téléphone<input type="tel" name="phone" required></label>
                    </div>

                    <div class="form-grid">
                        <label>Email<input type="email" name="email"></label>
                        <label>Ville<input type="text" name="city"></label>
                    </div>

                    <label>
                        <?= e($quotePanelBlock['service_label'] ?? 'Service') ?>
                        <select name="service_type">
                            <option value=""><?= e($quotePanelBlock['service_placeholder'] ?? 'Choisir') ?></option>
                            <?php foreach ($cards as $card): ?>
                                <option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label><?= e($quotePanelBlock['message_label'] ?? 'Votre besoin') ?><textarea name="message" required></textarea></label>
                    <label>
                        <?= e($quotePanelBlock['urgency_label'] ?? 'Urgence') ?>
                        <select name="urgency">
                            <option>Normale</option>
                            <option>Urgente</option>
                            <option>Très urgente</option>
                        </select>
                    </label>

                    <button class="btn btn--primary btn--block" type="submit"><?= e($quotePanelBlock['button_label'] ?? quote_form_options()['submit_label']) ?></button>
                </form>
            </div>
        </div>
    </section>

    <section class="section zone-home-section">
        <div class="container">
            <div class="zone-home">
                <div class="zone-home__intro">
                    <?php if (trim((string) ($zoneSection['eyebrow'] ?? '')) !== ''): ?>
                        <p class="eyebrow"><?= e($zoneSection['eyebrow']) ?></p>
                    <?php endif; ?>
                    <?php if (trim((string) ($zoneSection['title'] ?? '')) !== ''): ?>
                        <h2><?= e($zoneSection['title']) ?></h2>
                    <?php endif; ?>
                    <?php if (trim((string) ($zoneSection['lead'] ?? '')) !== ''): ?>
                        <p class="zone-home__lead"><?= e($zoneSection['lead']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($zoneSection['badges'])): ?>
                        <div class="zone-home__badges">
                            <?php foreach ((array) $zoneSection['badges'] as $badge): ?>
                                <span><?= e($badge) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (trim((string) ($zoneSection['button_label'] ?? '')) !== '' && $zoneButtonHref !== ''): ?>
                        <div class="zone-home__actions">
                            <a class="btn btn--primary" href="<?= e($zoneButtonHref) ?>"><?= e($zoneSection['button_label']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="zone-home__grid">
                    <?php foreach ((array) ($zoneSection['cards'] ?? []) as $zoneCard): ?>
                        <article class="zone-home__card">
                            <h3><?= e((string) ($zoneCard['title'] ?? '')) ?></h3>
                            <p><?= e((string) ($zoneCard['text'] ?? '')) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php render_footer(); exit; }

if (in_array($route, ['quote', 'contact'], true)) {
    $page = page_by_slug($route);
    $meta = seo_defaults($route, $page);
    render_head($meta);
    render_header(route_url($route));
    ?>
    <section class="page-hero"><div class="container"><p class="eyebrow"><?= e($page['title'] ?? 'Contact') ?></p><h1><?= e($page['excerpt'] ?? 'Parlez-nous de votre besoin') ?></h1><p><?= e($page['meta_description'] ?? 'Demandez un devis ou une intervention rapide.') ?></p></div></section>
    <section class="section"><div class="container split-panel"><div class="card rich-content"><?= $page['content_html'] ?: '<p>Décrivez votre besoin et nous vous répondrons rapidement.</p>' ?><div class="contact-list"><div><strong>Téléphone :</strong> <a href="<?= e(company_phone_link()) ?>"><?= e(company_phone()) ?></a></div><div><strong>Email :</strong> <a href="mailto:<?= e(company_email()) ?>"><?= e(company_email()) ?></a></div><div><strong>Zones :</strong> <?= e(company_regions()) ?></div><div><strong>Horaires :</strong> <?= e(company_hours()) ?></div></div></div><div class="card form-card"><h2>Être rappelé</h2><form action="<?= e(route_url('quote')) ?>" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form_type" value="quote"><input type="text" name="website" value="" class="hp-field" tabindex="-1" autocomplete="off"><div class="form-grid"><label>Nom complet<input type="text" name="full_name" required></label><label>Téléphone<input type="tel" name="phone" required></label></div><div class="form-grid"><label>Email<input type="email" name="email"></label><label>Ville<input type="text" name="city"></label></div><label>Service<select name="service_type"><option value="">Choisir</option><?php foreach (home_cards() as $card): ?><option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option><?php endforeach; ?></select></label><label>Votre besoin<textarea name="message" required></textarea></label><label>Urgence<select name="urgency"><option>Normale</option><option>Urgente</option><option>Très urgente</option></select></label><button class="btn btn--primary btn--block" type="submit"><?= e(quote_form_options()['submit_label']) ?></button></form></div></div></section>
    <?php render_footer(); exit; }

$page = page_by_slug($route);
if ($page) {
    $meta = seo_defaults($route, $page);
    render_head($meta);
    render_header(route_url($route));
    ?>
    <section class="page-hero"><div class="container"><p class="eyebrow"><?= e($page['page_type']) ?></p><h1><?= e($page['title']) ?></h1><p><?= e($page['excerpt'] ?? '') ?></p></div></section>
    <section class="section"><div class="container"><article class="card rich-content"><?= $page['content_html'] ?: '<p>Contenu à venir.</p>' ?></article></div></section>
    <?php render_footer(); exit; }

render_head(['title' => 'Page introuvable | ' . company_name(), 'description' => 'Cette page est introuvable.', 'canonical' => route_url($route)]);
render_header('');
?><section class="page-hero"><div class="container"><p class="eyebrow">Erreur 404</p><h1>Page introuvable</h1><p>La page demandée n’existe pas ou a été déplacée.</p><a class="btn btn--primary" href="<?= e(route_url('')) ?>">Retour à l’accueil</a></div></section><?php render_footer(); ?>
