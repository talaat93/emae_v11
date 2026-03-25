<?php
require_once __DIR__ . '/includes/bootstrap.php';
$services = service_pages();
$meta = page_meta([
    'title' => 'Devis en ligne | EMAE',
    'description' => 'Demande de devis EMAE pour dépannage, maintenance, chauffage, climatisation, électricité ou plomberie.',
    'keywords' => 'devis EMAE, devis dépannage, devis plomberie, devis électricité, devis climatisation',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Devis en ligne</p>
        <h1>Un formulaire plus complet pour les demandes sérieuses</h1>
        <p>La version 2 améliore la capture des leads avec des champs plus utiles, un service sélectionnable et un meilleur message de consentement.</p>
    </div>
</section>

<section class="section">
    <div class="container split-panel split-panel--form">
        <div class="card reveal">
            <p class="eyebrow">Avant d’envoyer</p>
            <h2>Ce qu’il faut préciser</h2>
            <ul class="feature-list">
                <li>La nature du besoin : panne, entretien, travaux, modernisation.</li>
                <li>La ville ou la zone d’intervention.</li>
                <li>Le niveau d’urgence si vous avez besoin d’un rappel rapide.</li>
                <li>Le plus de contexte possible pour faciliter la qualification.</li>
            </ul>
        </div>

        <div class="card form-card reveal">
            <p class="eyebrow">Formulaire de devis</p>
            <h2>Décrire le besoin</h2>
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
                <div class="form-grid">
                    <label>Service
                        <select name="service_type" required>
                            <option value="">Choisir</option>
                            <?php foreach ($services as $service): ?>
                                <option><?= e($service['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Niveau d’urgence
                        <select name="urgency">
                            <option>Standard</option>
                            <option>Urgent</option>
                            <option>Très urgent</option>
                        </select>
                    </label>
                </div>
                <label>Description du besoin
                    <textarea name="message" required placeholder="Décrivez la panne, la zone, l’équipement concerné et vos attentes."></textarea>
                </label>
                <label class="small checkbox-line">
                    <input type="checkbox" name="consent" value="1" required>
                    J’accepte la politique de confidentialité et souhaite être recontacté.
                </label>
                <button class="btn btn--primary btn--block" type="submit">Envoyer ma demande</button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
