<?php

$specialpages = [];

function registerSpecialPage($name, $func) {
	global $specialpages;

	$specialpages[$name] = $func;
}

// Pages that don't exist anymore
foreach (['contributions', 'recentchanges', 'version'] as $page)
	registerSpecialPage($page, function () {
		redirectPerma('/');
	});


// Stubs
foreach (['longpages', 'shortpages'] as $page)
	registerSpecialPage($page, function () {
		die('stub');
	});


// Special:OrphanedPages - Generates a list of "orphaned" pages, not linked from anywhere else on the wiki.
registerSpecialPage('orphanedpages', function () {

	$pagecontent = getPageContent();

	// Iterate over page contents, get a list of linked pages.
	$linkedpages = [];
	foreach ($pagecontent as $content) {
		preg_match_all('/\[\[(.*?)\]\]/', $content, $links);
		foreach ($links[1] as $link)
			$linkedpages[$link] = true;
	}

	// Add a "blacklist" of pages that get linked by the wiki software and therefore aren't really orphans.
	$blacklist = [
		'Main Page', 'Copyright', // wiki software
	];

	foreach ($blacklist as $page)
		$linkedpages[$page] = true;

	// Iterate over pages, any pages not existing in $linkedpages is an orphan!
	$orphanedpages = [];
	foreach ($pagecontent as $pagename => $content) {
		if (!isset($linkedpages[$pagename]))
			$orphanedpages[] = $pagename;
	}

	twigloader()->display('orphanedpages.twig', [
		'orphanedpages' => $orphanedpages
	]);
});

// Special:PageIndex - Generates a list of pages
registerSpecialPage('pageindex', function () {
	twigloader()->display('pageindex.twig', [
		'pages' => getPageList()
	]);
});

// Special:Random - Redirect to a random page
registerSpecialPage('random', function () {
	$pages = getPageList();

	redirect('/%s', str_replace(' ', '_', $pages[array_rand($pages)]));
});

// Special:SpecialPages - List of special pages
registerSpecialPage('specialpages', function () {
	twigloader()->display('specialpages.twig', [
		'specialpages' => [
			'LongPages' => 'Long pages',
			'OrphanedPages' => 'Orphaned pages',
			'PageIndex' => 'Page index',
			'Random' => 'Random',
			'ShortPages' => 'Short pages',
			'WantedPages' => 'Wanted pages'
		]
	]);
});

// Special:Sitemap - Generates a newline separated sitemap for Google Search Console et al.
registerSpecialPage('sitemap', function () {
	$sitemap = new Sitemap('https://wiki.voxelmanip.se/');

	$pages = glob(WIKI_PAGES.'*.md');

	foreach ($pages as $page)
		$sitemap->add(filepathToSlug($page));

	$sitemap->output();
});

// Special:WantedPages - Generates a list of "wanted" pages, ones linked to but don't exist yet.
registerSpecialPage('wantedpages', function () {
	$pagecontent = getPageContent();

	$wantedpages = [];

	foreach ($pagecontent as $content) {
		preg_match_all('/\[\[(.*?)\]\]/', $content, $links);
		foreach ($links[1] as $link) {
			if (!checkPageExistance($link)) {
				if (!isset($wantedpages[$link])) $wantedpages[$link] = 0;
				$wantedpages[$link]++;
			}
		}
	}

	twigloader()->display('wantedpages.twig', [
		'wantedpages' => $wantedpages
	]);
});
