<?php
require_once __DIR__ . '/includes/bootstrap.php';
$slug = $_GET['slug'] ?? '';
$post = find_post($slug) ?? blog_posts()[0];
$meta = page_meta([
    'title' => $post['title'] . ' | Blog EMAE',
    'description' => $post['excerpt'],
    'keywords' => strtolower($post['category']) . ', blog EMAE, dépannage, conseil métier',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Article</p>
        <h1><?= e($post['title']) ?></h1>
        <p><?= e($post['excerpt']) ?></p>
        <p class="small"><?= e(format_date_fr($post['date'])) ?> - <?= e($post['category']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container article-layout">
        <article class="card article-card reveal">
            <?php foreach ($post['content'] as $paragraph): ?>
                <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>

            <div class="info-stack info-stack--spaced">
                <div class="info-stack__item"><strong>Objectif SEO</strong><span>Répondre à une recherche concrète</span></div>
                <div class="info-stack__item"><strong>Objectif commercial</strong><span>Orienter vers appel, devis ou contact</span></div>
                <div class="info-stack__item"><strong>Évolution conseillée</strong><span>Ajouter vos cas réels, vos villes et vos photos</span></div>
            </div>
        </article>

        <aside class="card reveal">
            <p class="eyebrow">À lire aussi</p>
            <div class="stack-links">
                <?php foreach (blog_posts() as $other): ?>
                    <?php if ($other['slug'] !== $post['slug']): ?>
                        <a href="blog-post.php?slug=<?= e($other['slug']) ?>"><?= e($other['title']) ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <hr>
            <a class="btn btn--primary btn--block" href="quote.php">Demander un devis</a>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
