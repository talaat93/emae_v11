<?php
require_once __DIR__ . '/includes/bootstrap.php';
$items = faq_items();
$meta = page_meta([
    'title' => 'FAQ | EMAE',
    'description' => 'Questions fréquentes sur le fonctionnement du site EMAE, les devis, l’espace client et les zones d’intervention.',
    'keywords' => 'FAQ EMAE, devis, espace client, zones intervention',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">FAQ</p>
        <h1>Questions fréquentes</h1>
        <p>Une FAQ claire évite les blocages, rassure le visiteur et améliore l’expérience sur mobile.</p>
    </div>
</section>

<section class="section">
    <div class="container faq-list">
        <?php foreach ($items as $index => $item): ?>
            <article class="faq-item card reveal" data-faq>
                <button class="faq-question" type="button" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                    <span><?= e($item['question']) ?></span>
                    <span>+</span>
                </button>
                <div class="faq-answer" <?= $index === 0 ? '' : 'hidden' ?>>
                    <p><?= e($item['answer']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
