<?php

class DailyThought {
	private $year;
	private $month;
	private $date;
	private $title;
	private $url;
	
	public function __construct($year,$month,$date,$title,$url){
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
		return 'Test';
	}
	
	public function toObjectArray($tftdArray){
		$dailyThougts = array();
			
		foreach ($tftdArray as $year => $monthArray){
			foreach($monthArray as $month => $dateArray){
				foreach ($dateArray as $date => $title){
					$url = $this->createURL($year, $month, $date);
					$dailyThought = new DailyThought($year, $month, $date, $title, $url);
					
					$dailyThougts[] = $dailyThought;
					}
				}
			}
		return $dailyThougts;
	}
}

?>