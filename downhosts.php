<?php
include 'classes/dbcon.php';
include 'classes/bootstrap.php';

$html = new html('Device Status');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

$dbcon = new dbcon();

$dbcon->query("CALL DownHosts();");
$query = $dbcon->toArray();

$gridBuilder = new gridRowBuilder(2);
$tableDown = new table();
$tableRecent = new table();

$tableDown->addRow(array("Device Name", "IP Address", "Uptime Percent<br>(Last Hour)"));
$tableRecent->addRow(array("Device Name", "IP Address", "Uptime Percent<br>(Last Hour)"));

foreach($query as $value) {
	if($value['t_upnow'] == 0) {
		$tableDown->addRow(array($value['t_devname'], $value['t_ip'], $value['t_percent']));
	} else {
		$tableRecent->addRow(array($value['t_devname'], $value['t_ip'], $value['t_percent']));
	}
}

$panelDown = new panel("Down Now", $tableDown->toHTML(), "danger");
$panelRecent = new panel("Recently Down", $tableRecent->toHTML(), "warning");

$gridBuilder->addCol($panelDown->toHTML());
$gridBuilder->addCol($panelRecent->toHTML());

$html->appendBody($gridBuilder->toHTML());
echo $html->toHTML();
?>