<?php
chdir(dirname(__FILE__));
//include '../classes/dbcon.php';

class bulkSSH {
	private $pool;
	private $user;
	private $pass;

	function __construct($user, $pass) {
		$pool = new SSHPool(5, Worker::class);
		$this->pool->submit(new SSHThread("skip", $user, $pass, ""));
		foreach($input as $value) {
			echo "Adding Thread<br>";
			$thread = new SSHThread($value, $user, $pass, $cmd);
			$this->pool->submit($thread);
		}
	}

	function add($ip, $cmd) {
		
	}
	
	function runCmd() {

		$data = $this->pool->process();
		echo "Total Returned: ".sizeof($data).PHP_EOL;

		return $data;
	}
}

class SSHPool extends Pool {
    public $data = [];

    public function process()
    {
        while(count($this->work)) {
            $this->collect(function (SSHThread $job) {
				$job->wait();
                $this->data[] = $job->data;
                return $job->isGarbage();
            });
        }
        $this->shutdown();
        return $this->data;
    }
}

class SSHThread extends Collectable {
	private $ip;
	private $user;
	private $pass;
	private $cmd;
	private $data;
	
	function __construct($ip, $user, $pass, $cmd) {
		echo "Thread Created ".$ip." ".$user." ".$cmd."<br>";
		$this->ip = $ip;
		$this->user = $user;
		$this->pass = $pass;
		$this->cmd = $cmd;
	}
	
	public function run(){
		echo "Thread Executed<br>";
		if($this->cmd != "") {
			"Connecting to ".$this->ip." as ".$this->user." to issue ".$this->cmd."<br>";
			$ssh = new ssh($this->ip, $this->user, $this->pass);
			echo $ssh->connect();
			// $this->data = $ssh->exec($this->cmd);
			unset($ssh);
			$this->synchronized(function () {
				$this->setGarbage();
				$this->notify();
			});
		}
	}
	
	function __destruct() {
		//echo "Destruct".PHP_EOL;
	}
}










$dbcon = new dbcon();
$countQuery = $dbcon->query("select devid, intip from device d join interface i on d.devmgmtint = i.intid where devtype IS NULL LIMIT 1;");
$host = $dbcon->fetch();

function snmpProbe($ip) {
	$communities = array('S4ta*d3xcvpf', 'y2Y9j5Vz8oFHVgN5lbxm');
	foreach($communities as $key => $value) {
		$snmpResult = snmp2_get($ip, $value, "system.sysDescr.0");
		if($snmpResult !== false) return $snmpResult;
	}
	return $snmpResult;
}

$nmapResult = shell_exec('nmap -A '.$host['intip']);

$hostType = "unknown";
if(strpos($nmapResult, "Linux/SUSE")) $hostType = "Linux/SUSE";
else if(strpos($nmapResult, "http-title: Cisco ASDM")) $hostType = "Cisco ASA";
else if(strpos($nmapResult, "Cisco PIX OS 7.X (87%)")) $hostType = "Cisco ASA";
else if(strpos($nmapResult, "OpenSSH 7.2")) $hostType = "Cisco ASA";
else if(strpos($nmapResult, "Running: Cisco embedded, Cisco IOS 12.X")) $hostType = "Cisco IOS 12";
else if(strpos($nmapResult, "OS details: Cisco 2811 router (IOS 12.X)")) $hostType = "Cisco 2811 Router";

$probe = snmpProbe($host['intip']);


if($hostType == "unknown") echo $nmapResult;
else {
	echo "Host Type: ".$hostType."\n";
	echo "CALL SetDevType(".$host['devid'].",'".$hostType."');\n";
	$countQuery = $dbcon->query("CALL SetDevType(".$host['devid'].",'".$hostType."');");
}


?>