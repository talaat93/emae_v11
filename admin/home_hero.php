<?php
$adminSection = 'home-hero';
require __DIR__ . '/partials/header.php';

function hero_admin_setting(string $key, string $fallback = ''): string
{
    return setting($key, $fallback);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fields = [
        'hero_bg_from','hero_bg_to','hero_glow_left','hero_glow_right',
        'home_eyebrow','home_title','home_lead','home_bullets',
        'home_eyebrow_color','home_title_color','home_lead_color',
        'home_chip_1','home_chip_2','home_chip_3','home_chip_4','home_chip_5','home_chip_6',
        'home_button1_label','home_button1_url','home_button2_label','home_button2_url',
        'home_feature_1_title','home_feature_1_text','home_feature_2_title','home_feature_2_text','home_feature_3_title','home_feature_3_text',
        'home_quote_eyebrow','home_quote_title','home_quote_service_label','home_quote_city_label','home_quote_city_placeholder','home_quote_button_label','home_quote_meta',
    ];

    foreach ($fields as $field) {
        set_setting($field, trim((string) ($_POST[$field] ?? '')));
    }

    flash('success', 'Bloc accueil enregistré avec succès.');
    redirect_to('admin/home_hero.php');
}
?>
<div class="admin-page-toolbar">
  <div>
    <div class="admin-breadcrumb">Accueil / Bloc hero</div>
    <h1 class="admin-page-title">Texte principal & devis</h1>
    <p class="admin-page-subtitle">Modifie ici le grand titre, le descriptif, les puces, les boutons, les cartes infos et le bloc devis à droite.</p>
  </div>
  <div class="admin-toolbar-actions">
    <a class="admin-btn admin-btn--secondary" href="<?= e(route_url('')) ?>" target="_blank">Voir le site</a>
  </div>
</div>

