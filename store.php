<?php
include 'classes/main.php';

$html = new html('Device Status');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

if(isset($_GET['location'])) {
	$dbcon = new dbcon();
	$dbcon->query("SELECT l.locname, d.devid, d.devname, i.intip FROM location l JOIN device d on l.locid = d.devlocation JOIN interface i on d.devmgmtint = i.intid WHERE l.locid = ".$_GET['location'].";");
	if($dbcon->count() == 0) {
		$html->appendBody('<script>var storeData = "";</script>');
	} else {
		$result = $dbcon->toArray();
		$json = json_encode($result);
		$html->appendBody('<script>var storeData = '.$json.';</script>');
	}
	$gridBuilder = new gridRowBuilder(1);
	if(isset($result[0]['locname']))
		$output = '<h3>'.$result[0]['locname'].'</h3>';
	else
		$output = '<h3>No Devices For This Location</h3>';
	$output .= '<div id="storeDiv"> </div>';
	$gridBuilder->addCol($output);
	$html->appendBody($gridBuilder->toHTML());
	$html->appendBody('<script>var thisLocation = '.$_GET['location'].';</script>');
	$html->appendBody('<script src="js/store.js"></script>');
}
echo $html->toHTML();
?>