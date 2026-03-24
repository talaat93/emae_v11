
<?php
$adminSection = 'home_hero';
require __DIR__ . '/partials/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ([
        'hero_bg_from','hero_bg_to','hero_glow_left','hero_glow_right',
        'home_eyebrow','home_eyebrow_color','home_title','home_title_color','home_lead','home_lead_color','home_bullets',
        'home_chip_1','home_chip_2','home_chip_3','home_chip_4','home_chip_5','home_chip_6',
        'home_button1_label','home_button1_url','home_button2_label','home_button2_url',
        'home_feature_1_title','home_feature_1_text','home_feature_2_title','home_feature_2_text','home_feature_3_title','home_feature_3_text',
        'home_quote_eyebrow','home_quote_title','home_quote_service_label','home_quote_city_label','home_quote_city_placeholder','home_quote_button_label','home_quote_meta'
    ] as $field) {
        set_setting($field, trim((string) ($_POST[$field] ?? '')));
    }
    flash('success', 'Bloc hero premium enregistré.');
    redirect_to('admin/home_hero.php');
}
$hero = hero_settings();
?>
<div class="admin-page-toolbar">
  <div><div class="admin-breadcrumb">Accueil</div><h1 class="admin-page-title">Hero premium & bloc devis</h1><p class="admin-page-subtitle">Fond, grand titre, chips, cartes infos et formulaire à droite.</p></div>
  <div class="admin-toolbar-actions"><a class="admin-btn admin-btn--secondary" href="<?= e(route_url('')) ?>" target="_blank">Voir le site</a></div>
</div>
<form method="post" class="admin-stack">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Fond & ambiance</h2><p>Reprend le style premium bleu/violet montré auparavant.</p></div>
  <div class="admin-panel__body">
    <div class="admin-form-grid admin-form-grid--2">
      <label class="admin-field"><span>Fond gauche</span><input type="color" name="hero_bg_from" value="<?= e($hero['bg_from']) ?>"></label>
      <label class="admin-field"><span>Fond droite</span><input type="color" name="hero_bg_to" value="<?= e($hero['bg_to']) ?>"></label>
      <label class="admin-field"><span>Lueur gauche</span><input type="color" name="hero_glow_left" value="<?= e($hero['glow_left']) ?>"></label>
      <label class="admin-field"><span>Lueur droite</span><input type="color" name="hero_glow_right" value="<?= e($hero['glow_right']) ?>"></label>
    </div>
  </div>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Texte principal</h2><p>Le contenu à gauche du bloc devis.</p></div>
  <div class="admin-panel__body">
    <label class="admin-field"><span>Petit texte du haut</span><input type="text" name="home_eyebrow" value="<?= e($hero['eyebrow']) ?>"></label>
    <label class="admin-field"><span>Couleur petit texte</span><input type="color" name="home_eyebrow_color" value="<?= e($hero['eyebrow_color']) ?>"></label>
    <label class="admin-field"><span>Grand titre</span><textarea name="home_title" rows="4"><?= e($hero['title']) ?></textarea></label>
    <label class="admin-field"><span>Couleur grand titre</span><input type="color" name="home_title_color" value="<?= e($hero['title_color']) ?>"></label>
    <label class="admin-field"><span>Texte descriptif</span><textarea name="home_lead" rows="4"><?= e($hero['lead']) ?></textarea></label>
    <label class="admin-field"><span>Couleur texte descriptif</span><input type="color" name="home_lead_color" value="<?= e($hero['lead_color']) ?>"></label>
    <label class="admin-field"><span>Liste à puces (une ligne = une puce)</span><textarea name="home_bullets" rows="5"><?= e(setting('home_bullets', "Intervention moyenne sous 2h
Devis gratuit et sans engagement
Zone couverte : Île-de-France et Occitanie")) ?></textarea></label>
  </div>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Chips / onglets</h2><p>Les capsules sous le texte.</p></div>
  <div class="admin-panel__body">
    <div class="admin-form-grid admin-form-grid--3">
      <?php for ($i=1; $i<=6; $i++): ?>
        <label class="admin-field"><span>Chip <?= $i ?></span><input type="text" name="home_chip_<?= $i ?>" value="<?= e(setting('home_chip_' . $i, '')) ?>"></label>
      <?php endfor; ?>
    </div>
  </div>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Boutons</h2><p>Boutons principaux sous les chips.</p></div>
  <div class="admin-panel__body">
    <div class="admin-form-grid admin-form-grid--2">
      <label class="admin-field"><span>Texte bouton 1</span><input type="text" name="home_button1_label" value="<?= e($hero['button1_label']) ?>"></label>
      <label class="admin-field"><span>Lien bouton 1</span><input type="text" name="home_button1_url" value="<?= e($hero['button1_url']) ?>"></label>
      <label class="admin-field"><span>Texte bouton 2</span><input type="text" name="home_button2_label" value="<?= e($hero['button2_label']) ?>"></label>
      <label class="admin-field"><span>Lien bouton 2</span><input type="text" name="home_button2_url" value="<?= e($hero['button2_url']) ?>"></label>
    </div>
  </div>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Cartes infos</h2><p>Les 3 mini cartes premium visibles sous les boutons.</p></div>
  <div class="admin-panel__body">
    <div class="admin-form-grid admin-form-grid--3">
      <?php for ($i=1; $i<=3; $i++): ?>
        <div class="repeat-card">
          <h3>Carte <?= $i ?></h3>
          <label class="admin-field"><span>Titre</span><input type="text" name="home_feature_<?= $i ?>_title" value="<?= e(setting('home_feature_' . $i . '_title', '')) ?>"></label>
          <label class="admin-field"><span>Texte</span><textarea name="home_feature_<?= $i ?>_text" rows="3"><?= e(setting('home_feature_' . $i . '_text', '')) ?></textarea></label>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Bloc devis à droite</h2><p>Le formulaire blanc mis en avant dans le hero.</p></div>
  <div class="admin-panel__body">
    <label class="admin-field"><span>Petit texte du bloc devis</span><input type="text" name="home_quote_eyebrow" value="<?= e($hero['quote_eyebrow']) ?>"></label>
    <label class="admin-field"><span>Titre du bloc devis</span><input type="text" name="home_quote_title" value="<?= e($hero['quote_title']) ?>"></label>
    <div class="admin-form-grid admin-form-grid--2">
      <label class="admin-field"><span>Libellé service</span><input type="text" name="home_quote_service_label" value="<?= e($hero['quote_service_label']) ?>"></label>
      <label class="admin-field"><span>Libellé ville</span><input type="text" name="home_quote_city_label" value="<?= e($hero['quote_city_label']) ?>"></label>
      <label class="admin-field"><span>Placeholder ville</span><input type="text" name="home_quote_city_placeholder" value="<?= e($hero['quote_city_placeholder']) ?>"></label>
      <label class="admin-field"><span>Texte bouton</span><input type="text" name="home_quote_button_label" value="<?= e($hero['quote_button_label']) ?>"></label>
    </div>
    <label class="admin-field"><span>Texte bas du bloc</span><input type="text" name="home_quote_meta" value="<?= e($hero['quote_meta']) ?>"></label>
  </div>
</section>
<div class="admin-savebar"><button class="admin-btn admin-btn--primary" type="submit">Enregistrer le hero</button></div>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
