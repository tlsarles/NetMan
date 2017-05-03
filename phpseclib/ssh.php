<?php
include('Net/SSH2.php');

class SSH {
	private $session;
	
	function __construct($host, $user, $pass) {
		$this->session = new Net_SSH2($host);
		if (!$this->session->login($user, $pass)) {
			throw new Exception('Login Failed');
		}
	}

	function exec($cmd) {
		return $this->session->exec($cmd);
	}
}
/*
$session = new Net_SSH2('172.16.18.251');
$session->login('helpdesk', 'R33d0wnlEy');
echo $session->exec('show ver');
*/
?>