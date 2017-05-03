<?php
session_start();
include 'dbcon.php';

class Query {
	private $dbcon;
	
	function __construct() {
		$this->dbcon = new dbcon();
	}
	
	function getStoreList() {
		$this->dbcon->query("select locid, locname from location;");
		return $this->dbcon->toJSON();
	}
	
	function getDeviceHistory($devid) {
		$this->dbcon->query("select * from pingresult where pingdev=".$devid." ORDER BY pingtime DESC;");
		return $this->dbcon->toJSON();
	}
	
	function getPrivilege() {
		if(isset($_SESSION['privilege'])) $output = $_SESSION['privilege'];
		else $output = "none";
		return $output;
	}
	
	function privilegedQuery($query) {
		if(isset($_SESSION['privilege'])) {
			if($_SESSION['privilege'] == "admin") {
				$this->dbcon->query($query);
				return $query;
			} else {
				return "Not an Admin";
			}
		} else {
			return "Privilege Not Set";
		}
	}
	
	function selectJSON($query) {
		$this->dbcon->query($query);
		return $this->dbcon->toJSON();
	}
	
	function addDevice($ip, $hostname, $location) {
		return $this->privilegedQuery("CALL AddHost('".$ip."', '".$hostname."', '".$location."');");
	}
	
	function deleteDevice($deviceID) {
		return $this->privilegedQuery("CALL DeleteHost(".$deviceID.");");
	}
	
	function addLocation($location) {
		return $this->privilegedQuery("insert into location (locname) VALUES ('".$location."');");
	}
}
$query = new Query();
if(isset($_POST['getDeviceHistory'])) {
	echo $query->getDeviceHistory($_POST['devid']);
} else if(isset($_POST['getStoreList'])) {
	echo $query->getStoreList();
} else if(isset($_POST['getPrivilege'])) {
	echo $query->getPrivilege();
} else if(isset($_POST['deleteDevice'])) {
	echo $query->deleteDevice($_POST['devid']);
} else if(isset($_POST['addLocation'])) {
	echo $query->addLocation($_POST['addLocation']);
} else if(isset($_POST['deleteLocation'])) {
	echo $query->privilegedQuery("CALL DeleteLocation(".$_POST['deleteLocation'].");");
} else if(isset($_POST['addDevice'])) {
	echo $query->addDevice($_POST['ip'],$_POST['hostname'],$_POST['location']);
} else if(isset($_POST['addVLAN'])) {
	echo $query->privilegedQuery("insert into vlan (vlanid, vlanname) VALUES (".$_POST['addVLAN'].",'".$_POST['vlanName']."')");
} else if(isset($_POST['addSubnet'])) {
	echo $query->privilegedQuery("insert into subnet (subnetip, subnetmask, vlanid) VALUES ('".$_POST['addSubnet']."','".$_POST['subnetCIDR']."', 0)");
} else if(isset($_POST['getSubnet'])) {
	echo $query->selectJSON("select * from subnet s left join vlan v on v.vlanid = s.vlanid left join location l on s.subnetid = l.locsubnet WHERE s.subnetid = ".$_POST['getSubnet'].";");
} else if(isset($_POST['getVLANs'])) {
	echo $query->selectJSON("select * from vlan;");
} else if(isset($_POST['updateNetVLAN'])) {
	echo $query->privilegedQuery("update subnet set vlanid = ".$_POST['vlan']." WHERE subnetid = ".$_POST['updateNetVLAN'].";");
} else if(isset($_POST['deleteSubnet'])) {
	echo $query->privilegedQuery("delete from subnet where subnetid = ".$_POST['deleteSubnet'].";");
}
?>