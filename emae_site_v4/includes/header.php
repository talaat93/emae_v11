<?php
$meta = $meta ?? seo_defaults();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>">
    <meta name="keywords" content="<?= e($meta['keywords']) ?>">
    <meta name="theme-color" content="#0b1641">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($meta['canonical'])): ?><link rel="canonical" href="<?= e($meta['canonical']) ?>"><?php endif; ?>
    <link rel="preload" href="<?= e(asset('img/logo.png')) ?>" as="image">
    <link rel="preload" href="<?= e(asset('img/hero-van.jpg')) ?>" as="image">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <script defer src="<?= e(asset('js/app.js')) ?>"></script>
</head>
<body>
<div class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__left">
            <a href="<?= COMPANY_PHONE_LINK ?>">📞 <?= e(COMPANY_PHONE) ?></a>
            <a href="mailto:<?= e(COMPANY_EMAIL) ?>">✉️ <?= e(COMPANY_EMAIL) ?></a>
        </div>
        <div class="topbar__right">
            <span><?= e(COMPANY_CITY) ?></span>
            <?php if ($user): ?>
                <a href="dashboard.php">Mon espace</a>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="index.php" aria-label="Retour à l'accueil">
            <img src="<?= e(asset('img/logo.png')) ?>" alt="Logo EMAE">
        </a>

        <button class="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false">☰</button>

        <nav class="site-nav" aria-label="Navigation principale">
            <?php foreach (nav_items() as $label => $href): ?>
                <a class="<?= is_active_nav($href) ? 'is-active' : '' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="btn btn--small btn--primary" href="quote.php">Demander un devis</a>
        </nav>
    </div>
</header>

<?php if ($flash = flash('success')): ?>
    <div class="flash flash--success container"><?= e($flash) ?></div>
<?php endif; ?>
<?php if ($flash = flash('error')): ?>
    <div class="flash flash--error container"><?= e($flash) ?></div>
<?php endif; ?>
