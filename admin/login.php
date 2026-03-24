<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if (admin_logged_in()) redirect_to('admin/index.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (attempt_login($email, $password)) { flash('success', 'Connexion réussie.'); redirect_to('admin/index.php'); }
    flash('error', 'Email ou mot de passe incorrect.'); redirect_to('admin/login.php');
}
?><!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Connexion admin | <?= e(company_name()) ?></title><link rel="stylesheet" href="<?= e(asset_url('assets/css/admin.css')) ?>"></head><body class="admin-body"><div class="login-wrap"><div class="login-card"><?php if ($msg = flash('error')): ?><div class="flash flash--error"><?= e($msg) ?></div><?php endif; ?><h1>Connexion admin</h1><p>Connecte-toi pour gérer le site.</p><form method="post" class="admin-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="admin-field"><span>Email</span><input type="email" name="email" required></label><label class="admin-field"><span>Mot de passe</span><input type="password" name="password" required></label><button class="admin-btn admin-btn--primary" type="submit">Se connecter</button></form></div></div></body></html>