<?php
require_once __DIR__ . '/includes/bootstrap.php';
$meta = page_meta([
    'title' => 'Mentions légales | EMAE',
    'description' => 'Mentions légales EMAE à finaliser avant publication.',
    'keywords' => 'mentions légales EMAE',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Mentions légales</p>
        <h1>Page prête à compléter avant publication</h1>
        <p>Cette version 2 structure correctement la page légale, mais certaines données restent volontairement à compléter par vos informations officielles.</p>
    </div>
</section>

<section class="section">
    <div class="container legal-layout">
        <article class="card reveal">
            <h2>Éditeur du site</h2>
            <ul class="feature-list">
                <li><strong>Raison sociale :</strong> EMAE - Entreprise Multitechnique Avancée</li>
                <li><strong>Adresse :</strong> Adresse à compléter</li>
                <li><strong>SIREN / SIRET :</strong> À compléter</li>
                <li><strong>Forme juridique :</strong> À compléter</li>
                <li><strong>Email :</strong> <?= e(COMPANY_EMAIL) ?></li>
                <li><strong>Téléphone :</strong> <?= e(COMPANY_PHONE) ?></li>
            </ul>
        </article>

        <article class="card reveal">
            <h2>Publication et hébergement</h2>
            <ul class="feature-list">
                <li><strong>Responsable de publication :</strong> À compléter</li>
                <li><strong>Hébergeur :</strong> À compléter lors de la mise en ligne</li>
                <li><strong>Nom de domaine :</strong> À compléter</li>
                <li><strong>TVA intracommunautaire :</strong> À compléter si applicable</li>
            </ul>
        </article>

        <article class="card reveal">
            <h2>Important avant publication</h2>
            <ul class="feature-list">
                <li>Renseigner vos vraies informations légales.</li>
                <li>Ajouter l’hébergeur réel du site.</li>
                <li>Vérifier les coordonnées affichées.</li>
                <li>Compléter les mentions éventuelles liées à votre activité.</li>
            </ul>
        </article>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
