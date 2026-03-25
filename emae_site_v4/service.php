<?php
require_once __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$service = find_service($slug);
if (!$service) {
    http_response_code(404);
    $service = [
        'title' => 'Service introuvable',
        'hero' => 'La page demandée n’existe pas ou n’est plus disponible.',
        'bullets' => ['Retournez vers la page Services ou utilisez le formulaire de contact.'],
        'image' => asset('img/hero-van.jpg'),
        'badge' => '•',
        'seo' => '',
        'slug' => '',
    ];
}

$meta = page_meta([
    'title' => $service['title'] . ' | EMAE',
    'description' => $service['hero'],
    'keywords' => $service['seo'] . ', EMAE, dépannage, maintenance',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero--service">
    <div class="container page-hero__split">
        <div>
            <p class="eyebrow">Service métier</p>
            <h1><?= e($service['title']) ?></h1>
            <p><?= e($service['hero']) ?></p>
            <div class="hero__actions">
                <a class="btn btn--primary" href="quote.php">Demander un devis</a>
                <a class="btn btn--outline" href="<?= COMPANY_PHONE_LINK ?>">Appel urgent</a>
            </div>
        </div>
        <div class="page-hero__thumb reveal">
            <img src="<?= e($service['image']) ?>" alt="<?= e($service['title']) ?>">
        </div>
    </div>
</section>

<section class="section">
    <div class="container split-panel split-panel--form">
        <article class="card reveal">
            <p class="eyebrow">Prise en charge</p>
            <h2>Ce que la page doit rassurer immédiatement</h2>
            <ul class="feature-list">
                <?php foreach ($service['bullets'] as $bullet): ?>
                    <li><?= e($bullet) ?></li>
                <?php endforeach; ?>
                <li>Message clair sur le dépannage et la réactivité.</li>
                <li>CTA visibles pour l’appel et la demande de devis.</li>
                <li>Ton professionnel, simple et compréhensible sur mobile.</li>
            </ul>

            <div class="info-stack info-stack--spaced">
                <div class="info-stack__item"><strong>Approche</strong><span>Diagnostic, intervention ciblée, compte-rendu clair</span></div>
                <div class="info-stack__item"><strong>Clients visés</strong><span>Particuliers, syndics, commerces, petits tertiaires</span></div>
                <div class="info-stack__item"><strong>Zones</strong><span>Île-de-France et Occitanie</span></div>
            </div>
        </article>

        <aside class="card form-card reveal">
            <p class="eyebrow">Être rappelé</p>
            <h2>Demande liée à <?= e($service['title']) ?></h2>
            <form action="actions/contact_submit.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <label>Nom complet
                    <input type="text" name="full_name" required>
                </label>
                <label>Email
                    <input type="email" name="email" required>
                </label>
                <label>Téléphone
                    <input type="tel" name="phone">
                </label>
                <label>Objet
                    <input type="text" name="subject" value="<?= e('Demande sur ' . $service['title']) ?>" required>
                </label>
                <label>Message
                    <textarea name="message" required>Bonjour, je souhaite être recontacté pour une demande liée à <?= e($service['title']) ?>.</textarea>
                </label>
                <button class="btn btn--primary btn--block" type="submit">Être rappelé</button>
            </form>
        </aside>
    </div>
</section>

<section class="section section--soft">
    <div class="container">
        <div class="section-title reveal">
            <p class="eyebrow">Maillage interne</p>
            <h2>Services complémentaires</h2>
        </div>
        <div class="tag-grid tag-grid--services">
            <?php foreach (service_pages() as $other): ?>
                <?php if ($other['slug'] !== $service['slug']): ?>
                    <a class="tag tag--link" href="service.php?slug=<?= e($other['slug']) ?>"><?= e($other['title']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
