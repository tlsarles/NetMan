<?php
chdir(dirname(__FILE__));
include '../classes/dbcon.php';

$dbcon = new dbcon();

$continue = true;
while($continue) {
	$countQuery = $dbcon->query("select devid, intip from device d join interface i on d.devmgmtint = i.intid where devtype IS NULL LIMIT 1;");
	$host = $dbcon->fetch();

	$nmapResult = shell_exec('nmap -A '.$host['intip']);

	$hostType = "unknown";
	if(strpos($nmapResult, "Linux/SUSE")) $hostType = "Linux/SUSE";
	else if(strpos($nmapResult, "http-title: Cisco ASDM")) $hostType = "Cisco ASA";
	else if(strpos($nmapResult, "Cisco PIX OS 7.X (87%)")) $hostType = "Cisco ASA";
	else if(strpos($nmapResult, "OpenSSH 7.2")) $hostType = "Server";
	else if(strpos($nmapResult, "Running: Cisco embedded, Cisco IOS 12.X")) $hostType = "Cisco IOS 12";
	else if(strpos($nmapResult, "OS details: Cisco 2811 router (IOS 12.X)")) $hostType = "Cisco 2811 Router";
	else if(strpos($nmapResult, "Too many fingerprints match this host to give specific OS details")) $hostType = "Unknown";

	if($hostType == "unknown") {
		echo $nmapResult;
		$continue = false;
	}
	else {
		echo "Host Type: ".$hostType."\n";
		echo "CALL SetDevType(".$host['devid'].",'".$hostType."');\n";
		$countQuery = $dbcon->query("CALL SetDevType(".$host['devid'].",'".$hostType."');");
	}
}
?>