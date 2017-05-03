<?php
chdir(dirname(__FILE__));
include '../classes/dbcon.php';
include '../classes/bulkPing.php';
include '../classes/syslog.php';

// ini_set("memory_limit", "-1");

function sendAlert($messages) {
	$mailBody = Array();
	foreach($messages as $message) {
		// Syslog
		$syslogger = new syslogger('172.18.7.212');
		$syslogger->send($message);
		$mailBody[] .= $message."\n";
	}
	// E-Mail
	$to      = 'asarles@marsh.net';
	$subject = 'NetMan Test';
	$headers = 'From: NetMan@marsh.net' . "\r\n" .
		'Reply-To: ' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $mailBody, $headers);
}

$resultsPerQuery = 10;

$time_start = microtime(true);

$dbcon = new dbcon();

$countQuery = $dbcon->query("select count(*) from device;");
$countQuery = $countQuery->fetch_assoc()["count(*)"];
$countQuery = 10;
$offset = 0;
$pingData = array();
$peakMem = 0;
while($offset < $countQuery) {
	$thisQuery = "select device.devid, device.devname, interface.intip from device join interface on device.devid = interface.intdev LIMIT ".$offset.",".$resultsPerQuery.";";
	$devQuery = $dbcon->query($thisQuery);

	$offset += $resultsPerQuery;
	
	$pinger = new bulkPing();
	if(isset($devList)) unset($devList);
	while($row = $devQuery->fetch_assoc()) {	
		$devList[] = array($row["devid"], $row["intip"], $row["devname"]);
	}
	$pingerResult = $pinger->runPing($devList);
	
	// GET RESULTS INTO DB
	$messages = Array();
	foreach($pingerResult as $key => $value) {
		if($value[1] !== false) {
			$up = true;
			$pingms = $value[1];
		} else {
			$up = 0;
			$pingms = 'null';
		}
		
		$changed = $dbcon->query("CALL AddPing(".$key.",".$up.",".$pingms.")");
		
		$changed = $changed->fetch_array(MYSQLI_NUM)[0];

		if($changed == 1) {
			if($up == 0) $up = "Down";
			else if($up == 1) $up = "Up";
			$messages[] = $value[2]." (".$value[0].") Changed to ".$up;
		}
	}
	$mem = memory_get_usage();
	if($mem > $peakMem) $peakMem = $mem;
	echo $mem.PHP_EOL;
}

// Benchmarking
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Pinger Time:".$time.PHP_EOL;
$logFile = fopen("/var/www/html/cronjobs/ping.log", "a");
fwrite($logFile, date("Y-m-d H:i:s")." Time:".$time." Peak Mem Use:".$peakMem.PHP_EOL);
fclose($logFile);

/*
// Send Alerts
if(count($messages) > 0) {
	sendAlert($messages);
}
*/
?>