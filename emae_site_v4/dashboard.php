<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!isLoggedIn()) {
    redirect('login.php');
}
$user = currentUser();
$quotes = array_values(array_reverse(db_filter('quotes', fn(array $quote): bool => (int) ($quote['user_id'] ?? 0) === (int) $user['id'])));
$docs = array_values(array_reverse(db_filter('documents', fn(array $document): bool => in_array($document['audience'], ['public', 'client'], true))));
$meta = page_meta([
    'title' => 'Tableau de bord | EMAE',
    'description' => 'Espace client EMAE pour suivre les demandes et documents.',
    'keywords' => 'tableau de bord EMAE, espace client',
]);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Tableau de bord</p>
        <h1>Bonjour <?= e($user['first_name']) ?></h1>
        <p>Retrouvez ici vos demandes de devis et les documents que vous pouvez télécharger.</p>
    </div>
</section>

<section class="section">
    <div class="container dashboard-grid">
        <aside class="card sidebar reveal">
            <h2>Mon compte</h2>
            <p><strong><?= e($user['first_name'] . ' ' . $user['last_name']) ?></strong></p>
            <p class="small"><?= e($user['email']) ?></p>
            <?php if (!empty($user['phone'])): ?><p class="small"><?= e($user['phone']) ?></p><?php endif; ?>
            <a class="btn btn--primary btn--block" href="quote.php">Nouvelle demande</a>
        </aside>

        <div class="grid">
            <section class="card reveal">
                <h2>Mes demandes</h2>
                <?php if (!$quotes): ?>
                    <p class="small">Aucune demande n’est enregistrée pour le moment.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table-like">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Ville</th>
                                    <th>Urgence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotes as $quote): ?>
                                    <tr>
                                        <td><?= e(date('d/m/Y', strtotime($quote['created_at']))) ?></td>
                                        <td><?= e($quote['service_type']) ?></td>
                                        <td><?= e($quote['city']) ?></td>
                                        <td><?= e($quote['urgency']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card reveal">
                <h2>Documents disponibles</h2>
                <div class="doc-list">
                    <?php foreach ($docs as $doc): ?>
                        <div class="doc-item">
                            <span><?= e($doc['title']) ?></span>
                            <a class="btn btn--small btn--light" href="download.php?id=<?= (int) $doc['id'] ?>">Télécharger</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
