<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return db_find_one('users', fn(array $u): bool => (int) $u['id'] === (int) $_SESSION['user_id']);
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Jeton de sécurité invalide.');
    }
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function seo_defaults(): array
{
    return [
        'title' => SITE_NAME . ' | Dépannage multitechnique en Île-de-France et Occitanie',
        'description' => 'EMAE intervient pour le dépannage, la maintenance et les travaux en électricité, plomberie, chauffage, climatisation, CVC et pompes à chaleur.',
        'keywords' => 'dépannage climatisation, chauffage, électricité, plomberie, PAC, CVC, maintenance bâtiment, urgence technique',
        'canonical' => build_page_url(basename($_SERVER['PHP_SELF'] ?? 'index.php')),
    ];
}

function page_meta(array $overrides = []): array
{
    return array_merge(seo_defaults(), $overrides);
}

function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

function build_page_url(string $path): string
{
    $clean = ltrim($path, '/');
    return BASE_URL !== '' ? rtrim(BASE_URL, '/') . '/' . $clean : $clean;
}

function current_page(): string
{
    return basename($_SERVER['PHP_SELF'] ?? 'index.php');
}

function nav_items(): array
{
    return [
        'Accueil' => 'index.php',
        'Services' => 'services.php',
        'Zones d’intervention' => 'zones-intervention.php',
        'Réalisations' => 'realisations.php',
        'Blog' => 'blog.php',
        'À propos' => 'about.php',
        'FAQ' => 'faq.php',
        'Contact' => 'contact.php',
    ];
}

function is_active_nav(string $href): bool
{
    return current_page() === basename($href);
}

function service_pages(): array
{
    return [
        [
            'slug' => 'electricite',
            'title' => 'Électricité',
            'short' => 'Mise en sécurité, dépannage, tableaux, rénovation et alimentation des équipements techniques.',
            'hero' => 'Interventions électriques pour bâtiments résidentiels, commerces, parties communes et petits sites tertiaires.',
            'bullets' => ['Recherche de panne et remise en service', 'Mise en sécurité et remise en conformité', 'Tableaux électriques, TGBT et protection', 'Alimentation des équipements CVC et techniques'],
            'image' => asset('img/service-electricite.jpg'),
            'badge' => '⚡',
            'category' => 'courant-fort',
            'seo' => 'électricien dépannage, tableau électrique, mise en conformité',
        ],
        [
            'slug' => 'plomberie',
            'title' => 'Plomberie',
            'short' => 'Recherche de fuite, réparation sanitaire, remplacement d’équipements et maintenance courante.',
            'hero' => 'Plomberie technique pour logements, copropriétés, locaux tertiaires et petits ensembles immobiliers.',
            'bullets' => ['Recherche de fuite', 'Réseaux sanitaires et robinetterie', 'Maintenance des installations d’eau', 'Intervention sur colonnes et réseaux secondaires'],
            'image' => asset('img/service-plomberie.jpg'),
            'badge' => '🔧',
            'category' => 'sanitaire',
            'seo' => 'plombier dépannage, fuite eau, plomberie copropriété',
        ],
        [
            'slug' => 'chauffage',
            'title' => 'Chauffage',
            'short' => 'Diagnostic, dépannage et optimisation des équipements de chauffage pour confort et continuité de service.',
            'hero' => 'Prise en charge des pannes et besoins de maintenance sur les installations de chauffage.',
            'bullets' => ['Diagnostic de panne chauffage', 'Remise en service et contrôle de fonctionnement', 'Optimisation des réglages', 'Maintenance corrective et préventive'],
            'image' => asset('img/service-cvc.jpg'),
            'badge' => '🔥',
            'category' => 'thermique',
            'seo' => 'chauffagiste dépannage, panne chauffage, entretien chauffage',
        ],
        [
            'slug' => 'climatisation',
            'title' => 'Climatisation',
            'short' => 'Dépannage, entretien et remise en service des installations de climatisation et rafraîchissement.',
            'hero' => 'Solutions de climatisation pour particuliers, commerces et petits sites professionnels.',
            'bullets' => ['Diagnostic de dysfonctionnement', 'Entretien courant et nettoyage', 'Contrôle des performances', 'Préparation des remplacements et modernisations'],
            'image' => asset('img/service-cvc.jpg'),
            'badge' => '❄️',
            'category' => 'clim',
            'seo' => 'dépannage climatisation, entretien clim, panne clim',
        ],
        [
            'slug' => 'cvc',
            'title' => 'CVC',
            'short' => 'Maintenance et coordination des lots chauffage, ventilation et climatisation.',
            'hero' => 'Une approche multitechnique pour piloter les besoins CVC avec un interlocuteur unique.',
            'bullets' => ['Contrôles visuels et maintenance préventive', 'Diagnostic global des équipements', 'Lien avec les autres lots techniques', 'Compte-rendu clair pour le client'],
            'image' => asset('img/service-cvc.jpg'),
            'badge' => '🌀',
            'category' => 'cvc',
            'seo' => 'maintenance CVC, dépannage ventilation, entreprise CVC',
        ],
        [
            'slug' => 'pompes-a-chaleur',
            'title' => 'Pompes à chaleur',
            'short' => 'Accompagnement sur l’entretien, le diagnostic de panne et la modernisation des PAC.',
            'hero' => 'PAC air/air et air/eau : maintenance, diagnostic, remise en service et préparation des remplacements.',
            'bullets' => ['Diagnostic de panne PAC', 'Entretien et nettoyage', 'Vérification du fonctionnement', 'Accompagnement remplacement / modernisation'],
            'image' => asset('img/service-energies.jpg'),
            'badge' => '🌿',
            'category' => 'renouvelable',
            'seo' => 'pompe à chaleur dépannage, entretien PAC, maintenance PAC',
        ],
        [
            'slug' => 'ventilation',
            'title' => 'Ventilation',
            'short' => 'Prise en charge des besoins liés aux réseaux de ventilation, extraction et qualité d’air.',
            'hero' => 'Interventions ciblées sur la ventilation des logements, parties communes, commerces et petits locaux techniques.',
            'bullets' => ['VMC, extraction et ventilation simple', 'Contrôle des débits et organes accessibles', 'Maintenance courante', 'Repérage des anomalies et préconisations'],
            'image' => asset('img/service-cvc.jpg'),
            'badge' => '🌬️',
            'category' => 'ventilation',
            'seo' => 'dépannage ventilation, maintenance VMC, extraction air',
        ],
        [
            'slug' => 'maintenance',
            'title' => 'Maintenance multitechnique',
            'short' => 'Contrats et visites pour garder les installations techniques en bon état.',
            'hero' => 'Une logique de maintenance pour réduire les pannes et mieux planifier les interventions.',
            'bullets' => ['Visites préventives', 'Suivi des observations', 'Registre d’intervention', 'Priorisation des actions correctives'],
            'image' => asset('img/hero-van.jpg'),
            'badge' => '🛠️',
            'category' => 'maintenance',
            'seo' => 'maintenance multitechnique, contrat maintenance bâtiment',
        ],
    ];
}

