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



if ($route === 'services') {
    $page = page_by_slug('services');
    $cards = home_cards();
    $meta = seo_defaults('services', $page);

    $serviceHubDefaults = [
        'electricite' => [
            'icon' => '⚡',
            'eyebrow' => 'Électricité',
            'title' => 'Électricité',
            'lead' => 'Dépannage, mise en sécurité, tableaux électriques, rénovation et alimentation des équipements techniques.',
            'points' => [
                'Recherche de panne et remise en service',
                'Mise en sécurité et remise en conformité',
                'Tableaux électriques, TGBT et protection',
            ],
            'cta' => 'Découvrir le service',
            'photo_label' => 'Emplacement photo électricité',
        ],
        'plomberie' => [
            'icon' => '🔧',
            'eyebrow' => 'Plomberie',
            'title' => 'Plomberie',
            'lead' => 'Recherche de fuite, réparation sanitaire, remplacement d’équipements et maintenance courante.',
            'points' => [
                'Recherche de fuite',
                'Réseaux sanitaires et robinetterie',
                'Maintenance des installations d’eau',
            ],
            'cta' => 'Découvrir le service',
            'photo_label' => 'Emplacement photo plomberie',
        ],
        'chauffage' => [
            'icon' => '🔥',
            'eyebrow' => 'Chauffage',
            'title' => 'Chauffage',
            'lead' => 'Diagnostic, dépannage et optimisation des équipements de chauffage pour confort et continuité de service.',
            'points' => [
                'Diagnostic de panne chauffage',
                'Remise en service et contrôle de fonctionnement',
                'Optimisation des réglages',
            ],
            'cta' => 'Découvrir le service',
            'photo_label' => 'Emplacement photo chauffage',
        ],
        'climatisation' => [
            'icon' => '❄️',
            'eyebrow' => 'Climatisation',
            'title' => 'Climatisation',
            'lead' => 'Dépannage, entretien et remise en service des installations de climatisation et rafraîchissement.',
            'points' => [
                'Diagnostic de dysfonctionnement',
                'Entretien courant et nettoyage',
                'Contrôle des performances',
            ],
            'cta' => 'Découvrir le service',
            'photo_label' => 'Emplacement photo climatisation',
        ],
    ];

    $serviceCards = [];
    foreach ($cards as $card) {
        $title = trim((string) ($card['title'] ?? ''));
        $slug = trim((string) ($card['link'] ?? ''));
        $key = null;
        $ctx = mb_strtolower($title . ' ' . $slug, 'UTF-8');
        if (str_contains($ctx, 'electric')) {
            $key = 'electricite';
        } elseif (str_contains($ctx, 'plomb')) {
            $key = 'plomberie';
        } elseif (str_contains($ctx, 'chauff')) {
            $key = 'chauffage';
        } elseif (str_contains($ctx, 'clim') || str_contains($ctx, 'cvc')) {
            $key = 'climatisation';
        }
        if ($key === null || !isset($serviceHubDefaults[$key])) {
            continue;
        }
        $serviceCards[$key] = array_merge($serviceHubDefaults[$key], [
            'link' => $slug !== '' ? route_url($slug) : route_url('services'),
            'image' => asset_url((string) ($card['image'] ?? '')),
            'raw_image' => trim((string) ($card['image'] ?? '')),
        ]);
    }
    foreach ($serviceHubDefaults as $key => $defaults) {
        if (!isset($serviceCards[$key])) {
            $serviceCards[$key] = array_merge($defaults, [
                'link' => route_url($key),
                'image' => '',
                'raw_image' => '',
            ]);
        }
    }

    render_head($meta);
    render_header(route_url('services'));
    ?>
    <section class="page-hero page-hero--services-hub">
        <div class="container services-hub-hero">
            <div class="services-hub-hero__content">
                <p class="eyebrow"><?= e($page['page_type'] ?: 'Services') ?></p>
                <h1><?= e($page['title'] ?: 'Services EMAE') ?></h1>
                <p class="services-hub-hero__lead"><?= e($page['excerpt'] ?: 'Découvrez nos pôles métiers et accédez à des pages service complètes pour l’électricité, la plomberie, le chauffage et la climatisation.') ?></p>
                <div class="hero__actions services-hub-hero__actions">
                    <a class="btn btn--primary" href="<?= e(route_url('quote')) ?>">Demander un devis</a>
                    <a class="btn btn--outline" href="<?= e(company_phone_link()) ?>">Nous appeler</a>
                </div>
                <div class="services-hub-tabs" role="navigation" aria-label="Accès aux services">
                    <?php foreach ($serviceCards as $card): ?>
                        <a class="services-hub-tab" href="<?= e($card['link']) ?>">
                            <span class="services-hub-tab__icon"><?= e($card['icon']) ?></span>
                            <span><?= e($card['title']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="services-hub-hero__panel card">
                <p class="eyebrow eyebrow--light">Page services complète</p>
                <h2>Une vitrine métier pensée pour convertir vos visiteurs</h2>
                <ul class="services-hub-hero__points">
                    <li>Accès rapide à chaque spécialité : électricité, plomberie, chauffage, climatisation.</li>
                    <li>Emplacements prévus pour vos photos de chantier, vos textes métier et vos arguments commerciaux.</li>
                    <li>Design premium cohérent avec l’univers EMAE et appels à l’action visibles.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section section--soft services-hub-section">
        <div class="container">
            <div class="section-heading section-heading--center">
                <p class="eyebrow">Pôles d’intervention</p>
                <h2>Choisissez votre domaine d’intervention</h2>
                <p>Chaque bloc dirige vers une page service dédiée, avec vos photos, votre texte et vos arguments de conversion.</p>
            </div>
            <div class="services-hub-cards">
                <?php foreach ($serviceCards as $card): ?>
                    <article class="services-hub-card card">
                        <a class="services-hub-card__media" href="<?= e($card['link']) ?>">
                            <?php if ($card['raw_image'] !== ''): ?>
                                <img src="<?= e($card['image']) ?>" alt="<?= e($card['title']) ?>">
                            <?php else: ?>
                                <div class="services-hub-card__placeholder">
                                    <span><?= e($card['photo_label']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="services-hub-card__overlay"></div>
                            <div class="services-hub-card__badge"><?= e($card['icon']) ?></div>
                        </a>
                        <div class="services-hub-card__body">
                            <h3><?= e($card['title']) ?></h3>
                            <p><?= e($card['lead']) ?></p>
                            <ul class="services-hub-card__list">
                                <?php foreach ($card['points'] as $point): ?>
                                    <li><?= e($point) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a class="btn btn--outline btn--sm" href="<?= e($card['link']) ?>"><?= e($card['cta']) ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section services-hub-section">
        <div class="container">
            <div class="section-heading section-heading--center">
                <p class="eyebrow">Pages dédiées</p>
                <h2>Une structure pro pour présenter chaque métier</h2>
                <p>Un modèle plus crédible qu’une simple page liste : photos, textes, interventions, rassurance et appel à l’action.</p>
            </div>
            <div class="services-hub-feature-list">
                <?php foreach ($serviceCards as $key => $card): ?>
                    <article class="services-hub-feature <?= $key === 'plomberie' || $key === 'climatisation' ? 'services-hub-feature--reverse' : '' ?>">
                        <div class="services-hub-feature__content">
                            <p class="eyebrow"><?= e($card['eyebrow']) ?></p>
                            <h3><?= e($card['title']) ?> : une page métier complète</h3>
                            <p><?= e($card['lead']) ?></p>
                            <ul class="services-hub-feature__points">
                                <?php foreach ($card['points'] as $point): ?>
                                    <li><?= e($point) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="services-hub-feature__actions">
                                <a class="btn btn--primary" href="<?= e($card['link']) ?>">Voir la page <?= e(mb_strtolower($card['title'], 'UTF-8')) ?></a>
                            </div>
                        </div>
                        <div class="services-hub-feature__media card">
                            <?php if ($card['raw_image'] !== ''): ?>
                                <img src="<?= e($card['image']) ?>" alt="<?= e($card['title']) ?>">
                            <?php else: ?>
                                <div class="services-hub-feature__placeholder">
                                    <strong><?= e($card['photo_label']) ?></strong>
                                    <span>Ajoutez ici une photo chantier, une installation, ou un visuel premium lié au service.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--soft services-hub-section">
        <div class="container">
            <div class="section-heading section-heading--center">
                <p class="eyebrow">Photos & contenu</p>
                <h2>Des emplacements pensés pour vos photos et votre texte</h2>
                <p>Utilisez cette page pour mettre en avant vos réalisations, vos chantiers et les détails qui rassurent vos futurs clients.</p>
            </div>
            <div class="services-hub-gallery">
                <article class="services-hub-gallery__card card">
                    <div class="services-hub-gallery__media services-hub-gallery__media--placeholder"><span>Emplacement photo 1</span></div>
                    <h3>Photo chantier ou dépannage</h3>
                    <p>Ajoutez une photo réelle de chantier pour rendre la page plus crédible et plus concrète.</p>
                </article>
                <article class="services-hub-gallery__card card">
                    <div class="services-hub-gallery__media services-hub-gallery__media--placeholder"><span>Emplacement photo 2</span></div>
                    <h3>Texte technique ou argument commercial</h3>
                    <p>Expliquez votre méthode, votre rapidité d’intervention ou votre spécialisation métier.</p>
                </article>
                <article class="services-hub-gallery__card card">
                    <div class="services-hub-gallery__media services-hub-gallery__media--placeholder"><span>Emplacement photo 3</span></div>
                    <h3>Avant / après, maintenance, installation</h3>
                    <p>Montrez vos réalisations et structurez votre page pour qu’elle inspire confiance dès le premier écran.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section services-hub-section">
        <div class="container">
            <div class="section-heading section-heading--center">
                <p class="eyebrow">Pourquoi cette page ?</p>
                <h2>Une page générale qui ouvre vers les bonnes pages métier</h2>
            </div>
            <div class="services-hub-benefits">
                <article class="card services-hub-benefit"><strong>Navigation claire</strong><p>Le visiteur comprend immédiatement vos domaines d’intervention et choisit son besoin.</p></article>
                <article class="card services-hub-benefit"><strong>Meilleure conversion</strong><p>Des blocs clairs, des photos, du texte rassurant et des boutons visibles augmentent la prise de contact.</p></article>
                <article class="card services-hub-benefit"><strong>Base SEO locale</strong><p>Chaque service peut ensuite avoir sa propre page métier optimisée par ville ou par type d’intervention.</p></article>
            </div>
        </div>
    </section>

    <section class="section section--soft services-hub-section">
        <div class="container">
            <div class="services-hub-cta card">
                <div>
                    <p class="eyebrow">Besoin d’un accompagnement ?</p>
                    <h2>Un doute sur le service à choisir ? EMAE vous oriente rapidement.</h2>
                    <p>Expliquez votre besoin, nous vous redirigeons vers le bon métier et la bonne intervention.</p>
                </div>
                <div class="hero__actions">
                    <a class="btn btn--primary" href="<?= e(company_phone_link()) ?>">Appeler maintenant</a>
                    <a class="btn btn--outline" href="<?= e(route_url('quote')) ?>">Demander un devis</a>
                </div>
            </div>
        </div>
    </section>
    <?php
    render_footer();
    exit;
}

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
    $serviceContext = mb_strtolower(trim($route . ' ' . (string) ($page['slug'] ?? '') . ' ' . (string) ($page['title'] ?? '')), 'UTF-8');
    $serviceTemplateKey = null;
    if (str_contains($serviceContext, 'electric')) {
        $serviceTemplateKey = 'electricite';
    } elseif (str_contains($serviceContext, 'plomb')) {
        $serviceTemplateKey = 'plomberie';
    } elseif (str_contains($serviceContext, 'chauff')) {
        $serviceTemplateKey = 'chauffage';
    } elseif (str_contains($serviceContext, 'clim') || str_contains($serviceContext, 'cvc')) {
        $serviceTemplateKey = 'climatisation';
    }

    $serviceTemplates = [
        'electricite' => [
            'eyebrow' => 'Interventions électricité',
            'hero_title' => 'Votre électricien EMAE pour le dépannage, la mise en sécurité et la rénovation.',
            'hero_lead' => 'Intervention rapide, communication claire et accompagnement professionnel pour vos pannes, tableaux électriques et remises en conformité.',
            'interventions_title' => 'Nos interventions en électricité',
            'interventions_lead' => 'Voici les situations dans lesquelles EMAE peut intervenir rapidement.',
            'interventions' => [
                ['icon' => '⚡', 'title' => 'Panne électrique', 'text' => 'Recherche de panne et remise en service de vos circuits et équipements.'],
                ['icon' => '🧰', 'title' => 'Tableau électrique', 'text' => 'Réparation, remplacement ou sécurisation de tableau et protections.'],
                ['icon' => '🔌', 'title' => 'Prises et circuits', 'text' => 'Dépannage des prises, lignes dédiées et circuits défaillants.'],
                ['icon' => '💡', 'title' => 'Éclairage', 'text' => 'Remise en service et modernisation de vos éclairages intérieurs et extérieurs.'],
                ['icon' => '🏗️', 'title' => 'Installation électrique', 'text' => 'Ajout ou création d’alimentations pour vos équipements techniques.'],
                ['icon' => '🛡️', 'title' => 'Mise aux normes', 'text' => 'Mise en sécurité, protections adaptées et conformité des installations.'],
                ['icon' => '🏢', 'title' => 'Rénovation', 'text' => 'Modernisation électrique en logement, commerce ou bâtiment technique.'],
                ['icon' => '🚨', 'title' => 'Urgence 24/7', 'text' => 'Astreinte et intervention pour limiter l’arrêt d’activité et sécuriser le site.'],
            ],
            'trust_title' => 'Besoin d’un électricien ?',
            'trust_lead' => 'Une intervention claire, professionnelle et adaptée à votre bâtiment.',
            'benefits' => [
                'Techniciens qualifiés et intervention sécurisée',
                'Diagnostic clair avant travaux complémentaires',
                'Déplacement rapide selon votre zone',
                'Compte rendu et facturation propre en fin d’intervention',
            ],
            'highlight_title' => 'Dépannage électrique & mise en sécurité',
            'highlight_text' => 'Devis annoncé avant intervention • Recherche de panne • Remise en service',
            'zones_title' => 'Nos zones d’intervention 24h/24',
            'zones_lead' => 'Interventions sur vos secteurs couverts avec organisation claire des urgences et des rendez-vous.',
            'zones' => ['Paris (75)', 'Seine-et-Marne (77)', 'Yvelines (78)', 'Essonne (91)', 'Hauts-de-Seine (92)', 'Seine-Saint-Denis (93)', 'Val-de-Marne (94)', 'Val-d’Oise (95)'],
            'faq' => [
                ['q' => 'Intervenez-vous pour les pannes électriques urgentes ?', 'a' => 'Oui, EMAE organise les demandes urgentes pour sécuriser l’installation, rechercher la panne et remettre en service quand cela est possible.'],
                ['q' => 'Pouvez-vous remplacer un tableau électrique ?', 'a' => 'Oui, nous pouvons diagnostiquer l’existant, sécuriser les départs et proposer un remplacement ou une modernisation adaptée.'],
                ['q' => 'Faites-vous les remises en conformité ?', 'a' => 'Oui, nous intervenons pour la mise en sécurité et les travaux électriques nécessaires à une installation plus fiable.'],
                ['q' => 'Intervenez-vous aussi pour l’éclairage ?', 'a' => 'Oui, dépannage, remplacement, modernisation LED et remise en service des circuits d’éclairage.'],
                ['q' => 'Donnez-vous un devis avant travaux ?', 'a' => 'Oui, la situation est expliquée clairement et la suite de l’intervention est annoncée avant validation.'],
                ['q' => 'Travaillez-vous en logement et en local professionnel ?', 'a' => 'Oui, EMAE intervient aussi bien sur l’habitat, les commerces que les bâtiments techniques.'],
            ],
        ],
        'plomberie' => [
            'eyebrow' => 'Interventions plomberie',
            'hero_title' => 'Votre plombier EMAE pour les fuites, réparations sanitaires et remises en service.',
            'hero_lead' => 'Recherche de fuite, dépannage rapide et maintenance courante avec une communication claire avant intervention.',
            'interventions_title' => 'Nos interventions en plomberie',
            'interventions_lead' => 'Voici les situations dans lesquelles EMAE peut intervenir rapidement.',
            'interventions' => [
                ['icon' => '💧', 'title' => 'Recherche de fuite', 'text' => 'Localisation et réparation des fuites visibles ou suspectées sur vos réseaux.'],
                ['icon' => '🚿', 'title' => 'Sanitaires', 'text' => 'Réparation de WC, robinetterie, mécanismes, évacuations et accessoires sanitaires.'],
                ['icon' => '🧯', 'title' => 'Urgence plomberie', 'text' => 'Mise en sécurité temporaire pour limiter les dégâts et rétablir le service.'],
                ['icon' => '🔩', 'title' => 'Réseaux d’eau', 'text' => 'Intervention sur tuyauteries, alimentation, petits réseaux et raccordements.'],
                ['icon' => '🛁', 'title' => 'Équipements', 'text' => 'Remplacement d’évier, robinet, chasse d’eau, siphon ou appareil sanitaire.'],
                ['icon' => '🧼', 'title' => 'Entretien courant', 'text' => 'Maintenance et vérifications pour limiter les dysfonctionnements récurrents.'],
                ['icon' => '🏢', 'title' => 'Locaux & commerces', 'text' => 'Organisation des interventions sur bâtiment occupé ou activité ouverte.'],
                ['icon' => '📋', 'title' => 'Diagnostic clair', 'text' => 'Compte rendu simple, proposition d’action et chiffrage lisible.'],
            ],
            'trust_title' => 'Besoin d’un plombier ?',
            'trust_lead' => 'Une prise en charge rapide pour limiter l’impact sur votre bâtiment.',
            'benefits' => [
                'Recherche de fuite et remise en service rapide',
                'Intervention propre et sécurisée',
                'Communication claire avant remplacement de matériel',
                'Facturation conforme et traçable',
            ],
            'highlight_title' => 'Fuite, dépannage sanitaire & maintenance',
            'highlight_text' => 'Diagnostic • Réparation • Remplacement d’équipements',
            'zones_title' => 'Nos zones d’intervention 24h/24',
            'zones_lead' => 'Organisation des urgences et rendez-vous plomberie selon votre secteur.',
            'zones' => ['Paris (75)', 'Seine-et-Marne (77)', 'Val-de-Marne (94)', 'Occitanie', 'Locaux professionnels', 'Commerces', 'Habitat', 'Maintenance planifiée'],
            'faq' => [
                ['q' => 'Pouvez-vous intervenir pour une fuite urgente ?', 'a' => 'Oui, EMAE peut sécuriser, diagnostiquer et proposer la remise en service adaptée selon la panne constatée.'],
                ['q' => 'Faites-vous le remplacement de robinetterie ?', 'a' => 'Oui, nous remplaçons les équipements défectueux et contrôlons le bon fonctionnement après intervention.'],
                ['q' => 'Intervenez-vous sur des locaux professionnels ?', 'a' => 'Oui, nous adaptons l’intervention pour limiter l’impact sur l’activité du site.'],
                ['q' => 'Le devis est-il annoncé avant travaux ?', 'a' => 'Oui, les actions complémentaires sont expliquées avant validation.'],
                ['q' => 'Travaillez-vous aussi sur l’entretien ?', 'a' => 'Oui, EMAE réalise aussi des actions de maintenance courante et de remise en état.'],
                ['q' => 'Intervenez-vous sur l’évacuation et les siphons ?', 'a' => 'Oui, selon la demande, nous traitons aussi les organes d’évacuation et les raccordements courants.'],
            ],
        ],
        'chauffage' => [
            'eyebrow' => 'Interventions chauffage',
            'hero_title' => 'Votre chauffagiste EMAE pour le diagnostic, le dépannage et l’optimisation des équipements.',
            'hero_lead' => 'EMAE intervient pour remettre en service vos installations de chauffage et améliorer leur fonctionnement.',
            'interventions_title' => 'Nos interventions en chauffage',
            'interventions_lead' => 'Voici les situations dans lesquelles EMAE peut intervenir rapidement.',
            'interventions' => [
                ['icon' => '🔥', 'title' => 'Panne chauffage', 'text' => 'Diagnostic de défaut et remise en service des équipements de chauffage.'],
                ['icon' => '🌡️', 'title' => 'Régulation', 'text' => 'Contrôle des thermostats, sondes et organes de commande.'],
                ['icon' => '♨️', 'title' => 'Production de chaleur', 'text' => 'Vérification des organes essentiels et continuité de fonctionnement.'],
                ['icon' => '🛠️', 'title' => 'Réglages', 'text' => 'Optimisation des réglages pour améliorer confort et stabilité.'],
                ['icon' => '🏢', 'title' => 'Site occupé', 'text' => 'Organisation des interventions sur logements, bureaux et bâtiments techniques.'],
                ['icon' => '📊', 'title' => 'Contrôle de fonctionnement', 'text' => 'Vérification visuelle et contrôle des points importants après remise en service.'],
                ['icon' => '🚨', 'title' => 'Urgence de continuité', 'text' => 'Prise en charge des pannes pour limiter l’arrêt de chauffage.'],
                ['icon' => '📋', 'title' => 'Compte rendu clair', 'text' => 'Explication de la panne, des actions et des suites recommandées.'],
            ],
            'trust_title' => 'Besoin d’un chauffagiste ?',
            'trust_lead' => 'Une intervention structurée pour retrouver rapidement du confort et de la continuité.',
            'benefits' => [
                'Diagnostic clair et remise en service',
                'Réglages et optimisation des performances',
                'Intervention adaptée à l’exploitation du site',
                'Compte rendu et préconisations utiles',
            ],
            'highlight_title' => 'Dépannage chauffage & optimisation',
            'highlight_text' => 'Diagnostic • Contrôle • Remise en service',
            'zones_title' => 'Nos zones d’intervention 24h/24',
            'zones_lead' => 'Organisation des urgences chauffage selon vos zones couvertes.',
            'zones' => ['Île-de-France', 'Occitanie', 'Paris', 'Seine-et-Marne', 'Val-de-Marne', 'Locaux techniques', 'Commerces', 'Habitat'],
            'faq' => [
                ['q' => 'Pouvez-vous intervenir pour un chauffage qui ne démarre plus ?', 'a' => 'Oui, EMAE réalise le diagnostic initial et intervient pour remettre en service quand cela est possible.'],
                ['q' => 'Faites-vous les réglages de régulation ?', 'a' => 'Oui, nous pouvons contrôler et ajuster les réglages utiles au bon fonctionnement.'],
                ['q' => 'Intervenez-vous sur des bâtiments occupés ?', 'a' => 'Oui, l’intervention est organisée pour limiter les gênes et sécuriser le site.'],
                ['q' => 'Le devis est-il annoncé avant la suite ?', 'a' => 'Oui, les actions complémentaires sont expliquées avant validation.'],
                ['q' => 'Faites-vous aussi la maintenance ?', 'a' => 'Oui, EMAE peut intervenir dans une logique de dépannage comme de maintenance.'],
                ['q' => 'Travaillez-vous sur chauffage collectif ou individuel ?', 'a' => 'Nous adaptons l’intervention au contexte du site et à la nature de l’installation.'],
            ],
        ],
        'climatisation' => [
            'eyebrow' => 'Interventions climatisation',
            'hero_title' => 'Votre spécialiste EMAE pour le dépannage, l’entretien et la remise en service de climatisation.',
            'hero_lead' => 'Diagnostic de dysfonctionnement, entretien courant et contrôle des performances pour vos installations de rafraîchissement.',
            'interventions_title' => 'Nos interventions en climatisation',
            'interventions_lead' => 'Voici les situations dans lesquelles EMAE peut intervenir rapidement.',
            'interventions' => [
                ['icon' => '❄️', 'title' => 'Panne climatisation', 'text' => 'Diagnostic et remise en service des équipements de climatisation et rafraîchissement.'],
                ['icon' => '🧼', 'title' => 'Entretien courant', 'text' => 'Nettoyage, vérifications visuelles et remise en état des points accessibles.'],
                ['icon' => '🌬️', 'title' => 'Qualité de soufflage', 'text' => 'Contrôle du fonctionnement et de la diffusion d’air.'],
                ['icon' => '🛠️', 'title' => 'Réglages', 'text' => 'Optimisation des paramètres utiles au confort et à la stabilité de fonctionnement.'],
                ['icon' => '🏢', 'title' => 'Locaux & commerces', 'text' => 'Interventions compatibles avec l’exploitation du site et les usages.'],
                ['icon' => '📈', 'title' => 'Contrôle des performances', 'text' => 'Analyse simple du comportement de l’installation après intervention.'],
                ['icon' => '🚨', 'title' => 'Dépannage prioritaire', 'text' => 'Prise en charge rapide des défauts bloquants ou gênants.'],
                ['icon' => '📋', 'title' => 'Compte rendu', 'text' => 'Explication claire de la situation, des actions menées et des suites.'],
            ],
            'trust_title' => 'Besoin d’un spécialiste climatisation ?',
            'trust_lead' => 'Une intervention claire pour retrouver du confort et un fonctionnement stable.',
            'benefits' => [
                'Diagnostic clair du dysfonctionnement',
                'Entretien et remise en service',
                'Contrôle des performances après intervention',
                'Organisation adaptée à votre bâtiment',
            ],
            'highlight_title' => 'Dépannage, entretien & remise en service',
            'highlight_text' => 'Confort • Contrôle • Réponse rapide',
            'zones_title' => 'Nos zones d’intervention 24h/24',
            'zones_lead' => 'Interventions sur vos secteurs couverts avec organisation simple des urgences et rendez-vous.',
            'zones' => ['Île-de-France', 'Occitanie', 'Paris', 'Seine-et-Marne', 'Val-de-Marne', 'Bureaux', 'Commerces', 'Sites techniques'],
            'faq' => [
                ['q' => 'Pouvez-vous intervenir pour une climatisation qui ne souffle plus correctement ?', 'a' => 'Oui, EMAE peut diagnostiquer la panne, contrôler le fonctionnement et proposer la remise en service adaptée.'],
                ['q' => 'Faites-vous l’entretien courant ?', 'a' => 'Oui, entretien, nettoyage et vérifications utiles selon le contexte de l’installation.'],
                ['q' => 'Intervenez-vous sur des commerces ou bureaux ?', 'a' => 'Oui, nous adaptons l’intervention au site et à ses contraintes d’exploitation.'],
                ['q' => 'Le devis est-il annoncé avant travaux complémentaires ?', 'a' => 'Oui, les suites éventuelles sont expliquées avant validation.'],
                ['q' => 'Travaillez-vous aussi sur les réglages ?', 'a' => 'Oui, nous pouvons optimiser les réglages utiles au confort et à la stabilité de fonctionnement.'],
                ['q' => 'Proposez-vous un compte rendu de passage ?', 'a' => 'Oui, nous expliquons les actions réalisées et les points à surveiller après intervention.'],
            ],
        ],
    ];

    if ($serviceTemplateKey !== null && isset($serviceTemplates[$serviceTemplateKey])) {
        $serviceData = $serviceTemplates[$serviceTemplateKey];
        $meta = seo_defaults($route, $page);
        render_head($meta);
        render_header(route_url($route));
        ?>
        <section class="page-hero page-hero--service-emae">
            <div class="container service-hero__grid">
                <div class="service-hero__content">
                    <p class="eyebrow"><?= e($serviceData['eyebrow']) ?></p>
                    <h1><?= e($page['title']) ?></h1>
                    <p class="service-hero__lead"><?= e($serviceData['hero_lead']) ?></p>
                    <div class="hero__chips service-hero__chips">
                        <span><?= e($serviceData['interventions'][0]['title']) ?></span>
                        <span>Intervention rapide</span>
                        <span><?= e(company_regions()) ?></span>
                    </div>
                    <div class="hero__actions service-hero__actions">
                        <a class="btn btn--primary" href="<?= e(company_phone_link()) ?>">Nous appeler maintenant</a>
                        <a class="btn btn--outline" href="<?= e(route_url('quote')) ?>">Demander un devis</a>
                    </div>
                </div>
                <div class="service-hero__summary card">
                    <p class="eyebrow eyebrow--light">Intervention EMAE</p>
                    <h2><?= e($serviceData['hero_title']) ?></h2>
                    <ul class="service-hero__points">
                        <li>Diagnostic clair avant toute suite d’intervention.</li>
                        <li>Communication simple avec un interlocuteur technique.</li>
                        <li>Organisation adaptée au logement, commerce ou bâtiment technique.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section section--soft service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow"><?= e($serviceData['eyebrow']) ?></p>
                    <h2><?= e($serviceData['interventions_title']) ?></h2>
                    <p><?= e($serviceData['interventions_lead']) ?></p>
                </div>
                <div class="service-interventions-grid">
                    <?php foreach ($serviceData['interventions'] as $item): ?>
                        <article class="service-intervention-card card">
                            <div class="service-intervention-card__icon"><?= e($item['icon']) ?></div>
                            <h3><?= e($item['title']) ?></h3>
                            <p><?= e($item['text']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow">Accompagnement</p>
                    <h2><?= e($serviceData['trust_title']) ?></h2>
                    <p><?= e($serviceData['trust_lead']) ?></p>
                </div>
                <div class="service-trust card">
                    <div class="service-trust__grid">
                        <?php foreach ($serviceData['benefits'] as $benefit): ?>
                            <div class="service-trust__item">
                                <strong><?= e($benefit) ?></strong>
                                <p>Une intervention structurée, claire et adaptée à votre besoin.</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="service-trust__highlight">
                        <p class="service-trust__label"><?= e($serviceData['highlight_title']) ?></p>
                        <h3><?= e($page['title']) ?></h3>
                        <p><?= e($serviceData['highlight_text']) ?></p>
                    </div>
                    <div class="service-trust__footer">
                        <span>Intervention sécurisée et communication claire</span>
                        <a class="btn btn--primary" href="<?= e(company_phone_link()) ?>">Nous appeler maintenant</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--soft service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow">Process</p>
                    <h2>Process en 3 étapes</h2>
                    <p>Intervention rapide, communication transparente, résultat garanti.</p>
                </div>
                <div class="service-steps-grid">
                    <article class="service-step card"><span class="service-step__number">1</span><h3>Prise de contact</h3><p>Appelez-nous ou remplissez le formulaire pour décrire votre besoin.</p></article>
                    <article class="service-step card"><span class="service-step__number">2</span><h3>Déplacement rapide</h3><p>Organisation de l’intervention selon votre zone et le niveau de priorité.</p></article>
                    <article class="service-step card"><span class="service-step__number">3</span><h3>Intervention & suivi</h3><p>Diagnostic, action technique, compte rendu et solutions complémentaires si besoin.</p></article>
                </div>
            </div>
        </section>

        <section class="section service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow">Zones</p>
                    <h2><?= e($serviceData['zones_title']) ?></h2>
                    <p><?= e($serviceData['zones_lead']) ?></p>
                </div>
                <div class="service-zones card">
                    <div class="city-list service-zones__chips">
                        <?php foreach ($serviceData['zones'] as $zone): ?>
                            <span><?= e($zone) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--soft service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow">Questions fréquentes</p>
                    <h2>FAQ <?= e(mb_strtolower((string) $page['title'], 'UTF-8')) ?></h2>
                </div>
                <div class="service-faq-list">
                    <?php foreach ($serviceData['faq'] as $faq): ?>
                        <details class="service-faq-item card">
                            <summary><?= e($faq['q']) ?></summary>
                            <p><?= e($faq['a']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section service-page-section">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <p class="eyebrow">Contact</p>
                    <h2>Contactez votre <?= e(mb_strtolower((string) $page['title'], 'UTF-8')) ?></h2>
                    <p>Besoin d’une intervention ou d’un devis ? EMAE vous recontacte rapidement.</p>
                </div>
                <div class="split-panel service-contact-panel">
                    <div class="card form-card">
                        <h3>Un technicien vous contacte dès réception.</h3>
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
                            <label>Service<select name="service_type"><option value="<?= e($page['title']) ?>"><?= e($page['title']) ?></option><?php foreach (home_cards() as $card): ?><option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option><?php endforeach; ?></select></label>
                            <label>Votre besoin<textarea name="message" required></textarea></label>
                            <button class="btn btn--primary btn--block" type="submit">Envoyer ma demande</button>
                        </form>
                    </div>
                    <div class="service-contact-side">
                        <div class="card service-contact-card">
                            <h3>Informations de contact</h3>
                            <div class="contact-list">
                                <div><strong>Téléphone</strong><a href="<?= e(company_phone_link()) ?>"><?= e(company_phone()) ?></a></div>
                                <div><strong>Zone d’intervention</strong><span><?= e(company_regions()) ?></span></div>
                                <div><strong>Horaires</strong><span><?= e(company_hours()) ?></span></div>
                            </div>
                        </div>
                        <div class="card service-contact-card">
                            <h3>Pourquoi nous contacter ?</h3>
                            <ul class="service-contact-benefits">
                                <li>Devis gratuit et sans engagement</li>
                                <li>Intervention rapide selon votre zone</li>
                                <li>Technicien qualifié et intervention sécurisée</li>
                                <li>Facture conforme et suivi clair</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (trim((string) ($page['content_html'] ?? '')) !== ''): ?>
            <section class="section service-page-section service-page-section--content">
                <div class="container">
                    <article class="card rich-content"><?= $page['content_html'] ?></article>
                </div>
            </section>
        <?php endif; ?>
        <?php render_footer(); exit; }

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
