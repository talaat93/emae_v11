<?php

declare(strict_types=1);

const SITE_NAME = 'EMAE';
const SITE_FULL_NAME = 'Entreprise Multitechnique Avancée';
const BASE_URL = '';
const COMPANY_PHONE = '01 84 25 67 92';
const COMPANY_PHONE_LINK = 'tel:+33184256792';
const COMPANY_EMAIL = 'contact@emae-pro.fr';
const COMPANY_CITY = 'Île-de-France et Occitanie';
const COMPANY_ADDRESS = 'Adresse à compléter avant publication';
const DATASTORE_PATH = __DIR__ . '/../storage/database.json';
const DOCUMENTS_PATH = __DIR__ . '/../downloads';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

date_default_timezone_set('Europe/Paris');
