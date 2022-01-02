<?php

function base64UrlEncode($text) {
	return str_replace(
		['+', '/', '='],
		['-', '_', ''],
		base64_encode($text)
	);
}

function getSecret() {
	return "SomeVerySecretThingtoSignTokens";
}

function validateToken($aud, $token) {
	$tokenParts = explode('.', $token);
	// Check validity
	$signature = hash_hmac('sha256', $tokenParts[0].".".$tokenParts[1], getSecret(), true);
	if ( base64UrlEncode($signature) !== $tokenParts[2] ) {
		return false;
	}
	$payload = json_decode(base64_decode($tokenParts[1]));
	echo "PAYLOAD:".$payload;
	// Check expiration
	if( gettimeofday()['sec'] > $payload->exp ) {
		return false;
	}
	// Check audience
	if( $aud !== $payload->aud ) {
		return false;
	}
	return true;
}

$parts = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
echo "parts[1]: ".$parts[1]."\n";
echo "ValidToken:".validateToken("write:ddns", parts[1])."\n";

?>
