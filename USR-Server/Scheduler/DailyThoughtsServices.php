<?php

require_once("DatabaseServices.php");
require_once("Logger.php");

include './simple_html_dom.php';


class DailyThoughtServices {
	
	private $databaseServices;
	
	public function readDailyThoughts(){
		$html = file_get_html('http://www.sda-archives.com/tftd/tftd/index.html');
		
		//Get information from the TFTD Index page...
		$years = $this->getListOfYears($html);
		$months = $this->getListOfMonths($html, $years);
		
		//Get TFTD details from each monthly index page of each month captured above
		$tftdArray = $this->getDailyThoughtIndex($months);
		
		return $tftdArray;
	}
	
	function getListOfYears($html){
		$years = array();
		$count = 0;
		
		foreach($html->find('th') as $year){
			$text = $year->plaintext;
			$years[$count] = trim(substr($text,0,strlen($text)-1));
			$count++;
		}
		
		return $years;
	}
	
	function getListOfMonths($html, $years){
		//Mapping of month's name on the screen to month's numeric equivalent
		$monthNo = array("Jan" => "1", "Feb" => "2", "Mar" => "3", 
						 "Apr" => "4", "May" => "5", "June" => "6", 
						 "July" => "7", "Aug" => "8", "Sept" => "9",
						 "Oct" => "10", "Nov" => "11", "Dec" => "12");
		
		$months = array();
		$yearCount = 0;
		$mounthCount = 0;
		
		foreach($html->find('td') as $cell){
			$text = trim($cell->plaintext);
			
			if($mounthCount == 12){
				$yearCount++;
				$mounthCount = 0;
			}
			if($text != '' AND ord($text) != 38){
				$months[$years[$yearCount]][$mounthCount] = $monthNo[$text];
			}
			
			$mounthCount++;
		}
		
		return $months;
	}
	
	function getMonthlyIndexPageLink($year, $month){
		//Mapping of month names on the TFTD Index page; to the name of month as used in the link for monthly index
		$monthLinks = array("1" => "jan", "2" => "feb", "3" => "mar", 
							"4" => "apr", "5" => "may", "6" => "jun", 
							"7" => "jul", "8" => "aug", "9" =>  "sept", 
							"10" => "oct", "11" => "nov", "12" => "dec"); 
		
		if($this->databaseServices == null)
			$this->databaseServices = new DatabaseServices();
		
		$exceptionURL = $this->databaseServices->getDailyThoughtExceptionURL($year, $month);
		
		if($exceptionURL != ""){
			$link = $exceptionURL;
			LoggerService::info("Using exception URL: ".$link);
		}
		else{
			$link = "http://www.sda-archives.com/tftd/tftd/".$year."/".$monthLinks[$month]."/index.html";
		}
		
		return $link;
		
	}
	
	function getDailyThoughtIndex($months){
		$tftdArray = array();
		foreach ($months as $year => $monthlyArray){
			foreach ($monthlyArray as $key => $month){
				$link = $this->getMonthlyIndexPageLink($year, $month);
				$monthIndex = $this->getMonthlyIndexArray($link);
				$tftdArray[$year][$month] = $monthIndex;
			}
		}
		
		return $tftdArray;
	}
	
	function getMonthlyIndexArray($link){
		$monthIndex = array();
		$html = file_get_html($link);

		if($html == null){
			LoggerService::error("Could not open link: ".$link);
			return $monthIndex;
		}
		
		//Getting list of dates from Column 1 and 3 of the montly index
		$dates = array();
		foreach($html->find('th') as $cell){
			$str = trim($cell->plaintext);
			if($str!="" AND ord($str) != 38){
				$strArray = explode(" ",$str);
				$dates[] = intval(substr($strArray[1],0,2));
			}else{
				$dates[] = "";
			}
		}
		
		$count = 0;
		$titles = array();
		foreach($html->find('td') as $cell){
			$str = trim($cell->plaintext);
		
			$titles[$count] = $str;
			$count++;
		}
			
		if(count($dates) == count($titles)){
			$count = 0;
			foreach($dates as $key=>$date){
				if($date!="" AND ord($date) != 38 AND $titles[$count]!="" AND ord($titles[$count]) != 38){
					$monthIndex[$date] = $titles[$count];
				}
			$count++;
			}
			
		}else{
			LoggerService::error("There was some error while reading monthly index from: ".$link);
		}
		
		ksort($monthIndex);
		
		return $monthIndex;
		
	}
	
}
?>