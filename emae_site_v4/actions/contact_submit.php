<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../contact.php');
}
verify_csrf();

if (!hp_is_clean()) {
    flash('error', 'Envoi refusé.');
    redirect('../contact.php');
}

if (!check_rate_limit('contact_form', 6, 900)) {
    flash('error', 'Trop d’envois en peu de temps. Réessayez plus tard.');
    redirect('../contact.php');
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($fullName === '' || $email === '' || $subject === '' || $message === '') {
    flash('error', 'Merci de remplir tous les champs obligatoires.');
    redirect('../contact.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Adresse email invalide.');
    redirect('../contact.php');
}

db_insert('contacts', [
    'user_id' => currentUser()['id'] ?? null,
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'subject' => $subject,
    'message' => $message,
    'created_at' => date('c'),
]);

flash('success', 'Votre message a bien été enregistré.');
redirect('../contact.php');
