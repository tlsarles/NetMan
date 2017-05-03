<?php
chdir(dirname(__FILE__));
include '../classes/dbcon.php';
include '../classes/syslog.php';

$dbcon = new dbcon();
$dbcon->query("select d.devname, d.devup, i.intip from device d join interface i on d.devmgmtint = i.intid;");
$result = $dbcon->toArray();
$syslogger = new syslogger('172.18.7.212');
foreach($result as $value) {
	if($value['devup'] == 0) $up = "Down";
	else if($value['devup'] == 1) $up = "Up";
	$message = $value['devname']." (".$value['intip'].") Changed to ".$up.PHP_EOL;
	$syslogger->send($message);
	echo $message;
}
?>