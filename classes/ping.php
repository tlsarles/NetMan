<?php
class ping {
	private $pinger;
	function __construct() {
		require_once "Net/Ping.php";
		$this->pinger = Net_Ping::factory();
		if (PEAR::isError($this->pinger)) {
			throw new Exception($ping->getMessage());
		}
		$this->pinger->setArgs(array('count' => 1,'timeout' => 2));
	}
	
	function ping($host) {
		$result = $this->pinger->ping($host);
		$output = false;
		if(isset($result->_icmp_sequence[1]))
			$output = $result->_icmp_sequence[1];
		return $output;
	}
}
?>