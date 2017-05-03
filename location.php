<?php
include 'classes/main.php';

$html = new html('Locations','container');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

$dbcon = new dbcon();
$dbcon->query("select locid, count, locname, subnetip, subnetmask from (select l.locid, COUNT(d.devid) as count, l.locsubnet, l.locname from device d right outer join location l on d.devlocation = l.locid group by l.locid) AS q1 left outer join subnet s on q1.locsubnet = s.subnetid;");
$result = $dbcon->toArray();

$gridBuilder = new gridRowBuilder(1);

$output = "";
if($_SESSION['privilege'] == "admin") {
	$newLoc = '<form class="form-inline"><input type="text" id="addLocationTextInput" placeholder="New Location Name" class="form-control"> <button type="button" onclick="addLocation()" class="btn btn-primary">Add Group</button></form>';
	$panel = new panel("Add New Location", $newLoc);
	$output .= $panel->toHTML();
}

$output .= '<h3>Locations</h3>';

$table = new table();
$newRow = array("Location","Network","Number of Devices");
if($_SESSION['privilege'] == "admin") {
	$newRow[] = 'Actions';
}
$table->headers($newRow);
foreach($result as $key => $value) {
	$newRow = array($value['locname'],$value['subnetip'].' /'.$value['subnetmask'], $value['count']);
	if($_SESSION['privilege'] == "admin") {
		$newRow[] = '<span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="deleteLocation(\''.$value['locname'].'\','.$value['locid'].')"></span>';
	}
	$table->addRow($newRow);
}
$output .= $table->toHTML();


$gridBuilder->addCol($output);
$html->appendBody($gridBuilder->toHTML());
$html->appendBody('<script src="js/location.js"></script>');
echo $html->toHTML();
?>