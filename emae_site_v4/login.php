<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}
$meta = page_meta([
    'title' => 'Connexion | EMAE',
    'description' => 'Connexion à l’espace client EMAE.',
    'keywords' => 'connexion EMAE, espace client',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Espace client</p>
        <h1>Connexion</h1>
        <p>Connectez-vous pour retrouver vos demandes et télécharger vos documents clients.</p>
    </div>
</section>

<section class="section">
    <div class="container auth-layout">
        <div class="card form-card reveal">
            <form action="actions/login_submit.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Email
                    <input type="email" name="email" required>
                </label>
                <label>Mot de passe
                    <input type="password" name="password" required>
                </label>
                <button class="btn btn--primary btn--block" type="submit">Se connecter</button>
            </form>
        </div>
        <div class="card reveal">
            <h2>Pas encore de compte ?</h2>
            <p>Créez un accès simple pour suivre vos demandes de devis et retrouver les documents mis à disposition.</p>
            <a class="btn btn--outline" href="register.php">Créer un compte</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
