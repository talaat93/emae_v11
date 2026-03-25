<?php
require_once __DIR__ . '/includes/bootstrap.php';
$items = realisations_samples();
$meta = page_meta([
    'title' => 'Réalisations | EMAE',
    'description' => 'Exemples de réalisations et chantiers types à remplacer ensuite par vos vraies références.',
    'keywords' => 'réalisations EMAE, chantier électricité, chantier plomberie, maintenance CVC',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Réalisations</p>
        <h1>Une page plus crédible pour montrer votre savoir-faire</h1>
        <p>Les cartes ci-dessous sont des exemples de structure. Il suffit ensuite de remplacer les textes par vos vrais chantiers, vos photos et vos références commerciales.</p>
    </div>
</section>

<section class="section">
    <div class="container grid grid--2">
        <?php foreach ($items as $item): ?>
            <article class="card project-card reveal">
                <div class="project-card__top">
                    <span class="tag"><?= e($item['type']) ?></span>
                    <span class="small"><?= e($item['city']) ?></span>
                </div>
                <h2><?= e($item['title']) ?></h2>
                <p><?= e($item['summary']) ?></p>
                <ul class="mini-list">
                    <li>Contexte clair</li>
                    <li>Travaux ou dépannage réalisés</li>
                    <li>Résultat attendu / obtenu</li>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section--soft">
    <div class="container card reveal">
        <p class="eyebrow">À personnaliser</p>
        <h2>Ce qu’il faudra ajouter après</h2>
        <ul class="feature-list">
            <li>Photos avant / après.</li>
            <li>Type de bâtiment et ville réelle.</li>
            <li>Nature précise de l’intervention.</li>
            <li>Valeur ajoutée apportée au client.</li>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
