<?php
require_once __DIR__ . '/includes/bootstrap.php';

$config = require __DIR__ . '/config/config.php';

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>TEST EMAE</title></head><body style="font-family:Arial;padding:30px">';
echo '<h1 style="color:red">TEST EMAE OK</h1>';

echo '<p><strong>Dossier actuel :</strong> ' . __DIR__ . '</p>';
echo '<p><strong>Fichier actuel :</strong> ' . __FILE__ . '</p>';

echo '<h2>Config DB</h2>';
echo '<pre>';
print_r($config['db'] ?? []);
echo '</pre>';

echo '<h2>Valeurs admin enregistrées</h2>';
echo '<p><strong>home_title :</strong> ' . htmlspecialchars(site_setting('home_title', 'VIDE')) . '</p>';
echo '<p><strong>home_lead :</strong> ' . htmlspecialchars(site_setting('home_lead', 'VIDE')) . '</p>';
echo '<p><strong>home_quote_title :</strong> ' . htmlspecialchars(site_setting('home_quote_title', 'VIDE')) . '</p>';

echo '</body></html>';