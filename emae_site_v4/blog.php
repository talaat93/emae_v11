<?php
require_once __DIR__ . '/includes/bootstrap.php';
$posts = blog_posts();
$meta = page_meta([
    'title' => 'Blog | EMAE',
    'description' => 'Articles SEO simples pour renforcer la visibilité locale d’EMAE.',
    'keywords' => 'blog EMAE, conseil dépannage climatisation, entretien PAC, tableau électrique',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Blog</p>
        <h1>Des contenus utiles pour le SEO local</h1>
        <p>Les articles servent à attirer des prospects qui cherchent une réponse simple avant de demander un devis ou un dépannage. Cette version 2 contient déjà une base éditoriale plus crédible.</p>
    </div>
</section>

<section class="section">
    <div class="container grid grid--3">
        <?php foreach ($posts as $post): ?>
            <article class="card blog-card reveal">
                <span class="tag"><?= e($post['category']) ?></span>
                <h2><a href="blog-post.php?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>
                <p><?= e($post['excerpt']) ?></p>
                <div class="blog-card__meta">
                    <span><?= e(format_date_fr($post['date'])) ?></span>
                    <a href="blog-post.php?slug=<?= e($post['slug']) ?>">Lire l’article</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
