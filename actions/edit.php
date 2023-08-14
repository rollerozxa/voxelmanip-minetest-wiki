<?php
$type = 'edit';

$action = $_POST['action'] ?? null;

$pagedata = fetch("SELECT p.*, r.content FROM wikipages p JOIN wikirevisions r ON p.cur_revision = r.revision AND p.title = r.page WHERE BINARY p.title = ?", [$page]);

if ($action == 'Show changes' && $pagedata) {
	$diff = new Diff(
		explode("\n", $pagedata['content']),
		explode("\n", normalise($_POST['text'])));
	$renderer = new Diff_Renderer_Html_Inline;
	$diffoutput = $diff->render($renderer);
}

if ($action == 'Preview' || $action == 'Show changes') {
	$pagedata['content'] = $_POST['text'];
	$description = $_POST['description'];
}

if ($log && $action == 'Save changes' && (!$pagedata || $userdata['rank'] >= $pagedata['minedit'])) {
	$content = normalise($_POST['text'] ?? '');
	$description = $_POST['description'] ?? null;
	$size = strlen($content);

	$minedit = $_POST['minedit'] ?? null;
	if ($userdata['rank'] > 2 && $minedit) {
		query("UPDATE wikipages SET minedit = ? WHERE BINARY title = ?",
			[$minedit, $page]);
	}

	$actuallysubmit = !($content == $pagedata['content'] || $content == '');

	if ($actuallysubmit) {
		if ($pagedata) {
			query("UPDATE wikipages SET cur_revision = cur_revision + 1 WHERE BINARY title = ?",
				[$page]);

			$newrev = result("SELECT cur_revision FROM wikipages WHERE BINARY title = ?", [$page]);
			$oldsize = result("SELECT size FROM wikirevisions WHERE BINARY page = ? AND revision = ?", [$page, $newrev-1]);

			insertInto('wikirevisions', [
				'page' => $page,
				'revision' => $newrev,
				'author' => $userdata['id'],
				'time' => time(),
				'size' => $size,
				'sizediff' => ($size - $oldsize),
				'description' => $description,
				'content' => $content
			]);
		} else {
			insertInto('wikipages', ['title' => $page]);

			$newrev = 1;

			insertInto('wikirevisions', [
				'page' => $page,
				'author' => $userdata['id'],
				'time' => time(),
				'size' => $size,
				'description' => $description,
				'content' => $content
			]);
		}

		wikiEditHook([
			'page' => $page,
			'page_slugified' => $page_slugified,
			'description' => $description,
			'revision' => $newrev,
			'u_id' => $userdata['id'],
			'u_name' => $userdata['name']
		]);
	}

	redirect('/'.$page_slugified);
}

$pagedata['minedit'] = $_POST['minedit'] ?? ($pagedata['minedit'] ?? 1);

echo twigloader()->render('edit.twig', [
	'pagetitle' => $page,
	'pagetitle_slugified' => str_replace(' ', '_', $page),
	'page' => $pagedata,
	'action' => $action,
	'change_description' => $description ?? null,
	'ranks' => $ranks,
	'diff' => $diffoutput ?? null
]);