function homepage_services(): array
{
    return array_slice(service_pages(), 0, 4);
}

function find_service(string $slug): ?array
{
    foreach (service_pages() as $service) {
        if ($service['slug'] === $slug) {
            return $service;
        }
    }
    return null;
}

function zone_groups(): array
{
    return [
        [
            'title' => 'Île-de-France',
            'description' => 'Zone stratégique pour les demandes rapides, le dépannage et les visites techniques.',
            'cities' => ['Paris', 'Seine-et-Marne', 'Meaux', 'Marne-la-Vallée', 'Saint-Denis', 'Créteil'],
        ],
        [
            'title' => 'Occitanie',
            'description' => 'Zone adaptée aux interventions programmées, maintenance et accompagnement de proximité.',
            'cities' => ['Toulouse', 'Montpellier', 'Nîmes', 'Perpignan', 'Béziers', 'Albi'],
        ],
    ];
}

function commitments(): array
{
    return [
        'Un positionnement clair pour les dépannages et besoins urgents.',
        'Des pages métier dédiées pour mieux convertir les campagnes locales.',
        'Une image de marque sobre, rassurante et premium.',
        'Des formulaires simples pour capter les leads depuis mobile.',
        'Un espace client léger pour retrouver les demandes et documents.',
        'Une architecture PHP facile à maintenir et à publier.',
    ];
}

function sectors_served(): array
{
    return [
        'Particuliers',
        'Syndics et copropriétés',
        'Commerces et boutiques',
        'Bureaux et petits sites tertiaires',
        'Locaux techniques et parties communes',
    ];
}

function faq_items(): array
{
    return [
        ['question' => 'EMAE intervient-elle uniquement pour le dépannage ?', 'answer' => 'Non. Le site est orienté conversion pour les dépannages, mais EMAE peut aussi présenter ses offres de maintenance, travaux ciblés et modernisation technique.'],
        ['question' => 'Puis-je demander un devis sans créer de compte ?', 'answer' => 'Oui. Le formulaire de devis fonctionne sans compte. Le compte client permet seulement de retrouver vos demandes et certains documents.'],
        ['question' => 'Quelles zones sont mises en avant ?', 'answer' => 'La version 2 du site met en avant l’Île-de-France et l’Occitanie, avec une page dédiée pour travailler ensuite vos villes cibles.'],
        ['question' => 'Le site est-il prêt pour un usage local sur PC ?', 'answer' => 'Oui. Il fonctionne en PHP simple avec stockage JSON, ce qui facilite les tests en local avant publication.'],
        ['question' => 'Le chat est-il un vrai service de messagerie ?', 'answer' => 'Pas encore. Il s’agit d’un assistant rapide de préqualification qui redirige vers le formulaire de contact ou le téléphone d’urgence.'],
    ];
}

