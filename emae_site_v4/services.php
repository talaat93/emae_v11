<?php
require_once __DIR__ . '/includes/bootstrap.php';
$services = service_pages();
$meta = page_meta([
    'title' => 'Services EMAE | Électricité, plomberie, chauffage, climatisation, CVC, PAC',
    'description' => 'Toutes les pages métier EMAE pour capter les demandes en dépannage, maintenance et travaux ciblés.',
    'keywords' => 'services EMAE, dépannage chauffage, dépannage climatisation, électricien, plombier, maintenance multitechnique',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero--services">
    <div class="container">
        <p class="eyebrow">Services</p>
        <h1>Des pages métier dédiées pour mieux convertir</h1>
        <p>Chaque service dispose d’une page spécifique afin de mieux répondre aux recherches locales, aux campagnes Google Ads et aux demandes spontanées venant du mobile.</p>
    </div>
</section>

<section class="section">
    <div class="container grid grid--3">
        <?php foreach ($services as $service): ?>
            <article class="card service-card service-card--full reveal">
                <div class="service-card__media">
                    <img src="<?= e($service['image']) ?>" alt="<?= e($service['title']) ?>">
                </div>
                <div class="service-card__content">
                    <div class="service-card__icon"><?= e($service['badge']) ?></div>
                    <h2><?= e($service['title']) ?></h2>
                    <p><?= e($service['hero']) ?></p>
                    <ul class="mini-list">
                        <?php foreach ($service['bullets'] as $bullet): ?>
                            <li><?= e($bullet) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-actions">
                        <a class="btn btn--small btn--primary" href="service.php?slug=<?= e($service['slug']) ?>">Page dédiée</a>
                        <a class="btn btn--small btn--light" href="quote.php">Devis</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
