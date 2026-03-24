<?php
$adminSection = 'home-hero';
require __DIR__ . '/partials/header.php';

function hero_admin_setting(string $key, string $fallback = ''): string {
    return site_setting($key, $fallback);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fields = [
        'hero_bg_from',
        'hero_bg_to',
        'hero_glow_left',
        'hero_glow_right',

        'home_eyebrow',
        'home_title',
        'home_lead',

        'home_eyebrow_color',
        'home_title_color',
        'home_lead_color',

        'home_chip_1',
        'home_chip_2',
        'home_chip_3',
        'home_chip_4',
        'home_chip_5',
        'home_chip_6',

        'home_button1_label',
        'home_button1_url',
        'home_button2_label',
        'home_button2_url',

        'home_feature_1_title',
        'home_feature_1_text',
        'home_feature_2_title',
        'home_feature_2_text',
        'home_feature_3_title',
        'home_feature_3_text',

        'home_quote_eyebrow',
        'home_quote_title',
        'home_quote_service_label',
        'home_quote_city_label',
        'home_quote_city_placeholder',
        'home_quote_button_label',
        'home_quote_meta',
    ];

    foreach ($fields as $field) {
        set_site_setting($field, trim($_POST[$field] ?? ''));
    }

    flash('success', 'Hero premium enregistré.');
    redirect('admin/homepage_hero.php');
}
?>

<div class="admin-page-toolbar">
  <div>
    <div class="admin-breadcrumb">Accueil / Hero premium</div>
    <h1 class="admin-page-title">Hero premium</h1>
    <p class="admin-page-subtitle">Tu modifies ici le fond, la typo, les onglets, les cartes infos et le bloc devis.</p>
  </div>
</div>

