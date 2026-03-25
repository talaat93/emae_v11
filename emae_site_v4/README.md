# EMAE - Version 2 corrigée

Cette version 2 améliore la première livraison avec :

- un accueil plus proche de la maquette corporate / premium
- une vraie page **Zones d’intervention**
- davantage de pages métier SEO
- de **vrais PDF** dans `downloads/`
- une structure plus crédible pour la conversion B2C
- une configuration nettoyée (stockage JSON unique, plus de faux fichier SQLite)

## Stack retenue
- PHP 8+
- HTML / CSS / JavaScript natifs
- stockage JSON local : `storage/database.json`
- aucun service externe obligatoire

## Lancer en local
### Méthode 1 - très simple avec PHP installé
```bash
php -S localhost:8000
```

Puis ouvrir :
```text
http://localhost:8000/index.php
```

### Méthode 2 - avec XAMPP
Copier le dossier `emae_site` dans :
```text
C:\xampp\htdocs\
```

Puis ouvrir :
```text
http://localhost/emae_site/
```

## Données stockées
- utilisateurs, devis, contacts : `storage/database.json`
- documents PDF : `downloads/`

## Ce qu’il reste à personnaliser avant vraie publication
- mentions légales réelles
- adresse officielle, SIREN / SIRET, forme juridique
- villes réellement ciblées
- photos de vos vrais chantiers
- textes commerciaux finaux
- éventuel envoi d’emails SMTP plus tard

## Compte client
Le compte client est léger :
- inscription
- connexion
- suivi des demandes
- téléchargement des documents clients

## Sécurité déjà intégrée
- hashage des mots de passe
- jeton CSRF
- limitation simple des tentatives de connexion
- honeypot anti-spam sur formulaires
- mini limitation de fréquence sur contact / devis

## Conseils pour la suite
- remplacer progressivement les contenus exemples par vos vrais contenus
- créer des pages locales supplémentaires si vous ciblez des villes précises
- brancher un vrai SMTP lors de la mise en ligne
- basculer vers MySQL plus tard si vous voulez une vraie gestion plus avancée
