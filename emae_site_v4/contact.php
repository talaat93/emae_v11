<?php
require_once __DIR__ . '/includes/bootstrap.php';
$meta = page_meta([
    'title' => 'Contact | EMAE',
    'description' => 'Formulaire de contact EMAE pour demande d’information, dépannage, maintenance ou devis.',
    'keywords' => 'contact EMAE, dépannage, demande information, devis technique',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Contact</p>
        <h1>Parlons de votre besoin</h1>
        <p>Que ce soit pour un dépannage, une demande de maintenance, un besoin commercial ou un renseignement simple, cette page est conçue pour aller vite et rester lisible sur mobile.</p>
    </div>
</section>

<section class="section">
    <div class="container split-panel split-panel--form">
        <div class="card reveal">
            <p class="eyebrow">Informations</p>
            <h2>Coordonnées</h2>
            <ul class="feature-list">
                <li>Téléphone : <?= e(COMPANY_PHONE) ?></li>
                <li>Email : <?= e(COMPANY_EMAIL) ?></li>
                <li>Zones mises en avant : <?= e(COMPANY_CITY) ?></li>
                <li>Réponse plus rapide pour les urgences par téléphone.</li>
            </ul>
            <div class="doc-list">
                <div class="doc-item"><span>Plaquette de présentation</span><a class="btn btn--small btn--light" href="download.php?id=1">PDF</a></div>
            </div>
        </div>

        <div class="card form-card reveal">
            <p class="eyebrow">Formulaire</p>
            <h2>Envoyer un message</h2>
            <form action="actions/contact_submit.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <div class="form-grid">
                    <label>Nom complet
                        <input type="text" name="full_name" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" required>
                    </label>
                </div>
                <div class="form-grid">
                    <label>Téléphone
                        <input type="tel" name="phone">
                    </label>
                    <label>Objet
                        <input type="text" name="subject" required>
                    </label>
                </div>
                <label>Message
                    <textarea name="message" required placeholder="Décrivez votre besoin ou votre question."></textarea>
                </label>
                <button class="btn btn--primary btn--block" type="submit">Envoyer le message</button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
