<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/render.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [
    route_url(''),
    route_url('services'),
    route_url('realisations'),
    route_url('faq'),
    route_url('contact'),
    route_url('quote'),
];

foreach (all_pages() as $page) {
    $urls[] = route_url($page['slug']);
}
$urls = array_values(array_unique($urls));

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?= e($url) ?></loc>
    <changefreq>weekly</changefreq>
    <priority><?= $url === route_url('') ? '1.0' : '0.8' ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
