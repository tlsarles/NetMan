<?php
class dbcon {
	private $conn;
	private $result;
	
	function __construct() {
		$this->conn = new mysqli("localhost", "NetMan", "qpalzm1!", "netman");
		if ($this->conn->connect_errno) {
			echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
	}
	
	function nocommit() {
		$this->conn->autocommit(FALSE);
	}
	function commit() {
		$this->conn->commit();
	}
	function rollback() {
		$this->conn->rollback();
	}
	
	function query($query) {
		while($this->conn->more_results())
		{
			$this->conn->next_result();
			if($res = $this->conn->store_result())
			{
				$res->free(); 
			}
		}
		$this->result = $this->conn->query($query);
		if(!$this->result) {
			echo("Error description: " . mysqli_error($this->conn));
		} else {
			return $this->result;
		}
	}

	function count() {
		return mysqli_num_rows($this->result);
	}
	
	function toArray() {
		$output = array();
		while($row = $this->result->fetch_assoc()) {
			$output[] = $row;
		}
		return $output;	
	}
	
	function toJSON() {
		return json_encode($this->toArray());
	}
	
	function fetch() {
		return $this->result->fetch_assoc();
	}
	
	function toString() {
		$output = "";
		while($row = $this->result->fetch_assoc()) {
			$output .= $this->toStringExt($row) . "<br>";
		}
		return $output;
	}
	function toStringExt($input) {
		$output = "";
		foreach ($input as $key => $value) {
			$output .= $key . ": " . $value . ", ";
		}
		return $output;
	}
	function getSingleValue() {
		$row = $this->result->fetch_array(MYSQLI_NUM);
		return $row[0];
	}
}
?>