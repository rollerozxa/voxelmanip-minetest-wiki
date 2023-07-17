<?php

$provider = new League\OAuth2\Client\Provider\Github([
	'clientId'          => $config['gh_oauth_id'],
	'clientSecret'      => $config['gh_oauth_secret'],
	'redirectUri'       => $config['gh_oauth_redirect'],
]);

if (!isset($_GET['code'])) {
	$authUrl = $provider->getAuthorizationUrl();
	redirect($authUrl);
} else {
	$token = $provider->getAccessToken('authorization_code', [
		'code' => $_GET['code']
	]);

	try {
		$user = $provider->getResourceOwner($token);

		/*printf('id: %s<br>', $user->getId());
		printf('email: %s<br>', $user->getEmail());
		printf('name: %s<br>', $user->getName());
		printf('nickname: %s<br>', $user->getNickname());
		printf('url: %s<br>', $user->getUrl());
		printf('token: %s<br>', $token->getToken());*/

		// The GH OAuth library puts nickname in getName and username in getNickname...
		$id = $user->getId();
		$email = $user->getEmail();
		$username = $user->getNickname();
		$dispname = $user->getName();
		$ghtoken = $token->getToken();

		$token = result("SELECT token FROM users WHERE github_id = ?", [$id]);

		if (!$token) {

			// No account, register one
			$token = bin2hex(random_bytes(32));
			query("INSERT INTO users (name, displayname, email, token, github_id, github_token, joined) VALUES (?,?,?,?,?,?,?)",
				[$username, $dispname, $email, $token, $id, $ghtoken, time()]);
		}

		setcookie('token', $token, 2147483647, '/');

		redirect('/wiki/');

	} catch (Exception $e) {
		die("Something went wrong while requesting GitHub's API");
	}
}
