<?php

define('GIT_REPO', 'https://github.com/rollerozxa/voxelmanip-wiki');

// load profiler first
require_once('lib/profiler.php');
$profiler = new Profiler();

require_once('vendor/autoload.php');
foreach (glob("lib/*.php") as $file)
	require_once($file);

// Security headers.
header("Content-Security-Policy:"
	."default-src 'self';"
	."script-src 'self';"
	."img-src 'self' data: *.voxelmanip.se voxelmanip.se *.minetest.net minetest.net *.imgur.com imgur.com *.github.com github.com *.githubusercontent.com;"
	."media-src 'self';"
	."style-src 'self' 'unsafe-inline';");

header("Referrer-Policy: strict-origin-when-cross-origin");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-Xss-Protection: 1; mode=block");

if (php_sapi_name() != "cli") {
	// Shorter variables for common $_SERVER values
	define('URI', $_SERVER['REQUEST_URI'] ?? null);
	define('DOMAIN', (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']);
} else {
	// CLI fallback variables
	define('URI', '/');
	define('DOMAIN', 'localhost');
}
