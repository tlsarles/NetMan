<?php
chdir(dirname(__FILE__));

include '../classes/dbcon.php';
include '../classes/bulkPing2.php';
include '../classes/syslog.php';
// ini_set("memory_limit", "-1");

$resultsPerQuery = 10;

$time_start = microtime(true);

$dbcon = new dbcon();
$syslogger = new syslogger('172.18.7.212');

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
	
	while($row = $devQuery->fetch_assoc()) {
		$pinger->addPing($row["devid"], $row["intip"], $row["devname"]);
	}
	$pingerResult = $pinger->getResults();
	
	
	// GET RESULTS INTO DB
	foreach($pingerResult as $key => $value) {
		$query = "CALL AddPing(".$key.",".$value[3].",".$value[1].")";
		var_dump($value[1]);
		echo $query.PHP_EOL;
		
		/*
		$changed = $dbcon->query($query);
		
		$changed = $changed->fetch_array(MYSQLI_NUM)[0];

		if($changed == 1) {
			if($up == 0) $up = "Down";
			else if($up == 1) $up = "Up";
			$message = $value[2]." (".$value[0].") Changed to ".$up;
			$syslogger->send($message);
		}
		*/

	}

	$mem = memory_get_usage();
	if($mem > $peakMem) $peakMem = $mem;
	echo $mem.PHP_EOL;
}

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Pinger Time:".$time.PHP_EOL;
//$logFile = fopen("/var/www/html/cronjobs/ping.log", "a");
//fwrite($logFile, date("Y-m-d H:i:s")." Time:".$time." Peak Mem Use:".$peakMem.PHP_EOL);
//fclose($logFile);
?>