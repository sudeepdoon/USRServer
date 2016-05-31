<?php
require_once("DailyThought.php");
require_once("Logger.php");

class DatabaseServices {
	
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "USR";
	private $con;

	//--------------------------------------------- Servcies for Database Connection ----------------------------------------
	public function connectDatabase(){
		$this->con = mysql_connect($this->servername, $this->username, $this->password) or
		die(LoggerService::error("Could not connect to database: " . mysql_error()));
		
		mysql_select_db($this->dbname);
	}
	
	public function closeDatabase(){
		mysql_close($this->con);
	}
	
	
	//----------------------------------------------- Services for Daily Thoughts -------------------------------------------
	function clearDailyThoughtsStaging(){
		$SQL = "DELETE FROM TFTD_INDEX_STAGING";
		mysql_query($SQL) or die(LoggerService::error("Error while deleting Staging: ".mysql_error()));
		LoggerService::info("Deleted from Statging");
	}
	
	function createDailyThoughtsStaging($dailyThougts){
		foreach ($dailyThougts as $key=>$dailyThought){
			$SQL = "INSERT INTO TFTD_INDEX_STAGING (TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL) VALUES (".
				$dailyThought->getYear().",".$dailyThought->getMonth().",".$dailyThought->getDate().",'".
				str_replace("'", "''", $dailyThought->getTitle())."','".$dailyThought->getURL()."')";
			
			mysql_query($SQL) or die(LoggerService::error($SQL." : ".mysql_error()));
		}
		LoggerService::info("Inserted into Statging");
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
		
		mysql_query($SQL) or die(LoggerService::error("Error insertNewDailyThoughts: ".mysql_error()));
		LoggerService::info("New Daily Thoughts Inserted: ".mysql_affected_rows());
		
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
					"TI.TFTD_URL = C.NEW_URL, ".
					"TI.LAST_UPDATED = CURRENT_TIMESTAMP ";
		
		mysql_query($SQL) or die(LoggerService::error("Error updateExistingDailyThoughts: ".mysql_error()));
		LoggerService::info("Updated existing Daily Thoughts: ".mysql_affected_rows());
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
  					"TI.IS_DELETED = TRUE, ".
					"TI.LAST_UPDATED = CURRENT_TIMESTAMP ";
		
		mysql_query($SQL) or die(LoggerService::error("Error deleteNonExistingDailyThoughts: ". mysql_error()));
		LoggerService::info("Delelted Daily Thoughts: ".mysql_affected_rows());
	}

	
	public function syncDailyThoughts($dailyThougts){
		$this->clearDailyThoughtsStaging();
		$this->createDailyThoughtsStaging($dailyThougts);
		
		$this->insertNewDailyThoughts();
		$this->updateExistingDailyThoughts();
		$this->deleteNonExistingDailyThoughts();
	}
	
	public function getDailyThoughtExceptionURL($year, $month){
		$URL = "";
		$SQL = "SELECT TME.TFTD_URL AS TFTD_URL FROM TFTD_MONTHLYINDEX_EXCEPTIONS TME WHERE TME.EXCEPTION_YEAR = ".$year." AND TME.EXCEPTION_MONTH = ".$month;
		
		$result = mysql_query($SQL) or die(LoggerService::error("Error getDailyThoughtExceptionURL: ".mysql_error()));
		
		if($result){
			$row = mysql_fetch_assoc($result);
			$URL = $row['TFTD_URL'];
		}
		
		mysql_free_result($result);
		
		return $URL;
	}

	//---------------------------------------- Servcies for Weekly Lessons --------------------------------
	
	public function getWeeklySeriesIndex(){
		$SQL = "SELECT WL_SERIES_ID AS SERIES_ID, WL_TITLE AS TITLE, WL_INDEX_URL AS URL, WL_URL_PREFIX AS PREFIX FROM WEEKLY_SERIES_INDEX";
		$weeklySeriesIndex = array();
		
		$result = mysql_query($SQL) or die(LoggerService::error("Error getWeeklySeriesIndex: ".mysql_error()));
		
		if($result){
			while($row = mysql_fetch_assoc($result)){
				$innerArray = array();
				$innerArray["SERIES_ID"] = $row["SERIES_ID"];
				$innerArray["TITLE"] = $row["TITLE"];
				$innerArray["URL"] = $row["URL"]; 
				$innerArray["PREFIX"] = $row["PREFIX"];
				
				$weeklySeriesIndex[$row["SERIES_ID"]] = $innerArray;
			}
		}else{
			LoggerService::debug("No result in getWeeklySeriesIndex");
		}
		
		return $weeklySeriesIndex;
	}
	
	public function syncWeeklyLessons($weeklyLessons){
		$this->clearWeeklyLessonStaging();
		$this->createWeeklyLessonStaging($weeklyLessons);
		
		$this->insertNewWeeklyLessons();
		$this->updateExistingWeeklyLessons();
		$this->deleteNonExistingWeeklyLessons();
	}
	
