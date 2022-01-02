<?php
require 'validate.php';

function myLogRequest() {
	$myfile=fopen("update.log", "a") or die("Unable to open file");
	fwrite($myfile, "Headers:" . print_r($_SERVER, true) . "\n") or die("fwrite error");
	fwrite($myfile, "Request:" . print_r($_REQUEST, true) . "\n") or die("fwrite error");
	fclose($myfile);
}

myLogRequest();

if( ! isset($_SERVER['HTTP_AUTHORIZATION']) ) {
	http_response_code(403);
} else {
	$authParts = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
	
	$statusList = json_encode([
			["validtoken" => validateToken("write:ddns", $authParts[1])],
			["timestamp" => 2132131, "status" => "OK"],
			["timestamp" => 2132132, "status" => "OK"],
			["timestamp" => 2132133, "status" => "OK"]
		], JSON_PRETTY_PRINT);
	echo $statusList;
}
?>
