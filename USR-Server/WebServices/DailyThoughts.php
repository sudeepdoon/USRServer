<?php

Class DailyThoughts {
	
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "USR";
	private $con;
	
	private $monthNames = array("1"=>"January", "2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");

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

		$SQL = "SELECT TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL FROM TFTD_INDEX ORDER BY TFTD_YEAR DESC, TFTD_MONTH DESC, TFTD_DATE DESC";
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
		}
		
		mysql_free_result($result);
		
		return $returnArray; 
	}
	
	public function getAllYears(){
		if(! $this->con){
			$this->connectDatabase();
		}
		
		$SQL = "SELECT DISTINCT TFTD_YEAR FROM TFTD_INDEX ORDER BY TFTD_YEAR DESC";
		$result = mysql_query($SQL) or die(mysql_error());
		
		$returnArray = array();
		
		if ($result) {
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				$rowArray["id"] = $row['TFTD_YEAR'];
				$rowArray["name"] = $row['TFTD_YEAR'];
				$returnArray[] = $rowArray;
			}
		}
		mysql_free_result($result);
		
		return $returnArray;
	}
	
	public function getAllMonths($year){
		if(! $this->con){
			$this->connectDatabase();
		}
	
		$SQL = "SELECT DISTINCT TFTD_MONTH FROM TFTD_INDEX WHERE TFTD_YEAR = ".$year." ORDER BY TFTD_MONTH DESC";
		
		$result = mysql_query($SQL) or die(mysql_error());
	
		$returnArray = array();
	
		if ($result) {
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				$rowArray["id"] = $row['TFTD_MONTH'];
				$rowArray["name"] = $this->monthNames[$row['TFTD_MONTH']];
				$returnArray[] = $rowArray;
			}
		}
		mysql_free_result($result);
	
		return $returnArray;
	}
	
	public function getDailyThoughts($year, $month){
		if(! $this->con){
			$this->connectDatabase();
		}
	
		$SQL = "SELECT TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL FROM TFTD_INDEX WHERE TFTD_YEAR = ".$year." AND TFTD_MONTH = ".$month." ORDER BY TFTD_DATE DESC";
		$result = mysql_query($SQL) or die(mysql_error());
	
		$returnArray = array();
	
		if ($result) {
				
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				$rowArray["year"] = $row['TFTD_YEAR'];
				$rowArray["monthID"] = $row['TFTD_MONTH'];
				$rowArray["month"] = $this->monthNames[$row['TFTD_MONTH']];
				$rowArray["date"] = $row['TFTD_DATE'];
				$rowArray["title"] = str_replace("&", "and", $row['TFTD_TITLE']);
				$rowArray["url"] = $row['TFTD_URL'];
				$returnArray[] = $rowArray;
			}
		}
	
		mysql_free_result($result);
	
		return $returnArray;
	}
}

?>