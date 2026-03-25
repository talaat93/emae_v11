<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../quote.php');
}
verify_csrf();

if (!hp_is_clean()) {
    flash('error', 'Envoi refusé.');
    redirect('../quote.php');
}

if (!check_rate_limit('quote_form', 6, 900)) {
    flash('error', 'Trop de demandes en peu de temps. Réessayez plus tard.');
    redirect('../quote.php');
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$serviceType = trim($_POST['service_type'] ?? '');
$city = trim($_POST['city'] ?? '');
$urgency = trim($_POST['urgency'] ?? 'Standard');
$message = trim($_POST['message'] ?? '');
$consent = isset($_POST['consent']) ? 1 : 0;

if ($fullName === '' || $phone === '' || $serviceType === '' || $message === '' || $consent !== 1) {
    flash('error', 'Merci de renseigner les champs obligatoires du devis.');
    redirect('../quote.php');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Adresse email invalide.');
    redirect('../quote.php');
}

db_insert('quotes', [
    'user_id' => currentUser()['id'] ?? null,
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'service_type' => $serviceType,
    'city' => $city,
    'urgency' => $urgency,
    'message' => $message,
    'consent' => $consent,
    'created_at' => date('c'),
]);

flash('success', 'Votre demande de devis a bien été enregistrée.');
redirect('../quote.php');
