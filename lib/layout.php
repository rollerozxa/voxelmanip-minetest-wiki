<?php

/**
 * Twig loader, initializes Twig with standard configurations and extensions.
 *
 * @param string $subfolder Subdirectory to use in the templates/ directory.
 * @return \Twig\Environment Twig object.
 */
function twigloader() {
	global $userdata, $log, $domain, $uri, $config, $page_slugified, $type;

	$loader = new \Twig\Loader\FilesystemLoader('templates/');

	$twig = new \Twig\Environment($loader, [
		'cache' => TPL_CACHE,
		'auto_reload' => true,
	]);

	$twig->addGlobal('userdata', $userdata);
	$twig->addGlobal('log', $log);
	$twig->addGlobal('domain', $domain);
	$twig->addGlobal('uri', $uri);
	$twig->addGlobal('pagename', substr($_SERVER['PHP_SELF'], 0, -4));
	$twig->addGlobal('config', $config);
	$twig->addGlobal('page_slugified', $page_slugified);
	$twig->addGlobal('type', $type);

	$twig->addExtension(new WikiExtension());

	return $twig;
}

class WikiExtension extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		global $profiler;

		return [
			new \Twig\TwigFunction('userlink', 'userlink', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			})
		];
	}
	public function getFilters() {
		return [

			// Markdown function for wiki, sanitized and using the ToC extension.
			new \Twig\TwigFilter('markdown_wiki', 'parsing', ['is_safe' => ['html']]),

			new \Twig\TwigFilter('number_format', 'number_format')

		];
	}
}

function error($title, $message) {
	echo twigloader()->render('_error.twig', ['err_title' => $title, 'err_message' => $message]);
	die();
}

function redirect($url) {
	header(sprintf('Location: %s', $url));
	die();
}

