<?php

class Schedule{
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "USR";
	private $con;
	
	function connectDatabase(){
		$this->con = mysql_connect($this->servername, $this->username, $this->password) or  die("Could not connect: " . mysql_error());
		mysql_select_db($this->dbname);
	}
	
	function __destruct() {
		mysql_close($this->con);
	}
	
	public function getSchedule(){
		if(! $this->con){
			$this->connectDatabase();
		}
		
		$SQL = "SELECT S.S_DATE, S.TITLE FROM SCHEDULE S ORDER BY S.S_DATE ASC";
		$result = mysql_query($SQL) or die(mysql_error());
		
		$returnArray = array();
		
		if ($result) {
				
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				$rowArray['Date'] = $row['S_DATE'];
				$rowArray['Title'] = $row['TITLE'];
				
				$returnArray[] = $rowArray;
			}
		}
		
		mysql_free_result($result);
		
		return $returnArray;
	}
}

?>