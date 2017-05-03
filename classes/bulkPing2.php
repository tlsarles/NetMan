<?php
include 'ping.php';

class bulkPing {
	private $pool;
	
	function __construct() {
		$this->pool = new PingPool(50, Worker::class);
	}
	
	function addPing($dev, $host, $name) {
		$pinger = new pingThread($dev, $host, $name);
		$this->pool->submit($pinger);
	}
	
	function getResults() {
		return $this->pool->process();
	}
}

class PingPool extends Pool
{
    public $data = [];

    public function process()
    {
        while(count($this->work)) {
            $this->collect(function (pingThread $job) {
				$job->wait();
                $this->data[$job->dev] = array($job->host,$job->pingResult,$job->name, $job->up);
				echo $job->dev.": ".$job->up." ".$job->pingResult.PHP_EOL;
                return $job->isGarbage();
            });
        }
        $this->shutdown();
        return $this->data;
    }
}

class pingThread extends Collectable{
	private $pinger;
	private $host;
	private $dev;
	private $name;
	private $changed;
	private $up;
	
	function __construct($dev, $host, $name) {
		$this->pinger = new ping();
		$this->dev = $dev;
		$this->host = $host;
		$this->name = $name;
	}
	
	public function run(){
		$this->synchronized(function () {
			$this->pingResult = $this->pinger->ping($this->host);
			unset($this->pinger);
			$this->up = 1;
			if($this->pingResult === false) {
				$this->up = 0;
				$this->pingResult = 'null';
			}
			$this->setGarbage();
			$this->notify();
		});
		echo $this->dev.": ".$this->up." ".$this->pingResult.PHP_EOL;

	}
}
?>