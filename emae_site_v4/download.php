<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$document = db_find_one('documents', fn(array $doc): bool => (int) $doc['id'] === $id);

if (!$document) {
    http_response_code(404);
    exit('Document introuvable.');
}

if ($document['audience'] === 'client' && !isLoggedIn()) {
    flash('error', 'Connectez-vous pour accéder à ce document.');
    redirect('login.php');
}

$filePath = DOCUMENTS_PATH . '/' . $document['filename'];
if (!is_file($filePath)) {
    http_response_code(404);
    exit('Fichier manquant.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
