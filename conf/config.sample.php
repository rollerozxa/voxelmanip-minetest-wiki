<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'wiki_vm');
define('DB_USER', '');
define('DB_PASS', '');

define('TPL_CACHE', '/tmp/wiki_vm/');

// Customise your wiki
$config['title'] = "Wiki";
$config['description'] = "A very cool wiki.";
$config['logo'] = 'assets/logo.png';

// Allow registrations?
$config['allowregistrations'] = false;
