<?php
require_once __DIR__ . '/includes/bootstrap.php';

$meta = page_meta([
    'title' => 'EMAE | Dépannage climatisation, chauffage, électricité et plomberie',
    'description' => 'EMAE présente une offre claire pour le dépannage, la maintenance et les travaux en électricité, plomberie, chauffage, climatisation, CVC et pompes à chaleur.',
    'keywords' => 'EMAE, dépannage climatisation, chauffagiste, plombier, électricien, PAC, maintenance multitechnique',
]);
require __DIR__ . '/includes/header.php';
$homepageServices = homepage_services();
$allServices = service_pages();
$zones = zone_groups();
?>
<section class="hero hero--home">
    <div class="container hero__grid">
        <div class="hero__content">
            <p class="eyebrow">Entreprise multitechnique avancée</p>
            <h1>Le partenaire technique de vos bâtiments en Île-de-France et en Occitanie</h1>
            <p class="hero__lead">EMAE aide à capter des clients pour le dépannage, l’entretien et les besoins techniques en électricité, plomberie, chauffage, climatisation, CVC et pompes à chaleur.</p>

            <div class="hero__chips">
                <span>Électricité</span>
                <span>Plomberie</span>
                <span>CVC</span>
                <span>Climatisation</span>
                <span>Chauffage</span>
                <span>PAC</span>
            </div>

            <div class="hero__actions">
                <a class="btn btn--primary" href="quote.php">Demander un devis</a>
                <a class="btn btn--outline" href="services.php">Domaines d’intervention</a>
            </div>

            <div class="hero__highlights">
                <div><strong>Mobile first</strong><span>Prêt pour Google Ads local</span></div>
                <div><strong>Conversion rapide</strong><span>Appel, devis, contact, espace client</span></div>
                <div><strong>Image premium</strong><span>Corporate, claire et rassurante</span></div>
            </div>
        </div>

        <div class="hero__visual reveal">
            <div class="hero-visual-card">
                <img src="<?= e(asset('img/hero-van.jpg')) ?>" alt="Utilitaire EMAE">
                <div class="hero-visual-card__overlay">
                    <span class="hero-label">Urgence technique 24/7</span>
                    <span class="hero-label hero-label--light">Devis sous 24h ouvrées</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="service-showcase">
            <?php foreach ($homepageServices as $service): ?>
                <a class="service-media-card reveal" href="service.php?slug=<?= e($service['slug']) ?>">
                    <img src="<?= e($service['image']) ?>" alt="<?= e($service['title']) ?>">
                    <div class="service-media-card__shade"></div>
                    <div class="service-media-card__body">
                        <span class="service-media-card__icon"><?= e($service['badge']) ?></span>
                        <h2><?= e($service['title']) ?></h2>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section emergency-band">
    <div class="container emergency-band__inner">
        <div class="emergency-band__badge">24/7</div>
        <div>
            <p class="eyebrow eyebrow--light">Urgence technique</p>
            <h2>Astreinte visible, message clair, conversion immédiate</h2>
            <p>Le site met en avant votre numéro, le devis en ligne et des pages métier dédiées pour capter les demandes utiles dès l’arrivée sur la page d’accueil.</p>
        </div>
        <div class="emergency-band__actions">
            <a class="btn btn--primary" href="<?= COMPANY_PHONE_LINK ?>">Appel urgent</a>
            <a class="btn btn--light" href="contact.php">Envoyer un message</a>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="container">
        <div class="section-title reveal">
            <p class="eyebrow">Expertise</p>
            <h2>Notre expertise multitechnique</h2>
            <p>La version 2 du site ajoute plus de contenu métier, une vraie page zones d’intervention, des documents PDF et une structure plus crédible pour un site B2C premium.</p>
        </div>

        <div class="grid grid--4">
            <?php foreach (array_slice($allServices, 0, 4) as $service): ?>
                <article class="card feature-card reveal">
                    <div class="feature-card__icon"><?= e($service['badge']) ?></div>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['short']) ?></p>
                    <ul class="mini-list">
                        <?php foreach (array_slice($service['bullets'], 0, 3) as $bullet): ?>
                            <li><?= e($bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="btn btn--small btn--light" href="service.php?slug=<?= e($service['slug']) ?>">En savoir plus</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split-panel">
        <div class="card reveal">
            <p class="eyebrow">Pourquoi ce site convertit mieux</p>
            <h2>Des messages pensés pour les visiteurs qui cherchent un dépannage maintenant</h2>
            <ul class="feature-list">
                <?php foreach (commitments() as $commitment): ?>
                    <li><?= e($commitment) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card reveal">
            <p class="eyebrow">Profils de clients</p>
            <h2>Pour qui le site est présenté</h2>
            <div class="tag-grid">
                <?php foreach (sectors_served() as $sector): ?>
                    <span class="tag"><?= e($sector) ?></span>
                <?php endforeach; ?>
            </div>
            <div class="info-stack">
                <div class="info-stack__item"><strong>Formulaire de devis</strong><span>Accessible sans compte client</span></div>
                <div class="info-stack__item"><strong>Espace client</strong><span>Pour retrouver demandes et documents</span></div>
                <div class="info-stack__item"><strong>Chat rapide</strong><span>Assistant de préqualification sans service externe</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="container">
        <div class="section-title reveal">
            <p class="eyebrow">Zones</p>
            <h2>Des zones d’intervention visibles pour le SEO local</h2>
        </div>

        <div class="grid grid--2">
            <?php foreach ($zones as $zone): ?>
                <article class="card zone-card reveal">
                    <h3><?= e($zone['title']) ?></h3>
                    <p><?= e($zone['description']) ?></p>
                    <div class="city-list">
                        <?php foreach ($zone['cities'] as $city): ?>
                            <span><?= e($city) ?></span>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="cta-row">
            <a class="btn btn--outline" href="zones-intervention.php">Voir la page zones d’intervention</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split-panel split-panel--form">
        <div class="card reveal">
            <p class="eyebrow">Documents</p>
            <h2>Des supports téléchargeables plus professionnels</h2>
            <p>La version 2 remplace les anciens fichiers texte par de vrais PDF téléchargeables. Ces documents sont prévus comme base de travail, à personnaliser avec vos vraies conditions commerciales, assurances et références.</p>
            <div class="doc-list">
                <div class="doc-item"><span>Plaquette EMAE</span><a class="btn btn--small btn--light" href="download.php?id=1">Télécharger</a></div>
                <div class="doc-item"><span>Checklist maintenance CVC</span><a class="btn btn--small btn--light" href="login.php">Accès client</a></div>
                <div class="doc-item"><span>Guide préparation intervention</span><a class="btn btn--small btn--light" href="login.php">Accès client</a></div>
            </div>
        </div>

        <div class="card form-card reveal">
            <p class="eyebrow">Demande rapide</p>
            <h2>Être rappelé</h2>
            <form action="actions/quote_submit.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <div class="form-grid">
                    <label>Nom complet
                        <input type="text" name="full_name" required>
                    </label>
                    <label>Téléphone
                        <input type="tel" name="phone" required>
                    </label>
                </div>
                <div class="form-grid">
                    <label>Email
                        <input type="email" name="email">
                    </label>
                    <label>Ville
                        <input type="text" name="city" placeholder="Ex : Meaux, Paris, Toulouse">
                    </label>
                </div>
                <label>Service
                    <select name="service_type" required>
                        <option value="">Choisir</option>
                        <?php foreach ($allServices as $service): ?>
                            <option><?= e($service['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Description
                    <textarea name="message" required placeholder="Décrivez brièvement la panne ou le besoin."></textarea>
                </label>
                <label class="small checkbox-line">
                    <input type="checkbox" name="consent" value="1" required>
                    J’accepte d’être recontacté par EMAE pour ma demande.
                </label>
                <button class="btn btn--primary btn--block" type="submit">Envoyer ma demande</button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