function blog_posts(): array
{
    return [
        [
            'slug' => 'depannage-climatisation-que-faire',
            'title' => 'Dépannage climatisation : que faire avant l’intervention ?',
            'excerpt' => 'Les bons réflexes pour préparer un dépannage climatisation et transmettre les bonnes informations.',
            'content' => [
                'Avant de demander une intervention, le client peut déjà noter plusieurs éléments utiles : type d’équipement, message d’erreur éventuel, bruit inhabituel, perte de froid, arrêt complet ou fonctionnement intermittent.',
                'Sur un site pensé pour Google Ads local, ce type de contenu rassure immédiatement le prospect. Il montre que l’entreprise est organisée, qu’elle parle concret et qu’elle sait gérer les urgences.',
                'La page doit également rappeler qu’un diagnostic visuel ou fonctionnel ne remplace pas l’intervention d’un professionnel. L’objectif du contenu est de mieux qualifier la demande, pas de faire une réparation à distance.',
            ],
            'date' => '2026-03-20',
            'category' => 'Climatisation',
        ],
        [
            'slug' => 'quand-remplacer-tableau-electrique',
            'title' => 'Quand faut-il remplacer un tableau électrique ?',
            'excerpt' => 'Repérer les signes qui justifient une mise en sécurité, une rénovation ou un remplacement du tableau.',
            'content' => [
                'Un tableau ancien, mal repéré, surchargé ou présentant des coupures répétées peut justifier une remise à niveau. Le site EMAE peut utiliser cet angle pour attirer des demandes de mise en conformité ou de dépannage.',
                'Un bon article SEO local doit rester simple, lisible et orienté usage client. Il doit expliquer les situations courantes : disjonctions fréquentes, ajout d’équipements, logement ancien, local professionnel transformé ou installation modifiée sans cohérence globale.',
                'En complément, la page service Électricité doit reprendre les messages forts du site : diagnostic, sécurité, clarté du devis et zones d’intervention.',
            ],
            'date' => '2026-03-20',
            'category' => 'Électricité',
        ],
        [
            'slug' => 'entretien-pompe-a-chaleur',
            'title' => 'Entretien pompe à chaleur : pourquoi l’anticiper ?',
            'excerpt' => 'Mieux planifier l’entretien pour limiter les arrêts, préserver les performances et rassurer le client.',
            'content' => [
                'Une pompe à chaleur suivie régulièrement inspire confiance au client final et permet de mieux préparer les interventions. C’est aussi un bon sujet éditorial pour un site orienté maintenance et rénovation énergétique.',
                'Pour EMAE, cette thématique complète naturellement les pages chauffage, climatisation et maintenance multitechnique. Elle permet aussi de construire un maillage interne cohérent entre les différentes pages métier.',
                'L’article peut être enrichi plus tard avec vos vraies pratiques d’entretien, vos photos chantier et vos conseils selon les types d’installations traitées.',
            ],
            'date' => '2026-03-20',
            'category' => 'PAC',
        ],
    ];
}

function find_post(string $slug): ?array
{
    foreach (blog_posts() as $post) {
        if ($post['slug'] === $slug) {
            return $post;
        }
    }
    return null;
}

function realisations_samples(): array
{
    return [
        ['title' => 'Exemple - remise en état d’un tableau électrique', 'type' => 'Électricité', 'summary' => 'Reprise d’une installation désorganisée, mise en sécurité et préparation d’un repérage plus lisible.', 'city' => 'Île-de-France'],
        ['title' => 'Exemple - dépannage d’un réseau sanitaire', 'type' => 'Plomberie', 'summary' => 'Recherche de fuite, remplacement ciblé et remise en service sur un site occupé.', 'city' => 'Occitanie'],
        ['title' => 'Exemple - maintenance d’unités CVC', 'type' => 'CVC', 'summary' => 'Contrôles, nettoyage et relevés de base pour préparer les actions correctives prioritaires.', 'city' => 'Île-de-France'],
        ['title' => 'Exemple - accompagnement sur PAC', 'type' => 'Pompes à chaleur', 'summary' => 'Diagnostic de panne et aide à la décision entre réparation, entretien renforcé ou remplacement.', 'city' => 'Occitanie'],
    ];
}

function format_date_fr(string $date): string
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    $months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
}

function hp_is_clean(): bool
{
    return trim($_POST['website'] ?? '') === '';
}

function check_rate_limit(string $key, int $limit, int $windowSeconds): bool
{
    $now = time();
    $_SESSION['rate_limit'][$key] = array_values(array_filter(
        $_SESSION['rate_limit'][$key] ?? [],
        fn(int $timestamp): bool => ($now - $timestamp) < $windowSeconds
    ));
    if (count($_SESSION['rate_limit'][$key]) >= $limit) {
        return false;
    }
    $_SESSION['rate_limit'][$key][] = $now;
    return true;
}
