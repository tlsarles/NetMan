<?php 
class ssh {
    private $ssh_host; 
    private $ssh_port = 22; 
    private $ssh_auth_user;
    private $ssh_auth_pass;
    private $connection; 
	private $shell;
    
	function __construct($host, $user, $pass) {
		$this->ssh_host = $host;
		$this->ssh_auth_user = $user;
		$this->ssh_auth_pass = $pass;
	}
	
    public function connect() { 
		$output = "";
        if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port))) { 
            throw new Exception('Cannot connect to server'); 
        } 

        if (!ssh2_auth_password($this->connection, $this->ssh_auth_user, $this->ssh_auth_pass) ) { 
            throw new Exception('Autentication rejected by server'); 
        }
		
		if (!($this->shell = ssh2_shell($this->connection, 'vt102', null, 100, 100, SSH2_TERM_UNIT_CHARS)) ) {
			throw new Exception('Failed to get shell'); 
		}
		$output .= $this->exec('en');
		$output .= $this->exec($this->ssh_auth_pass);
		$output .= $this->exec('terminal length 0');
		$output .= $this->exec('terminal pager 0');
		return $output;
    } 
    public function exec($cmd) {
        fwrite($this->shell, $cmd . PHP_EOL);
        $data = "";
		sleep(1);
		//stream_set_blocking($this->shell, true);
		while($line = @fgets($this->shell)) {
			$data .= $line;
		}
        return $data;
    }
    public function disconnect() {
        //$this->exec('exit'); 
        $this->connection = null; 
    }
    public function __destruct() { 
        $this->disconnect(); 
    }
}
?> 