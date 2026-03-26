<?php
/**
 * Page service dédiée EMAE
 *
 * Utilisation rapide :
 * 1. Copiez ce fichier dans votre projet.
 * 2. Dupliquez-le pour chaque métier : electricite, plomberie, chauffage, climatisation.
 * 3. Modifiez simplement le tableau $service en haut du fichier.
 * 4. Liez la feuille de style assets/css/page-service-dedie.css.
 */

declare(strict_types=1);

$service = [
    'slug' => 'electricite',
    'label' => 'Électricité',
    'eyebrow' => 'Intervention rapide 24h/24 • 7j/7',
    'title' => 'Votre page service électricité haut de gamme, pensée pour convertir.',
    'lead' => "EMAE intervient pour le dépannage, la mise en sécurité, la rénovation, l'installation et la maintenance électrique en Île-de-France et en Occitanie. Cette page est faite pour accueillir vos vraies photos, vos textes métier et vos arguments de confiance.",
    'badge_1' => 'Intervention rapide',
    'badge_2' => 'Photos de chantiers',
    'badge_3' => 'Devis clair',
    'cta_primary' => 'Demander un devis',
    'cta_primary_url' => '#formulaire-contact',
    'cta_secondary' => 'Nous appeler',
    'cta_secondary_url' => 'tel:+33667830376',
    'phone' => '+33 6 67 83 03 76',
    'response' => 'Réponse rapide • Devis gratuit • Intervention sur rendez-vous ou en urgence',

    // Photos : remplacez les chemins ci-dessous par vos vraies images.
    'hero_photo_main' => 'storage/uploads/services/electricite-main.jpg',
    'hero_photo_side_1' => 'storage/uploads/services/electricite-side-1.jpg',
    'hero_photo_side_2' => 'storage/uploads/services/electricite-side-2.jpg',

    'intro_title' => 'Une page conçue pour présenter votre service comme une vraie offre premium',
    'intro_text' => "Ajoutez ici votre texte commercial principal : zone couverte, types d'interventions, niveau d'urgence, expérience, spécialités, certifications et promesse client.",

    'reasons_title' => 'Pourquoi choisir EMAE pour ce service ?',
    'reasons' => [
        'Techniciens qualifiés et interventions sécurisées',
        'Devis clair avant intervention',
        'Disponibilité sur interventions urgentes',
        'Communication simple, professionnelle et rassurante',
    ],

    'gallery_title' => 'Vos photos de réalisations et d’interventions',
    'gallery_text' => 'Cette zone est faite pour mettre vos vraies photos : dépannage, installation, rénovation, maintenance, avant/après, tableaux, équipements, etc.',
    'gallery_slots' => [
        'storage/uploads/services/electricite-gallery-1.jpg',
        'storage/uploads/services/electricite-gallery-2.jpg',
        'storage/uploads/services/electricite-gallery-3.jpg',
        'storage/uploads/services/electricite-gallery-4.jpg',
    ],

    'interventions_title' => 'Nos interventions en électricité',
    'interventions_lead' => 'Voici les situations dans lesquelles vous pouvez utiliser ce bloc pour rassurer le client et détailler votre savoir-faire.',
    'interventions' => [
        [
            'icon' => '⚡',
            'title' => 'Panne électrique',
            'text' => "Intervention rapide pour rétablir l'alimentation et sécuriser l'installation.",
        ],
        [
            'icon' => '🧰',
            'title' => 'Tableau électrique',
            'text' => 'Création, remplacement, mise en sécurité et remise en conformité.',
        ],
        [
            'icon' => '🔌',
            'title' => 'Prises et alimentation',
            'text' => 'Ajout, réparation, remplacement et contrôle des points de puissance.',
        ],
        [
            'icon' => '💡',
            'title' => 'Éclairage',
            'text' => 'Éclairage intérieur, extérieur, technique et amélioration du confort visuel.',
        ],
        [
            'icon' => '🏗️',
            'title' => 'Installation électrique',
            'text' => 'Installation complète ou partielle pour rénovation, extension ou local pro.',
        ],
        [
            'icon' => '✅',
            'title' => 'Mise en sécurité',
            'text' => 'Correction des défauts dangereux et remise en état de fonctionnement.',
        ],
        [
            'icon' => '🔄',
            'title' => 'Rénovation',
            'text' => 'Modernisation de l’existant, adaptation des circuits et optimisation.',
        ],
        [
            'icon' => '🚨',
            'title' => 'Urgence',
            'text' => 'Intervention prioritaire en cas de panne bloquante ou de danger électrique.',
        ],
    ],

    'offer_title' => 'Un bloc offre / arguments / prix de départ',
    'offer_lead' => 'Tu peux utiliser cette zone pour afficher tes avantages, un prix à partir de, et un gros CTA qui convertit.',
    'offer_highlights' => [
        'Technicien certifié et intervention sécurisée',
        'Prix annoncés avant intervention',
        'Déplacement rapide selon zone',
        'Paiement sécurisé et facture',
    ],
    'offer_price_label' => 'Intervention standard à partir de',
    'offer_price' => '39€',
    'offer_price_note' => 'Déplacement et devis annoncés avant intervention selon le besoin.',
    'offer_cta' => 'Nous appeler maintenant',
    'offer_cta_url' => 'tel:+33667830376',

    'process_title' => 'Process en 3 étapes',
    'process_lead' => 'Intervention rapide, communication transparente, résultat propre et professionnel.',
    'process' => [
        ['step' => '1', 'title' => 'Prise de contact', 'text' => 'Appel, formulaire ou demande de devis depuis le site.'],
        ['step' => '2', 'title' => 'Analyse et déplacement', 'text' => 'Qualification du besoin, proposition claire, puis intervention.'],
        ['step' => '3', 'title' => 'Travaux et validation', 'text' => 'Réalisation, contrôles, conseils d’usage et remise du compte rendu.'],
    ],

    'zones_title' => "Zone d'intervention",
    'zones_lead' => 'Ajoute ici ta carte, tes villes prioritaires, tes secteurs, ton délai moyen et les départements couverts.',
    'zones' => ['Paris', 'Seine-et-Marne', 'Yvelines', 'Essonne', 'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d’Oise'],

    'faq_title' => 'Questions fréquentes',
    'faq' => [
        ['q' => 'Quel est le prix d’une intervention ?', 'a' => 'Le tarif dépend du type de panne, du matériel et du temps estimé. Vous pouvez annoncer ici votre méthode de chiffrage.'],
        ['q' => 'Quel est le délai d’intervention ?', 'a' => 'Vous pouvez préciser ici votre délai moyen, vos horaires, vos créneaux d’urgence et votre zone couverte.'],
        ['q' => 'Intervenez-vous pour les urgences ?', 'a' => 'Oui, cette zone peut être utilisée pour détailler votre disponibilité et les cas prioritaires.'],
        ['q' => 'Faites-vous les mises en sécurité ou mises aux normes ?', 'a' => 'Oui, vous pouvez décrire ici vos types d’interventions de sécurisation et de rénovation.'],
    ],

    'contact_title' => 'Contactez votre technicien',
    'contact_lead' => 'Besoin d’un devis, d’un rappel ou d’une intervention ? Utilisez ce bloc pour capter les demandes chaudes en fin de page.',
    'contact_form_title' => 'Un technicien vous contacte dès réception.',
    'contact_info_title' => 'Informations de contact',
    'contact_reasons_title' => 'Pourquoi nous contacter ?',
    'contact_reasons' => [
        'Devis gratuit et sans engagement',
        'Intervention rapide selon la zone',
        'Technicien qualifié et intervention sécurisée',
        'Facture claire et conseils utiles',
    ],
];

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pageTitle(array $service): string
{
    return h($service['label'] . ' | EMAE');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= pageTitle($service) ?></title>
    <link rel="stylesheet" href="assets/css/page-service-dedie.css">
</head>
<body>
    <header class="service-topbar">
        <div class="container topbar-inner">
            <div class="brand">EMAE</div>
            <nav class="service-nav">
                <a href="#interventions">Interventions</a>
                <a href="#galerie">Photos</a>
                <a href="#process">Process</a>
                <a href="#contact">Contact</a>
            </nav>
            <a class="btn btn--ghost" href="<?= h($service['cta_secondary_url']) ?>"><?= h($service['cta_secondary']) ?></a>
        </div>
    </header>

    <main>
        <section class="service-hero">
            <div class="container service-hero__grid">
                <div class="service-hero__content">
                    <p class="eyebrow"><?= h($service['eyebrow']) ?></p>
                    <h1><?= h($service['title']) ?></h1>
                    <p class="lead"><?= h($service['lead']) ?></p>

                    <div class="hero-badges">
                        <span><?= h($service['badge_1']) ?></span>
                        <span><?= h($service['badge_2']) ?></span>
                        <span><?= h($service['badge_3']) ?></span>
                    </div>

                    <div class="hero-actions">
                        <a class="btn btn--primary" href="<?= h($service['cta_primary_url']) ?>"><?= h($service['cta_primary']) ?></a>
                        <a class="btn btn--secondary" href="<?= h($service['cta_secondary_url']) ?>"><?= h($service['cta_secondary']) ?></a>
                    </div>

                    <div class="hero-trust">
                        <div>
                            <strong><?= h($service['phone']) ?></strong>
                            <span>Numéro direct</span>
                        </div>
                        <div>
                            <strong>Vos photos / vos textes</strong>
                            <span>Sections entièrement remplaçables</span>
                        </div>
                        <div>
                            <strong><?= h($service['response']) ?></strong>
                            <span>Bloc de réassurance</span>
                        </div>
                    </div>
                </div>

                <div class="service-hero__media">
                    <div class="hero-main-photo photo-slot">
                        <img src="<?= h($service['hero_photo_main']) ?>" alt="Photo principale <?= h($service['label']) ?>">
                        <div class="photo-overlay">Photo principale du service</div>
                    </div>
                    <div class="hero-side-photos">
                        <div class="photo-slot compact">
                            <img src="<?= h($service['hero_photo_side_1']) ?>" alt="Photo secondaire 1 <?= h($service['label']) ?>">
                            <div class="photo-overlay">Photo chantier / intervention</div>
                        </div>
                        <div class="photo-slot compact">
                            <img src="<?= h($service['hero_photo_side_2']) ?>" alt="Photo secondaire 2 <?= h($service['label']) ?>">
                            <div class="photo-overlay">Photo avant / après</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--intro">
            <div class="container section-intro-card">
                <div>
                    <p class="eyebrow">Présentation</p>
                    <h2><?= h($service['intro_title']) ?></h2>
                    <p><?= h($service['intro_text']) ?></p>
                </div>
                <ul class="reason-list">
                    <?php foreach ($service['reasons'] as $reason): ?>
                        <li><?= h($reason) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="section" id="galerie">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Galerie</p>
                    <h2><?= h($service['gallery_title']) ?></h2>
                    <p><?= h($service['gallery_text']) ?></p>
                </div>
                <div class="gallery-grid">
                    <?php foreach ($service['gallery_slots'] as $slot): ?>
                        <div class="photo-slot gallery-slot">
                            <img src="<?= h($slot) ?>" alt="Photo galerie <?= h($service['label']) ?>">
                            <div class="photo-overlay">Emplacement photo</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="interventions">
            <div class="container">
                <div class="section-heading centered">
                    <p class="eyebrow">Interventions</p>
                    <h2><?= h($service['interventions_title']) ?></h2>
                    <p><?= h($service['interventions_lead']) ?></p>
                </div>
                <div class="intervention-grid">
                    <?php foreach ($service['interventions'] as $item): ?>
                        <article class="intervention-card">
                            <div class="intervention-icon"><?= h($item['icon']) ?></div>
                            <h3><?= h($item['title']) ?></h3>
                            <p><?= h($item['text']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="offer-box">
                    <div class="section-heading centered narrow">
                        <p class="eyebrow">Offre</p>
                        <h2><?= h($service['offer_title']) ?></h2>
                        <p><?= h($service['offer_lead']) ?></p>
                    </div>

                    <div class="offer-grid">
                        <?php foreach ($service['offer_highlights'] as $highlight): ?>
                            <div class="offer-highlight"><?= h($highlight) ?></div>
                        <?php endforeach; ?>
                    </div>

                    <div class="price-box">
                        <span class="price-label"><?= h($service['offer_price_label']) ?></span>
                        <strong class="price-value"><?= h($service['offer_price']) ?></strong>
                        <p><?= h($service['offer_price_note']) ?></p>
                    </div>

                    <div class="centered-cta">
                        <a class="btn btn--primary btn--large" href="<?= h($service['offer_cta_url']) ?>"><?= h($service['offer_cta']) ?></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="process">
            <div class="container">
                <div class="section-heading centered narrow">
                    <p class="eyebrow">Process</p>
                    <h2><?= h($service['process_title']) ?></h2>
                    <p><?= h($service['process_lead']) ?></p>
                </div>
                <div class="process-grid">
                    <?php foreach ($service['process'] as $step): ?>
                        <article class="process-card">
                            <div class="process-step"><?= h($step['step']) ?></div>
                            <h3><?= h($step['title']) ?></h3>
                            <p><?= h($step['text']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" id="zones">
            <div class="container">
                <div class="section-heading centered narrow">
                    <p class="eyebrow">Zones</p>
                    <h2><?= h($service['zones_title']) ?></h2>
                    <p><?= h($service['zones_lead']) ?></p>
                </div>

                <div class="zone-map-slot">
                    <div>Emplacement pour votre carte / visuel de zone d’intervention</div>
                </div>

                <div class="zone-tags">
                    <?php foreach ($service['zones'] as $zone): ?>
                        <span><?= h($zone) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="faq">
            <div class="container narrow-container">
                <div class="section-heading centered narrow">
                    <p class="eyebrow">FAQ</p>
                    <h2><?= h($service['faq_title']) ?></h2>
                </div>

                <div class="faq-list">
                    <?php foreach ($service['faq'] as $faq): ?>
                        <details class="faq-item">
                            <summary><?= h($faq['q']) ?></summary>
                            <p><?= h($faq['a']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" id="contact">
            <div class="container">
                <div class="section-heading centered narrow">
                    <p class="eyebrow">Contact</p>
                    <h2><?= h($service['contact_title']) ?></h2>
                    <p><?= h($service['contact_lead']) ?></p>
                </div>

                <div class="contact-grid" id="formulaire-contact">
                    <div class="contact-form-card">
                        <h3><?= h($service['contact_form_title']) ?></h3>
                        <form>
                            <label>Nom complet
                                <input type="text" placeholder="Votre nom">
                            </label>
                            <label>Téléphone
                                <input type="tel" placeholder="06 00 00 00 00">
                            </label>
                            <label>Email
                                <input type="email" placeholder="votre@email.fr">
                            </label>
                            <label>Adresse / Ville
                                <input type="text" placeholder="Votre adresse ou votre ville">
                            </label>
                            <label>Service souhaité
                                <select>
                                    <option><?= h($service['label']) ?></option>
                                    <option>Plomberie</option>
                                    <option>Chauffage</option>
                                    <option>Climatisation</option>
                                </select>
                            </label>
                            <label>Votre besoin
                                <textarea rows="5" placeholder="Décrivez votre demande, votre panne, votre installation ou vos travaux."></textarea>
                            </label>
                            <button type="submit" class="btn btn--primary btn--large">Envoyer ma demande</button>
                        </form>
                    </div>

                    <div class="contact-side">
                        <article class="contact-info-card">
                            <h3><?= h($service['contact_info_title']) ?></h3>
                            <ul>
                                <li><strong>Téléphone :</strong> <?= h($service['phone']) ?></li>
                                <li><strong>Zone :</strong> Île-de-France • Occitanie</li>
                                <li><strong>Disponibilité :</strong> 24h/24 • 7j/7</li>
                            </ul>
                        </article>

                        <article class="contact-info-card">
                            <h3><?= h($service['contact_reasons_title']) ?></h3>
                            <ul>
                                <?php foreach ($service['contact_reasons'] as $reason): ?>
                                    <li><?= h($reason) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
