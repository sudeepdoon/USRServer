<?php
require_once("DailyThought.php");

class DatabaseServices {
	
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "USR";
	private $con;

	public function connectDatabase(){
		$this->con = mysql_connect($this->servername, $this->username, $this->password) or
		die("Could not connect: " . mysql_error());
		
		mysql_select_db($this->dbname);
	}
	
	public function closeDatabase(){
		mysql_close($this->con);
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
	
	function insertNewDailyThoughts(){
		$SQL = "INSERT INTO TFTD_INDEX (TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL) ".
				"SELECT TIS.TFTD_YEAR as TFTD_YEAR, TIS.TFTD_MONTH as TFTD_MONTH, TIS.TFTD_DATE as TFTD_DATE, TIS.TFTD_TITLE as TFTD_TITLE, TIS.TFTD_URL as TFTD_URL ".
				"FROM TFTD_INDEX_STAGING TIS LEFT OUTER JOIN TFTD_INDEX TI ".
				"ON TIS.TFTD_YEAR = TI.TFTD_YEAR AND ".
					"TIS.TFTD_MONTH = TI.TFTD_MONTH AND ".
					"TIS.TFTD_DATE = TI.TFTD_DATE ".
				"WHERE (TI.TFTD_YEAR IS NULL AND ".
					"TI.TFTD_MONTH IS NULL AND ".
					"TI.TFTD_DATE IS NULL AND ".
					"TI.IS_DELETED IS NULL) ".
					"OR (TI.IS_DELETED = TRUE)";
		
		mysql_query($SQL) or die(mysql_error());
		
	}
	
	function updateExistingDailyThoughts(){
		
		$SQL = "UPDATE TFTD_INDEX TI INNER JOIN ".
			"( ".
				"SELECT TI.TFTD_YEAR as TFTD_YEAR, TI.TFTD_MONTH as TFTD_MONTH, TI.TFTD_DATE as TFTD_DATE, TI.TFTD_TITLE AS OLD_TITLE, TIS.TFTD_TITLE AS NEW_TITLE, TI.TFTD_URL AS OLD_URL, TIS.TFTD_URL AS NEW_URL ".
				"FROM  TFTD_INDEX TI INNER JOIN TFTD_INDEX_STAGING TIS ".
				"ON ".
					"TIS.TFTD_YEAR = TI.TFTD_YEAR AND ".
					"TIS.TFTD_MONTH = TI.TFTD_MONTH AND ".
					"TIS.TFTD_DATE = TI.TFTD_DATE ".
				"WHERE ".
					"(TIS.TFTD_TITLE != TI.TFTD_TITLE OR TIS.TFTD_URL != TI.TFTD_URL) AND ".
					"TI.TFTD_IS_MANUAL = FALSE AND ".
					"TI.IS_DELETED = FALSE ".
				") C ".
				"ON TI.TFTD_YEAR = C.TFTD_YEAR AND ".
					"TI.TFTD_MONTH = C.TFTD_MONTH AND ".
					"TI.TFTD_DATE = C.TFTD_DATE ".
				"SET ".
					"TI.TFTD_TITLE = C.NEW_TITLE, ".
					"TI.TFTD_URL = C.NEW_URL ";
		
		mysql_query($SQL) or die(mysql_error());
		
	}
	
	function deleteNonExistingDailyThoughts(){
		$SQL = "UPDATE TFTD_INDEX TI INNER JOIN ".
				"( ".
					"SELECT TI.TFTD_YEAR as TFTD_YEAR, TI.TFTD_MONTH as TFTD_MONTH, TI.TFTD_DATE as TFTD_DATE ".
					"FROM  TFTD_INDEX TI LEFT OUTER JOIN TFTD_INDEX_STAGING TIS ".
 					"ON TIS.TFTD_YEAR = TI.TFTD_YEAR AND ".
 						"TIS.TFTD_MONTH = TI.TFTD_MONTH AND ".
  						"TIS.TFTD_DATE = TI.TFTD_DATE ".
					"WHERE TIS.TFTD_YEAR IS NULL AND ".
  						"TIS.TFTD_MONTH IS NULL AND ".
 						"TIS.TFTD_DATE IS NULL AND ".
  						"TI.TFTD_IS_MANUAL = FALSE AND ".
 						"TI.IS_DELETED = FALSE ".
				") C ".
				"ON TI.TFTD_YEAR = C.TFTD_YEAR AND ".
 				"TI.TFTD_MONTH = C.TFTD_MONTH AND ".
 				"TI.TFTD_DATE = C.TFTD_DATE ".
				"SET ".
  					"TI.IS_DELETED = TRUE";
		
		mysql_query($SQL) or die(mysql_error());
	}

	
	public function syncDailyThoughts($dailyThougts){
		$this->clearStaging();
		$this->createStaging($dailyThougts);
		
		$this->insertNewDailyThoughts();
		$this->updateExistingDailyThoughts();
		$this->deleteNonExistingDailyThoughts();
	}
	
	public function getDailyThoughtExceptionURL($year, $month){
		$URL = "";
		$SQL = "SELECT TME.TFTD_URL AS TFTD_URL FROM TFTD_MONTHLYINDEX_EXCEPTIONS TME WHERE TME.EXCEPTION_YEAR = ".$year." AND TME.EXCEPTION_MONTH = ".$month;
		
		$result = mysql_query($SQL) or die(mysql_error());
		
		if($result){
			$row = mysql_fetch_assoc($result);
			$URL = $row['TFTD_URL'];
		}
		
		mysql_free_result($result);
		
		return $URL;
	}

}
?>