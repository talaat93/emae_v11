<?php
require_once __DIR__ . '/includes/bootstrap.php';
$meta = page_meta([
    'title' => 'Politique de confidentialité | EMAE',
    'description' => 'Politique de confidentialité EMAE pour les formulaires de contact, devis et espace client.',
    'keywords' => 'RGPD EMAE, confidentialité EMAE',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Politique de confidentialité</p>
        <h1>Traitement simple et proportionné des données</h1>
        <p>La version 2 du site ne contient ni analytics, ni pixel publicitaire, ni service externe de chat. Les données traitées sont limitées aux formulaires et à l’espace client.</p>
    </div>
</section>

<section class="section">
    <div class="container legal-layout">
        <article class="card reveal">
            <h2>Données collectées</h2>
            <p>Le site peut stocker les informations envoyées via les formulaires de contact, devis et création de compte : nom, email, téléphone, ville, objet et description de la demande.</p>
        </article>
        <article class="card reveal">
            <h2>Finalité</h2>
            <p>Ces données servent uniquement à traiter la demande, répondre au prospect ou mettre à disposition certains documents dans l’espace client.</p>
        </article>
        <article class="card reveal">
            <h2>Base actuelle</h2>
            <p>Les données sont enregistrées localement dans un fichier JSON pour la version de test. Avant publication, vous pourrez conserver cette logique ou migrer vers une base MySQL.</p>
        </article>
        <article class="card reveal">
            <h2>Droits</h2>
            <p>Conformément au RGPD, toute personne peut demander l’accès, la rectification ou la suppression de ses données via <?= e(COMPANY_EMAIL) ?>.</p>
        </article>
        <article class="card reveal">
            <h2>Durée de conservation</h2>
            <p>À définir et à préciser selon votre organisation réelle. Il est recommandé de fixer une durée de conservation raisonnable pour les formulaires et comptes inactifs.</p>
        </article>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
