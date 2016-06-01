<?php

class WeeklyLessons {
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
	
	public function getAllSeries(){
		if(! $this->con){
			$this->connectDatabase();
		}
		
		$SQL = "SELECT DISTINCT WL_SERIES_ID, WL_TITLE FROM WEEKLY_SERIES_INDEX ORDER BY WL_SERIES_ID";
		$result = mysql_query($SQL) or die(mysql_error());
		
		$returnArray = array();
		
		if ($result) {
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
				
				$rowArray["ID"] = $row['WL_SERIES_ID'];
				$rowArray["Title"] = str_replace("&", "and",$row['WL_TITLE']);
				
				$returnArray[] = $rowArray;
			}
		}
		
		mysql_free_result($result);
		
		return $returnArray;
	}
	
	public function getAllLessons($seriesID){
		if(! $this->con){
			$this->connectDatabase();
		}
		
		$SQL = "SELECT WL_CODE, WL_TITLE, WL_URL FROM WL_INDEX WHERE IS_DELETED <> TRUE AND WL_SERIES_ID = ".$seriesID." ORDER BY WL_CODE, WL_TITLE";
		$result = mysql_query($SQL) or die(mysql_error());
		
		$returnArray = array();
		
		if ($result) {
			while($row = mysql_fetch_assoc($result)) {
				$rowArray = array();
		
				$rowArray["Code"] = $row['WL_CODE'];
				$rowArray["Title"] = str_replace("&", "and",$row['WL_TITLE']);
				$rowArray["Url"] = $row['WL_URL'];
				
				$returnArray[] = $rowArray;
			}
		}
		
		mysql_free_result($result);
		
		return $returnArray;		
	}
}

?>