<?php
require_once __DIR__ . '/includes/bootstrap.php';
$zones = zone_groups();
$meta = page_meta([
    'title' => 'Zones d’intervention | EMAE',
    'description' => 'Page dédiée aux zones d’intervention EMAE pour travailler le SEO local en Île-de-France et en Occitanie.',
    'keywords' => 'zones intervention EMAE, dépannage Île-de-France, dépannage Occitanie, Meaux, Paris, Toulouse',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Zones d’intervention</p>
        <h1>Une page locale pensée pour le référencement et les campagnes ciblées</h1>
        <p>Cette page a été ajoutée dans la version 2 pour mieux présenter les territoires couverts, les villes exemples et la logique d’intervention d’EMAE.</p>
    </div>
</section>

<section class="section">
    <div class="container grid grid--2">
        <?php foreach ($zones as $zone): ?>
            <article class="card zone-card zone-card--detailed reveal">
                <h2><?= e($zone['title']) ?></h2>
                <p><?= e($zone['description']) ?></p>
                <div class="city-list">
                    <?php foreach ($zone['cities'] as $city): ?>
                        <span><?= e($city) ?></span>
                    <?php endforeach; ?>
                </div>
                <ul class="mini-list">
                    <li>Dépannage et demandes rapides.</li>
                    <li>Maintenance, diagnostics et visites programmées.</li>
                    <li>Page prête à être enrichie avec vos vraies communes cibles.</li>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section--soft">
    <div class="container split-panel">
        <div class="card reveal">
            <p class="eyebrow">Conseil SEO</p>
            <h2>Comment enrichir cette page ensuite</h2>
            <ul class="feature-list">
                <li>Ajouter vos villes prioritaires réelles.</li>
                <li>Créer des variantes de pages ou sections locales par métier.</li>
                <li>Ajouter vos photos de chantier ou de véhicule dans chaque zone.</li>
                <li>Relier la page avec les services climatisation, chauffage, plomberie et électricité.</li>
            </ul>
        </div>
        <div class="card reveal">
            <p class="eyebrow">Conseil commercial</p>
            <h2>Ce que le visiteur doit comprendre</h2>
            <p>La page ne doit pas seulement lister des villes. Elle doit montrer qu’EMAE sait organiser une intervention, traiter un besoin urgent et répondre vite. C’est cela qui aide à convertir un clic en demande réelle.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
