<?php
require_once("DailyThought.php");

class DatabaseServices {
	
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "USR";
	private $con;

	function connectDatabase(){
		$this->con = mysql_connect($this->servername, $this->username, $this->password) or
		die("Could not connect: " . mysql_error());
		
		mysql_select_db($this->dbname);
	}
	
	function clearStaging(){
		$SQL = "DELETE FROM TFTD_INDEX_STAGING";
		mysql_query($SQL) or die(mysql_error());
	}
	
	function createStaging($dailyThougts){
		$this->clearStaging();
		
		foreach ($dailyThougts as $key=>$dailyThought){
			$SQL = "INSERT INTO TFTD_INDEX_STAGING (TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL) VALUES (".
				$dailyThought->getYear().",".$dailyThought->getMonth().",".$dailyThought->getDate().",'".
				str_replace("'", "''", $dailyThought->getTitle())."','".$dailyThought->getURL()."')";
			
			mysql_query($SQL) or die(mysql_error());
		}
	}
	
	public function syncDailyThoughts($dailyThougts){
		$this->connectDatabase();
		
		$this->clearStaging();
		$this->createStaging($dailyThougts);
	}

}
?>