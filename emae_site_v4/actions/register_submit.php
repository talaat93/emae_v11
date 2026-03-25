<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}
verify_csrf();

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    flash('error', 'Merci de renseigner des informations valides.');
    redirect('../register.php');
}

if (db_find_one('users', fn(array $item): bool => strtolower($item['email']) === strtolower($email))) {
    flash('error', 'Cette adresse email est déjà utilisée.');
    redirect('../register.php');
}

$user = db_insert('users', [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'created_at' => date('c'),
]);

$_SESSION['user_id'] = (int) $user['id'];
flash('success', 'Votre compte client a bien été créé.');
redirect('../dashboard.php');
