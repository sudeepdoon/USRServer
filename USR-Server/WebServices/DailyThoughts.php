<?php

Class DailyThoughts {
	
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

	public function getAllDailyThoughts(){
		if(! $this->con){
			$this->connectDatabase();
		}

		$SQL = "SELECT TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL FROM TFTD_INDEX";
		$result = mysql_query($SQL) or die(mysql_error());

		$returnArray = array();

		if ($result) {
			
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				$rowArray["year"] = $row['TFTD_YEAR'];
				$rowArray["month"] = $row['TFTD_MONTH'];
				$rowArray["date"] = $row['TFTD_DATE'];
				$rowArray["title"] = str_replace("&", "and", $row['TFTD_TITLE']);
				$rowArray["url"] = $row['TFTD_URL'];
				$returnArray[] = $rowArray;
    		}
		mysql_free_result($result);    	
		}

		return $returnArray; 
	}
}

?>