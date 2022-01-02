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
	//echo "In validateToken token:".$token."\n";
	$tokenParts = explode('.', $token);
	//echo "Count:".count($tokenParts)."\n";
	//print_r($tokenParts);
	// Check validity
	$signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], getSecret(), true);
	$base64UrlSignature = base64UrlEncode($signature);
	//echo "Received signature   : " . $tokenParts[2] . "\n";
	//echo "Verify with signature: " . $base64UrlSignature . "\n";
	if ( $base64UrlSignature !== $tokenParts[2] ) {
		echo "Invalid signature\n";
		return false;
	}
	//echo "Valid signature\n";
	$payloadJson = base64_decode($tokenParts[1]);
	//echo "JSON payload: ".$payloadJson."\n";
	$payload = json_decode($payloadJson, true);
	//echo "Payload: ".print_r($payload, true)."\n";
	// Check expiration
	$now = gettimeofday()['sec'];
	//echo "now: ".$now.", exp: ".$payload['exp']."\n";
	if( $now > $payload['exp'] ) {
		echo "Expired\n";
		return false;
	}
	// Check audience
	if( $aud !== $payload['aud'] ) {
		echo "Incorrect audience\n";
		return false;
	}
	return true;
}

?>
