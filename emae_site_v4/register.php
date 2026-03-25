<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}
$meta = page_meta([
    'title' => 'Créer un compte | EMAE',
    'description' => 'Création de compte client EMAE.',
    'keywords' => 'compte client EMAE, inscription espace client',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Espace client</p>
        <h1>Créer un compte</h1>
        <p>Le compte client reste volontairement simple : il sert à suivre les demandes et à récupérer les documents réservés aux clients.</p>
    </div>
</section>

<section class="section">
    <div class="container auth-layout auth-layout--wide">
        <div class="card form-card reveal">
            <form action="actions/register_submit.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-grid">
                    <label>Prénom
                        <input type="text" name="first_name" required>
                    </label>
                    <label>Nom
                        <input type="text" name="last_name" required>
                    </label>
                </div>
                <div class="form-grid">
                    <label>Email
                        <input type="email" name="email" required>
                    </label>
                    <label>Téléphone
                        <input type="tel" name="phone">
                    </label>
                </div>
                <label>Mot de passe
                    <input type="password" name="password" minlength="8" required>
                </label>
                <button class="btn btn--primary btn--block" type="submit">Créer mon compte</button>
            </form>
        </div>
        <div class="card reveal">
            <h2>Ce que vous trouverez ensuite</h2>
            <ul class="feature-list">
                <li>Vos demandes de devis enregistrées.</li>
                <li>Vos documents clients disponibles au téléchargement.</li>
                <li>Un espace très léger, sans complexité inutile.</li>
            </ul>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