<form method="post" class="admin-stack">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Fond</h2>
      <p>Couleurs du fond comme sur ton image.</p>
    </div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Fond gauche</span>
          <input type="color" name="hero_bg_from" value="<?= e(hero_admin_setting('hero_bg_from', '#04113b')) ?>">
        </label>
        <label class="admin-field">
          <span>Fond droite</span>
          <input type="color" name="hero_bg_to" value="<?= e(hero_admin_setting('hero_bg_to', '#18357f')) ?>">
        </label>
      </div>

      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Lueur gauche</span>
          <input type="color" name="hero_glow_left" value="<?= e(hero_admin_setting('hero_glow_left', '#4a233f')) ?>">
        </label>
        <label class="admin-field">
          <span>Lueur droite</span>
          <input type="color" name="hero_glow_right" value="<?= e(hero_admin_setting('hero_glow_right', '#335dff')) ?>">
        </label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Texte principal</h2>
    </div>
    <div class="admin-panel__body">
      <label class="admin-field">
        <span>Petit texte</span>
        <input type="text" name="home_eyebrow" value="<?= e(hero_admin_setting('home_eyebrow', 'ENTREPRISE MULTITECHNIQUE AVANCÉE')) ?>">
      </label>

      <label class="admin-field">
        <span>Grand titre</span>
        <textarea name="home_title" rows="4"><?= e(hero_admin_setting('home_title', "Le partenaire\ntechnique de vos\nbâtiments en Île-de-\nFrance et en Occitanie")) ?></textarea>
      </label>

      <label class="admin-field">
        <span>Texte descriptif</span>
        <textarea name="home_lead" rows="4"><?= e(hero_admin_setting('home_lead', "EMAE aide à capter des clients pour le dépannage, l’entretien et les besoins techniques en électricité, plomberie, chauffage, climatisation, CVC et pompes à chaleur.")) ?></textarea>
      </label>

      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <label class="admin-field">
          <span>Couleur petit texte</span>
          <input type="color" name="home_eyebrow_color" value="<?= e(hero_admin_setting('home_eyebrow_color', '#83a6ff')) ?>">
        </label>

        <label class="admin-field">
          <span>Couleur titre</span>
          <input type="color" name="home_title_color" value="<?= e(hero_admin_setting('home_title_color', '#ffffff')) ?>">
        </label>

        <label class="admin-field">
          <span>Couleur descriptif</span>
          <input type="color" name="home_lead_color" value="<?= e(hero_admin_setting('home_lead_color', '#dbe6ff')) ?>">
        </label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Onglets</h2>
    </div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <label class="admin-field">
            <span>Onglet <?= $i ?></span>
            <input type="text" name="home_chip_<?= $i ?>" value="<?= e(hero_admin_setting('home_chip_' . $i, ['','Électricité','Plomberie','CVC','Climatisation','Chauffage','PAC'][$i] ?? '')) ?>">
          </label>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Boutons</h2>
    </div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Texte bouton 1</span>
          <input type="text" name="home_button1_label" value="<?= e(hero_admin_setting('home_button1_label', 'Demander un devis')) ?>">
        </label>
        <label class="admin-field">
          <span>Lien bouton 1</span>
          <input type="text" name="home_button1_url" value="<?= e(hero_admin_setting('home_button1_url', url_for('quote.php'))) ?>">
        </label>
      </div>

      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Texte bouton 2</span>
          <input type="text" name="home_button2_label" value="<?= e(hero_admin_setting('home_button2_label', 'Domaines d’intervention')) ?>">
        </label>
        <label class="admin-field">
          <span>Lien bouton 2</span>
          <input type="text" name="home_button2_url" value="<?= e(hero_admin_setting('home_button2_url', url_for('services.php'))) ?>">
        </label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Cartes infos</h2>
    </div>
    <div class="admin-panel__body">
      <div class="admin-form-grid admin-form-grid--3" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;">
        <label class="admin-field">
          <span>Titre 1</span>
          <input type="text" name="home_feature_1_title" value="<?= e(hero_admin_setting('home_feature_1_title', 'Mobile first')) ?>">
        </label>
        <label class="admin-field">
          <span>Titre 2</span>
          <input type="text" name="home_feature_2_title" value="<?= e(hero_admin_setting('home_feature_2_title', 'Conversion rapide')) ?>">
        </label>
        <label class="admin-field">
          <span>Titre 3</span>
          <input type="text" name="home_feature_3_title" value="<?= e(hero_admin_setting('home_feature_3_title', 'Image premium')) ?>">
        </label>

        <label class="admin-field">
          <span>Texte 1</span>
          <textarea name="home_feature_1_text" rows="3"><?= e(hero_admin_setting('home_feature_1_text', 'Prêt pour Google Ads local')) ?></textarea>
        </label>
        <label class="admin-field">
          <span>Texte 2</span>
          <textarea name="home_feature_2_text" rows="3"><?= e(hero_admin_setting('home_feature_2_text', 'Appel, devis, contact, espace client')) ?></textarea>
        </label>
        <label class="admin-field">
          <span>Texte 3</span>
          <textarea name="home_feature_3_text" rows="3"><?= e(hero_admin_setting('home_feature_3_text', 'Corporate, claire et rassurante')) ?></textarea>
        </label>
      </div>
    </div>
  </section>

  <section class="admin-panel">
    <div class="admin-panel__head">
      <h2>Bloc devis</h2>
    </div>
    <div class="admin-panel__body">
      <label class="admin-field">
        <span>Petit texte</span>
        <input type="text" name="home_quote_eyebrow" value="<?= e(hero_admin_setting('home_quote_eyebrow', 'DEMANDE DE DEVIS GRATUITE')) ?>">
      </label>

      <label class="admin-field">
        <span>Titre</span>
        <input type="text" name="home_quote_title" value="<?= e(hero_admin_setting('home_quote_title', 'Obtenir un rappel rapide')) ?>">
      </label>

      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Libellé service</span>
          <input type="text" name="home_quote_service_label" value="<?= e(hero_admin_setting('home_quote_service_label', 'Service')) ?>">
        </label>
        <label class="admin-field">
          <span>Libellé ville</span>
          <input type="text" name="home_quote_city_label" value="<?= e(hero_admin_setting('home_quote_city_label', 'Ville')) ?>">
        </label>
      </div>

      <label class="admin-field">
        <span>Placeholder ville</span>
        <input type="text" name="home_quote_city_placeholder" value="<?= e(hero_admin_setting('home_quote_city_placeholder', 'Ex : Meaux, Paris, Toulouse')) ?>">
      </label>

      <div class="admin-form-grid admin-form-grid--2">
        <label class="admin-field">
          <span>Texte bouton</span>
          <input type="text" name="home_quote_button_label" value="<?= e(hero_admin_setting('home_quote_button_label', 'Continuer')) ?>">
        </label>
        <label class="admin-field">
          <span>Texte bas</span>
          <input type="text" name="home_quote_meta" value="<?= e(hero_admin_setting('home_quote_meta', 'Artisans disponibles • devis gratuit • réponse rapide')) ?>">
        </label>
      </div>
    </div>
  </section>

  <div class="admin-savebar">
    <button class="admin-btn admin-btn--primary" type="submit">Enregistrer</button>
  </div>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>