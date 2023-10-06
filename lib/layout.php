<?php

function twigloader() {
	$loader = new \Twig\Loader\FilesystemLoader('templates/');

	$twig = new \Twig\Environment($loader, [
		'cache' => "/tmp/wiki_vm/",
		'auto_reload' => true,
	]);

	$twig->addGlobal('pagename', 'FIXME');

	$twig->addExtension(new WikiExtension());

	return $twig;
}

class WikiExtension extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		global $profiler;

		return [
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			})
		];
	}
	public function getFilters() {
		return [
			// Markdown function for wiki, sanitized and using the ToC extension.
			new \Twig\TwigFilter('markdown_wiki', 'parsing', ['is_safe' => ['html']])
		];
	}
}

function redirect($url, ...$args) {
	header('Location: '.sprintf($url, ...$args));
	die();
}

function redirectPerma($url, ...$args) {
	header('Location: '.sprintf($url, ...$args), true, 301);
	die();
}
