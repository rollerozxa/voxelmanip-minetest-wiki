<?php
require('lib/common.php');

if (str_starts_with(URI, '/wiki/'))
	redirectPerma("%s", substr(URI, 5));

$path = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));

if ($path != '/') $p = substr($path, 1);

$page = (isset($p) ? str_replace('_', ' ', $p) : 'Main Page');
$page_slugified = (isset($p) ? $p : 'Main_Page');

if (isset($_GET['rev']) || isset($_GET['action']))
	redirectPerma('/%s', $page_slugified);

if (str_starts_with($page, 'Special:')) {
	$specialpage = strtolower(substr($page, 8));
	if (isset($specialpages[$specialpage])) {
		$specialpages[$specialpage]();
		die();
	}
}

$filename = WIKI_PAGES.str_replace('/', 'Ä', $page_slugified).'.md';

if (file_exists($filename))
	$pagecontent = file_get_contents($filename);
else
	http_response_code(404);

twigloader()->display('index.twig', [
	'pagetitle' => $page,
	'pagetitle_slugified' => str_replace(' ', '_', $page),
	'pagecontent' => $pagecontent ?? null,
]);
