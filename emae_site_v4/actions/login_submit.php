<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php');
}
verify_csrf();

$_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0);
if ($_SESSION['login_attempts'] >= 8) {
    flash('error', 'Trop de tentatives. Réessayez plus tard.');
    redirect('../login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$user = db_find_one('users', fn(array $item): bool => strtolower($item['email']) === strtolower($email));
if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['login_attempts']++;
    flash('error', 'Identifiants invalides.');
    redirect('../login.php');
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['login_attempts'] = 0;
flash('success', 'Connexion réussie.');
redirect('../dashboard.php');
