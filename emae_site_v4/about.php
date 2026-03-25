<?php
require_once __DIR__ . '/includes/bootstrap.php';
$meta = page_meta([
    'title' => 'À propos | EMAE',
    'description' => 'Présentation d’EMAE, de son positionnement premium et de son approche multitechnique.',
    'keywords' => 'à propos EMAE, entreprise multitechnique, positionnement premium, dépannage bâtiment',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">À propos</p>
        <h1>Une image plus crédible, plus structurée et plus rassurante</h1>
        <p>La version 2 du site a été retravaillée pour rapprocher le rendu de votre maquette : plus de contenu métier, plus de preuves de sérieux et un meilleur équilibre entre branding et conversion.</p>
    </div>
</section>

<section class="section">
    <div class="container split-panel">
        <div class="card reveal">
            <p class="eyebrow">Positionnement</p>
            <h2>Ce que raconte EMAE</h2>
            <p>EMAE se présente comme une entreprise multitechnique orientée réactivité, polyvalence et clarté. Le ton est corporate / premium, sans complexité inutile pour le visiteur.</p>
            <ul class="feature-list">
                <li>Une lecture simple sur mobile.</li>
                <li>Des boutons importants mis en valeur en orange.</li>
                <li>Un bleu profond pour l’univers de marque.</li>
                <li>Des pages dédiées aux métiers et aux zones.</li>
            </ul>
        </div>
        <div class="card reveal">
            <p class="eyebrow">Cibles</p>
            <h2>Les profils que le site peut toucher</h2>
            <div class="tag-grid">
                <?php foreach (sectors_served() as $sector): ?>
                    <span class="tag"><?= e($sector) ?></span>
                <?php endforeach; ?>
            </div>
            <p class="small" style="margin-top:1rem;">Vous pourrez ensuite enrichir cette page avec votre parcours, vos domaines historiques, vos références et vos engagements opérationnels réels.</p>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="container grid grid--3">
        <article class="card reveal">
            <h3>1. Attirer</h3>
            <p>Un accueil fort, des services visibles, une promesse simple et un univers visuel cohérent.</p>
        </article>
        <article class="card reveal">
            <h3>2. Rassurer</h3>
            <p>Des pages métiers lisibles, des zones d’intervention, un contact direct et des documents PDF.</p>
        </article>
        <article class="card reveal">
            <h3>3. Convertir</h3>
            <p>Appels, devis, formulaires courts, espace client et messages adaptés aux besoins urgents.</p>
        </article>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
