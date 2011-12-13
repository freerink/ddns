<?php
define("DB_USER", "2737ddns");
define("DB_PASSWD", "dmjnw3ten");
define("DB_HOST", "sql1.pcextreme.nl");
define("DB_NAME", "2737ddns");

if( isset($_GET['time']) && isset($_GET['hash']) ) {
	$tijd = $_GET['time'];
	$hash = $_GET['hash'];
	$addr = $_SERVER['REMOTE_ADDR'];
	$secret = "Frank Is Gek?";
	$server_hash = hash("sha256", $secret." ".$tijd);
// $msgtext = "Remote address: ".$addr."\nclient time: ".base64_decode($tijd)."\nclient hash: ".$hash."\nserver hash: ".$server_hash."\n";
	if ( $hash == $server_hash ) {
		// echo $msgtext;
		// Store the remote address in the database
		// Init DB connection
		mysql_connect(DB_HOST, DB_USER, DB_PASSWD) or die ('Could not connect: ' . mysql_error());
		mysql_select_db(DB_NAME) or die('Could not select database: ' . mysql_error());
		// Get last entry
		$query = "select id, v4address from ipaddress where id = (SELECT max(id) FROM ipaddress)";
		$result = mysql_query($query);
		$last_v4address = "";
		$last_id = -1;
		if ( $result ) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			mysql_free_result($result);
			$last_id = $row['id'];
			$last_v4address = $row['v4address'];
		}
		if( $last_v4address == $addr && $last_id != -1 ) {
			echo "Update last remote address record: ".$last_v4address."\n";
			$query = "update ipaddress set updated = CURRENT_TIMESTAMP where id = ".$last_id;
			$result = mysql_query($query);
			if ( $result ) {
				echo "Updated remote address: ".$addr."\n";
			} else {
				echo "Error updating remote address with id: ".$last_id."\n";
			}
		} else {	
			// Insert new ipaddress
			$query = sprintf("INSERT INTO ipaddress (v4address) values ('%s')", mysql_real_escape_string($addr));
			$result = mysql_query($query);
			if ( $result ) {
				echo "Added remote address: ".$addr."\n";
			} else {
				echo "Error adding remote address: ".$addr."\n";
			}
		}
	} else {
		echo "Not authorised\n";
	}
} else {
	echo "Hallo\n";
}
?>
