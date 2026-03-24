EMAE V10 corrigé
================

But de cette version
--------------------
- garder l’interface admin de Emae_v10
- corriger la synchronisation admin -> front
- remplacer le hero de la V10 par le hero de la V4
- mettre le bloc devis à la place du camion
- supprimer automatiquement les chips et cartes infos laissées vides dans l’admin
- rendre le site plus fiable en local sous XAMPP

Prérequis
---------
- PHP 8+
- MySQL / MariaDB (XAMPP convient)
- Apache recommandé

Installation rapide sous XAMPP
------------------------------
1. Dézipper le dossier dans C:\xampp\htdocs\
2. Démarrer Apache et MySQL dans XAMPP
3. Ouvrir : http://localhost/emae_v10_corrige/install.php
4. Laisser Base URL vide si le site est dans htdocs
5. Compléter l’installation
6. Se connecter à : http://localhost/emae_v10_corrige/admin/login.php

Comptes et réglages
-------------------
- les identifiants admin sont choisis pendant l’installation
- le hero se règle dans Admin > Bloc hero & devis
- les cartes services se règlent dans Admin > Cartes services accueil
- les coordonnées et le logo se règlent dans Admin > Identité & coordonnées

Corrections majeures incluses
-----------------------------
1. ajout d’un alias site_setting() pour supprimer l’erreur fatale des anciens écrans admin
2. routeur stabilisé via index.php?route=... pour éviter les problèmes de jolis liens en local
3. hero V4 réintégré avec bloc devis à droite
4. si un chip ou une carte info du hero est vide, il disparaît du front
5. CTA, topbar, footer, logo et services reliés aux paramètres admin

Fichiers clés
-------------
- index.php : routeur + accueil + formulaire
- includes/helpers.php : settings, URL, hero, images
- includes/render.php : head, header, footer
- admin/home_hero.php : édition du hero
- admin/home_services.php : édition des cartes services
- admin/site_identity.php : coordonnées et logo
- assets/css/style.css : front corrigé
- assets/css/admin.css : admin