	function clearWeeklyLessonStaging(){
		$SQL = "DELETE FROM WL_INDEX_STAGING";
		mysql_query($SQL) or die(LoggerService::error("Error while deleting Staging: ".mysql_error()));
		LoggerService::info("Deleted from Statging");
	}
	
	function createWeeklyLessonStaging($weeklyLessons){
		foreach($weeklyLessons as $key => $weeklyLesson){
			$SQL = "INSERT INTO WL_INDEX_STAGING (WL_SERIES_ID, WL_CODE, WL_TITLE, WL_URL) VALUES (".$weeklyLesson->getSeries().",'".
			$weeklyLesson->getLessonCode()."','".str_replace("'", "''",$weeklyLesson->getTitle())."','".$weeklyLesson->getURL()."')";
			
			mysql_query($SQL) or die(LoggerService::error($SQL." : ".mysql_error()));
		}
		LoggerService::info("Inserted into Statging");
		
	}
	
	function insertNewWeeklyLessons(){
		$SQL = "INSERT INTO WL_INDEX (WL_SERIES_ID, WL_CODE, WL_TITLE, WL_URL) ".      
				  "SELECT WIS.WL_SERIES_ID AS SERIES_ID, WIS.WL_CODE AS CODE, WIS.WL_TITLE AS TITLE, WIS.WL_URL AS URL ".
				  "FROM WL_INDEX_STAGING WIS LEFT OUTER JOIN WL_INDEX WI ".
				  "ON WIS.WL_SERIES_ID = WI.WL_SERIES_ID AND ".
				    "WIS.WL_CODE = WI.WL_CODE ".
				  "WHERE ".
				    "(WI.WL_TITLE IS NULL AND ".
				    "WI.WL_URL IS NULL AND ".
				    "WI.IS_DELETED IS NULL) ".
				    "OR (WI.IS_DELETED = TRUE)";
		
		mysql_query($SQL) or die(LoggerService::error("Error insertNewWeeklyLessons: ".mysql_error()));
		LoggerService::info("New Weekly Lessons Inserted: ".mysql_affected_rows());
	}
	
	function updateExistingWeeklyLessons(){
		$SQL = "UPDATE WL_INDEX WI INNER JOIN ".
				"( ".
				  "SELECT WI.WL_SERIES_ID AS SERIES_ID, WI.WL_CODE AS CODE, WI.WL_TITLE AS OLD_TITLE, WIS.WL_TITLE AS NEW_TITLE, WI.WL_URL AS OLD_URL, WIS.WL_URL AS NEW_URL ". 
				  "FROM WL_INDEX WI INNER JOIN WL_INDEX_STAGING WIS ".
				  "ON ".
				    "WI.WL_SERIES_ID = WIS.WL_SERIES_ID AND ".
				    "WI.WL_CODE = WIS.WL_CODE ".
				  "WHERE ".
				    "(WIS.WL_TITLE != WI.WL_TITLE OR WIS.WL_URL != WI.WL_URL) AND ".
				    "WI.WL_IS_MANUAL = FALSE AND ".
				    "WI.IS_DELETED = FALSE ".
				") C ".
				"ON WI.WL_SERIES_ID = C.SERIES_ID AND ".
				  "WI.WL_CODE = C.CODE ".
				"SET ".
				  "WI.WL_TITLE = C.NEW_TITLE, ".
				  "WI.WL_URL = C.NEW_URL, ".
				  "WI.LAST_UPDATED = CURRENT_TIMESTAMP";
		
		mysql_query($SQL) or die(LoggerService::error("Error updateExistingWeeklyLessons: ".mysql_error()));
		LoggerService::info("Updated existing Weekly Lessons: ".mysql_affected_rows());		
	}
	
	function deleteNonExistingWeeklyLessons(){
		$SQL = "UPDATE WL_INDEX WI INNER JOIN ".
			"( ".
			  "SELECT WI.WL_SERIES_ID AS SERIES_ID, WI.WL_CODE AS CODE ".
			  "FROM WL_INDEX WI LEFT OUTER JOIN WL_INDEX_STAGING WIS ".
			  "ON WI.WL_SERIES_ID = WIS.WL_SERIES_ID AND ".
			   "WI.WL_CODE = WIS.WL_CODE ".
			  "WHERE WIS.WL_TITLE IS NULL AND ".
			    "WIS.WL_URL IS NULL AND ".
			    "WI.WL_IS_MANUAL = FALSE AND ".
			    "WI.IS_DELETED = FALSE ".
			  ") C ".
			"ON WI.WL_SERIES_ID = C.SERIES_ID AND ".
			  "WI.WL_CODE = C.CODE ".
			"SET ".
			  "WI.IS_DELETED = TRUE, ".
			  "WI.LAST_UPDATED = CURRENT_TIMESTAMP";
		
		mysql_query($SQL) or die(LoggerService::error("Error deleteNonExistingWeeklyLessons: ".mysql_error()));
		LoggerService::info("Deleted Weekly Lessons: ".mysql_affected_rows());		
	}
}
?>