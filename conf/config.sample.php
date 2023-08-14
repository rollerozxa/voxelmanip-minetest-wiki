<?php
$host = '127.0.0.1';
$db   = 'wiki';
$user = '';
$pass = '';

$tplCache = 'templates/cache';
$tplNoCache = false; // **DO NOT SET AS TRUE IN PROD - DEV ONLY**

// Customise your wiki
$config['title'] = "Wiki";
$config['description'] = "A very cool wiki.";
$config['logo'] = 'assets/logo.png';

// Allow registrations?
$config['allowregistrations'] = false;
