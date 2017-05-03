<?php
include 'ping.php';
dl('pthreads.so');

class bulkPing {

	function __construct() {

	}

	function runPing($input) {
		$pool = new PingPool(4);
		// Add a duplicate of the first thread
		// For some reason, the first thread is never returned
		//$pool->submit(new pingThread($input[0][0], $input[0][1], $input[0][2]));
		foreach ($input as $i => $value) {
			echo "Input : ".$value[0]." - ".$value[1].PHP_EOL;
			$pinger = new pingThread($value[0], $value[1], $value[2]);
			$pool->submit($pinger);
		}
		echo "TEST".PHP_EOL;
		$data = $pool->process();
		echo "Total Returned: ".sizeof($data).PHP_EOL;

		return $data;
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
                $this->data[$job->dev] = array($job->host,$job->pingResult,$job->name);
                return $job->isGarbage();
            });
        }
        $this->shutdown();
        return $this->data;
    }
}

class pingThread extends Collectable {
	private $pinger;
	private $host;
	private $dev;
	private $name;
	private $pingResult;
	
	function __construct($dev, $host, $name) {
		$this->pinger = new ping();
		$this->dev = $dev;
		$this->host = $host;
		$this->name = $name;
		echo "Created ".$this->name.PHP_EOL;
	}
	
	public function run(){
		$this->synchronized(function () {
			$this->pingResult = $this->pinger->ping($this->host);
			$this->setGarbage();
			$this->notify();
		});
		echo "DEV:".$this->name." - ".$this->host." - ".$this->pingResult.PHP_EOL;
		unset($this->pinger);
	}
}
?>