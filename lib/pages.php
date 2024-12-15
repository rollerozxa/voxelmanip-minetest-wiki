<?php

define('WIKI_PAGES', 'data/pages/');

function checkPageExistance($pagename) {
	return file_exists(humanToFilepath($pagename));
}

function filepathToSlug($name) {
	return str_replace(
		[WIKI_PAGES, '.md', 'Ä'],
		['', '', '/'],
	$name);
}

function filepathToHuman($name) {
	return str_replace(
		[WIKI_PAGES, '.md', 'Ä', '_'],
		['', '', '/', ' '],
	$name);
}

function humanToFilepath($name) {
	return WIKI_PAGES.str_replace(['/', ' '], ['Ä', '_'], $name).'.md';
}

function getPageList() {
	$pages = glob(WIKI_PAGES.'*.md');

	$pagelist = [];
	foreach ($pages as $page)
		$pagelist[] = filepathToHuman($page);

	return $pagelist;
}

function getPageContent() {
	$pages = glob(WIKI_PAGES.'*.md');

	$pagecontents = [];
	foreach ($pages as $page)
		$pagecontents[filepathToHuman($page)] = file_get_contents($page);

	return $pagecontents;
}

function getPageCount() {
	return count(glob(WIKI_PAGES.'*.md'));
}

if (file_exists('data/redirects.php'))
	require('data/redirects.php');
else
	$pageRedirects = [];
