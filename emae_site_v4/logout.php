<?php
require_once __DIR__ . '/includes/bootstrap.php';
unset($_SESSION['user_id']);
flash('success', 'Vous êtes déconnecté.');
redirect('index.php');