<form method="post" class="admin-stack">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

  <section class="admin-panel">
    <div class="admin-panel__head"><h2>1. Fond</h2><p>Fond du hero comme ton visuel.</p></div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Fond gauche</span><input type="color" name="hero_bg_from" value="<?= e(hero_admin_setting('hero_bg_from', '#04113b')) ?>"></label>
        <label class="admin-field"><span>Fond droite</span><input type="color" name="hero_bg_to" value="<?= e(hero_admin_setting('hero_bg_to', '#18357f')) ?>"></label>
      </div>
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Lueur gauche</span><input type="color" name="hero_glow_left" value="<?= e(hero_admin_setting('hero_glow_left', '#4a233f')) ?>"></label>
        <label class="admin-field"><span>Lueur droite</span><input type="color" name="hero_glow_right" value="<?= e(hero_admin_setting('hero_glow_right', '#335dff')) ?>"></label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head"><h2>2. Texte principal</h2><p>Titre, descriptif et puces.</p></div>
    <div class="admin-panel__body">
      <label class="admin-field"><span>Petit texte du haut</span><input type="text" name="home_eyebrow" value="<?= e(hero_admin_setting('home_eyebrow', 'EMAE - INTERVENTION RAPIDE 24H/24 7J/7')) ?>"></label>
      <label class="admin-field"><span>Grand titre</span><textarea name="home_title" rows="4"><?= e(hero_admin_setting('home_title', 'Dépannage et rénovation en électricité, climatisation, PAC, ventilation et chauffage')) ?></textarea></label>
      <label class="admin-field"><span>Texte descriptif</span><textarea name="home_lead" rows="4"><?= e(hero_admin_setting('home_lead', 'Intervention moyenne en moins de 2h sur l’Île-de-France et l’Occitanie. Appel immédiat, devis rapide, urgence 24h/24 7j/7.')) ?></textarea></label>
      <label class="admin-field"><span>Puces (une ligne = une puce)</span><textarea name="home_bullets" rows="5"><?= e(hero_admin_setting('home_bullets', "Intervention moyenne sous 2h\nDevis gratuit et sans engagement\nZone couverte : Île-de-France et Occitanie")) ?></textarea></label>
      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <label class="admin-field"><span>Couleur petit texte</span><input type="color" name="home_eyebrow_color" value="<?= e(hero_admin_setting('home_eyebrow_color', '#83a6ff')) ?>"></label>
        <label class="admin-field"><span>Couleur titre</span><input type="color" name="home_title_color" value="<?= e(hero_admin_setting('home_title_color', '#ffffff')) ?>"></label>
        <label class="admin-field"><span>Couleur descriptif</span><input type="color" name="home_lead_color" value="<?= e(hero_admin_setting('home_lead_color', '#dbe6ff')) ?>"></label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head"><h2>3. Boutons & onglets</h2><p>Les 2 boutons et les petites pastilles sous le texte.</p></div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Texte bouton 1</span><input type="text" name="home_button1_label" value="<?= e(hero_admin_setting('home_button1_label', 'Appeler maintenant')) ?>"></label>
        <label class="admin-field"><span>Lien bouton 1</span><input type="text" name="home_button1_url" value="<?= e(hero_admin_setting('home_button1_url', company_phone_link())) ?>"></label>
      </div>
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Texte bouton 2</span><input type="text" name="home_button2_label" value="<?= e(hero_admin_setting('home_button2_label', 'Demander un devis')) ?>"></label>
        <label class="admin-field"><span>Lien bouton 2</span><input type="text" name="home_button2_url" value="<?= e(hero_admin_setting('home_button2_url', route_url('quote'))) ?>"></label>
      </div>
      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <label class="admin-field"><span>Onglet <?= $i ?></span><input type="text" name="home_chip_<?= $i ?>" value="<?= e(hero_admin_setting('home_chip_' . $i, '')) ?>"></label>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head"><h2>4. Bandeau infos</h2><p>Ces 3 textes seront affichés sur un seul long onglet sous les boutons.</p></div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <label class="admin-field"><span>Titre carte 1</span><input type="text" name="home_feature_1_title" value="<?= e(hero_admin_setting('home_feature_1_title', 'Mobile first')) ?>"></label>
        <label class="admin-field"><span>Titre carte 2</span><input type="text" name="home_feature_2_title" value="<?= e(hero_admin_setting('home_feature_2_title', 'Conversion rapide')) ?>"></label>
        <label class="admin-field"><span>Titre carte 3</span><input type="text" name="home_feature_3_title" value="<?= e(hero_admin_setting('home_feature_3_title', 'Image premium')) ?>"></label>
        <label class="admin-field"><span>Texte carte 1</span><textarea name="home_feature_1_text" rows="3"><?= e(hero_admin_setting('home_feature_1_text', 'Prêt pour Google Ads local')) ?></textarea></label>
        <label class="admin-field"><span>Texte carte 2</span><textarea name="home_feature_2_text" rows="3"><?= e(hero_admin_setting('home_feature_2_text', 'Appel, devis, contact, espace client')) ?></textarea></label>
        <label class="admin-field"><span>Texte carte 3</span><textarea name="home_feature_3_text" rows="3"><?= e(hero_admin_setting('home_feature_3_text', 'Corporate, claire et rassurante')) ?></textarea></label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head"><h2>5. Bloc devis à droite</h2><p>Titre et libellés du formulaire.</p></div>
    <div class="admin-panel__body">
      <label class="admin-field"><span>Petit texte bloc devis</span><input type="text" name="home_quote_eyebrow" value="<?= e(hero_admin_setting('home_quote_eyebrow', 'Demande rapide')) ?>"></label>
      <label class="admin-field"><span>Titre bloc devis</span><input type="text" name="home_quote_title" value="<?= e(hero_admin_setting('home_quote_title', 'Être rappelé')) ?>"></label>
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Libellé service</span><input type="text" name="home_quote_service_label" value="<?= e(hero_admin_setting('home_quote_service_label', 'Service')) ?>"></label>
        <label class="admin-field"><span>Libellé ville</span><input type="text" name="home_quote_city_label" value="<?= e(hero_admin_setting('home_quote_city_label', 'Ville')) ?>"></label>
      </div>
      <label class="admin-field"><span>Placeholder ville</span><input type="text" name="home_quote_city_placeholder" value="<?= e(hero_admin_setting('home_quote_city_placeholder', 'Ex : Meaux, Paris, Toulouse')) ?>"></label>
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field"><span>Texte bouton</span><input type="text" name="home_quote_button_label" value="<?= e(hero_admin_setting('home_quote_button_label', 'Être rappelé')) ?>"></label>
        <label class="admin-field"><span>Texte bas</span><input type="text" name="home_quote_meta" value="<?= e(hero_admin_setting('home_quote_meta', 'Artisans disponibles • devis gratuit • réponse rapide')) ?>"></label>
      </div>
    </div>
  </section>

  <div class="admin-savebar"><button class="admin-btn admin-btn--primary" type="submit">Enregistrer</button></div>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
