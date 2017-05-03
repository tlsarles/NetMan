<?php
$mysqli = new mysqli("localhost", "NetMan", "qpalzm1!", "netman");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$myfile = fopen("bb-db.log", "r") or die("Unable to open file!");
$duplicates = 0;
$pdups = 0;
$mydb = array();
$output = "";
$location = "Default";
while(!feof($myfile)) {
	$line = fgets($myfile);
	// Look for headers
	$valid = preg_match('/page/', $line, $ip);
	if($valid) {
		$line = substr($line, strlen($ip[0]));
		$line = str_replace('#', '', $line);
		$location = trim($line);
		$output .= "</table><h3>".$location."</h3><table>";
	} else {
		// If not a header, match an IP Address
		$valid = preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $line, $ip);
		if($valid) {
			$ip = $ip[0];
			$len = strlen($ip);
			$line = substr($line, $len);
			$line = str_replace('#', '', $line);
			$line = trim($line);
			
			// Then Match a Hostname
			$valid = preg_match('/\S+marsh.net/', $line, $hostname);
			if($valid) $hostname = $hostname[0];
			else $hostname = $line;
			
			$output .=  "CALL AddHost('".$ip."', '".$hostname."', '".$location."');<br>";
			//$mysqli->query("CALL AddHost('".$ip."', '".$hostname."', '".$location."')");
			if(array_key_exists($ip, $mydb)) {
				$duplicates++;
				if(array_key_exists($location, $mydb[$ip])) {
					if($mydb[$ip][$location] == $hostname)
						$pdups++;
				}
			}
			
			$mydb[$ip[0]][$location] = $hostname;
			
		}
	}
}

echo "Duplicates/P : " . $duplicates . " / " . $pdups;
echo $output . "";
fclose($myfile);
?>