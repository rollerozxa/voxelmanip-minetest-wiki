<?php
$sitemap = new Sitemap('https://'.$_SERVER['HTTP_HOST'].'/');

$pages = query("SELECT title FROM wikipages ORDER BY title ASC");

foreach ($pages as $page)
	$sitemap->add(str_replace(' ', '_', $page['title']));

$sitemap->output();
