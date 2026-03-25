<?php $servicesFooter = array_slice(service_pages(), 0, 6); ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <img src="<?= e(asset('img/logo.png')) ?>" alt="Logo EMAE" class="footer-logo">
            <p>EMAE présente une image claire, premium et rassurante pour capter des demandes de dépannage, maintenance et travaux ciblés en multitechnique.</p>
            <div class="footer-badges">
                <span>Urgence 24/7</span>
                <span>Devis rapide</span>
                <span>Mobile first</span>
            </div>
        </div>

        <div>
            <h3>Navigation</h3>
            <ul>
                <?php foreach (nav_items() as $label => $href): ?>
                    <li><a href="<?= e($href) ?>"><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3>Services clés</h3>
            <ul>
                <?php foreach ($servicesFooter as $service): ?>
                    <li><a href="service.php?slug=<?= e($service['slug']) ?>"><?= e($service['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3>Contact & légal</h3>
            <ul>
                <li><a href="<?= COMPANY_PHONE_LINK ?>"><?= e(COMPANY_PHONE) ?></a></li>
                <li><a href="mailto:<?= e(COMPANY_EMAIL) ?>"><?= e(COMPANY_EMAIL) ?></a></li>
                <li><?= e(COMPANY_CITY) ?></li>
                <li><a href="mentions-legales.php">Mentions légales</a></li>
                <li><a href="politique-confidentialite.php">Politique de confidentialité</a></li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> EMAE - Tous droits réservés.</p>
        <p class="small">Version 2 du site : base prête à publier après ajout de vos informations légales réelles.</p>
    </div>
</footer>

<div class="floating-actions">
    <a class="floating-actions__call" href="<?= COMPANY_PHONE_LINK ?>">Appeler</a>
    <a class="floating-actions__quote" href="quote.php">Devis</a>
    <button class="floating-actions__chat" type="button" data-chat-toggle>Chat</button>
</div>

<div class="chat-widget" hidden>
    <div class="chat-widget__header">
        <strong>Assistant EMAE</strong>
        <button type="button" data-chat-toggle aria-label="Fermer le chat">×</button>
    </div>
    <div class="chat-widget__body">
        <p>Bonjour. Sélectionnez un besoin rapide :</p>
        <div class="chat-pills">
            <button type="button" class="chat-pill" data-chat-answer="Nous mettons en avant l’Île-de-France et l’Occitanie. Utilisez la page Zones d’intervention pour voir les villes exemples.">Zones d’intervention</button>
            <button type="button" class="chat-pill" data-chat-answer="Le formulaire devis est disponible sans création de compte. Pour une urgence, le plus rapide reste l’appel téléphonique.">Demande de devis</button>
            <button type="button" class="chat-pill" data-chat-answer="Pour un besoin urgent, appelez directement le 01 84 25 67 92 puis détaillez le contexte dans le formulaire si besoin.">Urgence 24/7</button>
        </div>
        <div class="chat-response" data-chat-response></div>
        <a class="btn btn--block btn--primary" href="contact.php">Envoyer un message</a>
    </div>
</div>
</body>
</html>
