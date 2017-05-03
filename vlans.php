<?php
include 'classes/main.php';

$html = new html('VLANs');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

$dbcon = new dbcon();

// Build Subnet Table
$dbcon->query("select * from subnet s left join vlan v on v.vlanid = s.vlanid left join location l on s.subnetid = l.locsubnet;");
$query = $dbcon->toArray();
	
$gridBuilder = new gridRowBuilder(2);
$subnetTable = new table();

foreach($query as $value) {
	$thisRow = array($value['subnetip']." /".$value['subnetmask'], $value['locname'], $value['vlanname']);
	
	if($_SESSION['privilege'] == "admin") {
		$thisRow[] = '<span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="deleteSubnet(\''.$value['subnetip'].'\','.$value['subnetid'].')"></span>';
		$thisRow[2] .= ' <span class="glyphicon glyphicon-pencil" aria-hidden="true" data-toggle="modal" onclick="vlanEditor('.$value['subnetid'].')" data-target="#subnetUpdate"></span>';
	}
	$subnetTable->addRow($thisRow);
	
	/*
	$subnet = ip2long($value['subnetip']);
    $mask = -1 << (32 - $value['subnetmask']);
    $subnet &= $mask;
	*/
}
// Add Subnet Form
$subnetOutput = '';
if($_SESSION['privilege'] == "admin") {
	$subnetOutput .= '<form class="form-inline"><input type="text" id="addSubnetNW" placeholder="Network Address" class="form-control"> '.
						'<span style="vertical-align:middle;font-size:18pt;">/</span><input type="text" id="addSubnetCIDR" placeholder="CIDR" class="form-control" size="4"> '.
						'<button type="button" onclick="addSubnet()" class="btn btn-primary">Add Subnet</button></form><br>';
}

// Build VLAN Table
$dbcon->query("select vlan.vlanid, vlan.vlanname, subnet.subnetip, subnet.subnetmask from vlan left join subnet on vlan.vlanid = subnet.vlanid;");
$query = $dbcon->toArray();
// Eliminate Duplicates
$vlanDB = array();
foreach($query as $value) {
	if(isset($vlanDB[$value['vlanid']])) {
		$vlanDB[$value['vlanid']]['subnet'] = "Multiple";
	} else {
		$vlanDB[$value['vlanid']] = array('vlanid'=>$value['vlanid'],'vlanname'=>$value['vlanname'],'subnet'=>$value['subnetip'].' /'.$value['subnetmask']);
	}
}
$VLANtable = new table();
foreach($vlanDB as $value) {
	$VLANtable->addRow(array($value['vlanid'],$value['vlanname'], $value['subnet']));
}

// Add VLAN Form
$VLANoutput = "";
if($_SESSION['privilege'] == "admin") {
	$VLANoutput .= '<form class="form-inline"><input type="text" id="addVLANNum" placeholder="VLAN #" class="form-control" size="4"> <input type="text" id="addVLANName" placeholder="VLAN Name" class="form-control"> <button type="button" onclick="addVLAN()" class="btn btn-primary">Add VLAN</button></form><br>';
}

$subnetOutput .= $subnetTable->toHTML();
$SubnetPanel = new panel("Subnets", $subnetOutput);
$VLANoutput .= $VLANtable->toHTML();
$VLANpanel = new panel("VLANs", $VLANoutput);

$gridBuilder->addCol($SubnetPanel->toHTML());
$gridBuilder->addCol($VLANpanel->toHTML());

$html->appendBody($gridBuilder->toHTML());
$html->appendBody('<script src="js/vlan.js"></script>');
$modal = new modal('subnetUpdate');
$html->appendBody($modal->toHTML());
echo $html->toHTML();
?>
