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
    if ($button1Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $button1Href)) $button1Href = route_url($button1Href);
    $button2Href = trim((string) $hero['button2_url']);
    if ($button2Href !== '' && !preg_match('#^(https?:|tel:|mailto:|/)#i', $button2Href)) $button2Href = route_url($button2Href);
    render_head($meta);
    render_header(route_url(''));
    ?>
    <?php
    $quoteEyebrow = trim((string) ($hero['quote_eyebrow'] ?? ''));
    if ($quoteEyebrow === '' || strtolower($quoteEyebrow) === strtolower('DEMANDE DE DEVIS GRATUITE')) {
        $quoteEyebrow = 'Demande rapide';
    }
    $quoteTitle = trim((string) ($hero['quote_title'] ?? ''));
    if ($quoteTitle === '' || strtolower($quoteTitle) === strtolower('Obtenir un rappel rapide')) {
        $quoteTitle = 'Être rappelé';
    }
    $quoteButtonLabel = trim((string) ($hero['quote_button_label'] ?? ''));
    if ($quoteButtonLabel === '' || strtolower($quoteButtonLabel) === strtolower('Continuer')) {
        $quoteButtonLabel = 'Être rappelé';
    }
    ?>
    <section class="hero hero--home">
        <div class="container hero__grid">
            <div class="hero__content">
                <?php if (trim((string) $hero['eyebrow']) !== ''): ?><p class="eyebrow"><?= e($hero['eyebrow']) ?></p><?php endif; ?>
                <?php if (trim((string) $hero['title']) !== ''): ?><h1><?= nl2br(e($hero['title'])) ?></h1><?php endif; ?>
                <?php if (trim((string) $hero['lead']) !== ''): ?><p class="hero__lead"><?= nl2br(e($hero['lead'])) ?></p><?php endif; ?>
                <?php if (!empty($hero['chips'])): ?><div class="hero__chips"><?php foreach ($hero['chips'] as $chip): ?><span><?= e($chip) ?></span><?php endforeach; ?></div><?php endif; ?>
                <?php if (trim((string) $hero['button1_label']) !== '' || trim((string) $hero['button2_label']) !== ''): ?>
                    <div class="hero__actions">
                        <?php if (trim((string) $hero['button1_label']) !== '' && $button1Href !== ''): ?><a class="btn btn--primary" href="<?= e($button1Href) ?>"><?= e($hero['button1_label']) ?></a><?php endif; ?>
                        <?php if (trim((string) $hero['button2_label']) !== '' && $button2Href !== ''): ?><a class="btn btn--outline" href="<?= e($button2Href) ?>"><?= e($hero['button2_label']) ?></a><?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($features)): ?>
                    <div class="hero__feature-ribbon">
                        <?php foreach ($features as $index => $feature): ?>
                            <span class="hero__feature-item">
                                <?php if ($feature['title'] !== ''): ?><strong><?= e($feature['title']) ?></strong><?php endif; ?>
                                <?php if ($feature['text'] !== ''): ?><span><?= e($feature['text']) ?></span><?php endif; ?>
                            </span>
                            <?php if ($index < count($features) - 1): ?><span class="hero__feature-separator">•</span><?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="hero__visual hero__visual--hero-form reveal">
                <div class="hero-quote-card hero-quote-card--hero">
                    <?php if ($quoteEyebrow !== ''): ?><p class="hero-quote-card__eyebrow"><?= e($quoteEyebrow) ?></p><?php endif; ?>
                    <?php if ($quoteTitle !== ''): ?><h2><?= e($quoteTitle) ?></h2><?php endif; ?>
                    <form action="<?= e(route_url('quote')) ?>" method="post" class="hero-quote-card__form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="form_type" value="quote">
                        <input type="hidden" name="message" value="Demande rapide depuis le hero - merci de me rappeler.">
                        <input type="hidden" name="urgency" value="Urgente">
                        <input type="text" name="website" value="" class="hp-field" tabindex="-1" autocomplete="off">
                        <div class="form-grid form-grid--hero">
                            <label>Nom complet<input type="text" name="full_name" required></label>
                            <label>Téléphone<input type="tel" name="phone" required></label>
                        </div>
                        <div class="form-grid form-grid--hero">
                            <label><?= e($hero['quote_service_label']) ?><select name="service_type"><option value="">Choisir un service</option><?php foreach ($cards as $card): ?><option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option><?php endforeach; ?></select></label>
                            <label><?= e($hero['quote_city_label']) ?><input type="text" name="city" placeholder="<?= e($hero['quote_city_placeholder']) ?>"></label>
                        </div>
                        <button class="btn btn--primary btn--block" type="submit"><?= e($quoteButtonLabel) ?></button>
                        <?php if (trim((string) $hero['quote_meta']) !== ''): ?><p class="hero-quote-card__meta"><?= e($hero['quote_meta']) ?></p><?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="section section--tight service-band"><div class="container"><div class="service-showcase service-showcase--v10"><?php foreach ($cards as $card): ?><a class="service-media-card reveal" href="<?= e(route_url($card['link'])) ?>"><img src="<?= e(asset_url($card['image'])) ?>" alt="<?= e($card['title']) ?>"><div class="service-media-card__shade"></div><div class="service-media-card__body"><h2><?= e($card['title']) ?></h2></div></a><?php endforeach; ?></div></div></section>
    <section class="section section--soft"><div class="container split-panel"><div class="card"><p class="eyebrow">Avis clients</p><h2>Des témoignages qui rassurent</h2><div class="reviews-grid"><?php foreach ($reviews as $review): ?><article class="review-card"><div class="review-card__stars"><?= str_repeat('★', (int) $review['rating']) ?></div><h3><?= e($review['author_name']) ?></h3><p><?= e($review['content']) ?></p></article><?php endforeach; ?></div></div><div class="card form-card quote-panel"><p class="eyebrow">Demande de devis rapide</p><h2>Parlez-nous de votre besoin</h2><p class="quote-panel__lead">Décris ton besoin en quelques lignes et EMAE te recontacte rapidement avec une solution claire.</p><form action="<?= e(route_url('quote')) ?>" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="form_type" value="quote"><input type="text" name="website" value="" class="hp-field" tabindex="-1" autocomplete="off"><div class="form-grid"><label>Nom complet<input type="text" name="full_name" required></label><label>Téléphone<input type="tel" name="phone" required></label></div><div class="form-grid"><label>Email<input type="email" name="email"></label><label>Ville<input type="text" name="city"></label></div><label>Service<select name="service_type"><option value="">Choisir</option><?php foreach ($cards as $card): ?><option value="<?= e($card['title']) ?>"><?= e($card['title']) ?></option><?php endforeach; ?></select></label><label>Votre besoin<textarea name="message" required></textarea></label><label>Urgence<select name="urgency"><option>Normale</option><option>Urgente</option><option>Très urgente</option></select></label><button class="btn btn--primary btn--block" type="submit"><?= e(quote_form_options()['submit_label']) ?></button></form></div></div></section>
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
