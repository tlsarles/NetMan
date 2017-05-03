<?php
include 'classes/dbcon.php';
include 'classes/bulkSSH.php';

$dbcon = new dbcon();

$dbcon->query("select d.devname, i.intip, s.subnetip from device d join interface i on d.devmgmtint = i.intid join location l on l.locid = devlocation join subnet s on s.subnetid = l.locsubnet where devname like '%fw%' AND devname not like '%ifw%' LIMIT 3;");
$result = $dbcon->toArray();

$ssher = new bulkSSH('misals','Wflrul1!');
$toProcess = array();
foreach($result as $key => $value) {
	if($key > 0 && $key <= 1) {
		echo "Pushing Thread ".$key."<br>";
		$ssher->add();
		$toProcess[] = array($value['intip'], 'show ver');
	}
}

echo "Running Threads...<br>";
//echo nl2br($ssher->runCmd());
?>