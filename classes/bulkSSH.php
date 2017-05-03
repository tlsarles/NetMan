<?php
chdir(dirname(__FILE__));
include 'ssh.php';

class bulkSSH {
	private $pool;
	private $user;
	private $pass;

	function __construct($user, $pass, $input) {
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
?>