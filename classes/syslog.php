<?php
class syslogger {
	private $destination;

	function __construct($dest) {
		$this->destination = $dest;
	}
	
	function send($message, $program = "UpDown") {
	  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	  $syslog_message = "<22>" . date('M d H:i:s ') . $program . ': ' . $message;
	  socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $this->destination, 514);
	  socket_close($sock);
	}
}
?>