<?php

class DailyThought {
	private $year;
	private $month;
	private $date;
	private $title;
	private $url;
	
	public function setValues($year,$month,$date,$title,$url){
		$this->year = $year;
		$this->month = $month;
		$this->date = $date;
		$this->title = $title;
		$this->url = $url;
	}
	
	public function  __toString(){
		return $this->year."-".$this->month."-".$this->date+": ".$this->title."  URL -> ".$this->url."/n";
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getMonth(){
		return $this->month;
	}
	
	public function getDate(){
		return $this->date;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getURL(){
		return  $this->url;
	}
	
	function createURL($year,$month,$date){
		$monthLinks = array("1" => "jan", "2" => "feb", "3" => "mar",
				"4" => "apr", "5" => "may", "6" => "jun",
				"7" => "jul", "8" => "aug", "9" =>  "sept",
				"10" => "oct", "11" => "nov", "12" => "dec");
		
		$yy = substr($year,-2);
		
		$mm = "";
		if(intval($month) < 10){
			$mm = "0".$month;
		}else{
			$mm = $month;
		}
		
		$dd = "";
		if(intval($date) < 10){
			$dd = "0".$date;
		}else{
			$dd = $date;
		}		
		
		$link = "http://www.sda-archives.com/tftd/tftd/".$year."/".$monthLinks[$month]."/tftd_".$mm.$dd.$yy.".html";
		
		return $link;
	}
	
	public function toObjectArray($tftdArray){
		$dailyThougts = array();
			
		foreach ($tftdArray as $year => $monthArray){
			foreach($monthArray as $month => $dateArray){
				foreach ($dateArray as $date => $title){
					$url = $this->createURL($year, $month, $date);
					$dailyThought = new DailyThought();
					$dailyThought->setValues($year, $month, $date, $title, $url);
					
					$dailyThougts[] = $dailyThought;
					}
				}
			}
		return $dailyThougts;
	}
}

?>