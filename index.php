<!DOCTYPE html> 
<html>
	<head>
		<title>DDNS</title>
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="stylesheet" href="https://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
		<script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script src="https://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
	</head>
	<body>
		<div data-role="page">

	<div data-role="header">
		<h1>DDNS</h1>
	</div><!-- /header -->

	<div data-role="content">	
		<p>Hello world</p>		

<?php
define("DB_USER", "2737ddns");
define("DB_PASSWD", "ddnsdmjnw3tenddns");
define("DB_HOST", "fragrant-tooth.2737ddns.dbinf.buildingtogether.io");
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
	echo "connect db";
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME) or die ('Could not connect to database: ' . mysql_error());
	echo "connected to db";
	$query = "select id, v4address, updated, created from ipaddress where id = (SELECT max(id) FROM ipaddress);";
	if ( mysqli_multi_query($link, $query) ) {
		echo "done query";
		if( $result = mysqli_store_result($link) ) {
			echo "in store result";
			while( $row = mysqli_fetch_row($result) ) {
				echo "in fetch row";
				$last_id = $row[0];
				$last_v4address = $row[1];
				$updated = $row[2];
				$created = $row[3];
			}
			mysqli_free_result($result);
			echo "freed result";
		}
?>
		<div class="ui-grid-a">
			<!--tr><th>DDNS address</th><th>Hostname</th><th>Created</th><th>Updated</th></tr>
			<tr-->
<?php
		echo "<div class='ui-bar-c'>".$last_v4address."</div><div class='ui-bar-b'>".gethostbyaddr($last_v4address)."</div><div class='ui-bar-c'>".$created."</div><div class='ui-bar-b'>".$updated."</div>";
?>
		</div>
<?php
	} else {
		echo "<h1>Geen DDNS info beschikbaar</h1>";
	}
}
?>
	</div><!-- /content -->
</div><!-- /page -->
	</body>
</html>
