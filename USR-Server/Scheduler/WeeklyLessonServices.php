<?php

require_once("DatabaseServices.php");
require_once("Logger.php");

include './simple_html_dom.php';

class WeeklyLessonServices{
	
	public function readWeeklyLessons($databaseServices){
		
		$weeklySeriesIndex = $databaseServices->getWeeklySeriesIndex();
		$weeklyLessonArray = $this->getAllWeeklyLessons($weeklySeriesIndex);
		
		return $weeklyLessonArray;
		
	}
	
	private function getAllWeeklyLessons($weeklySeriesIndex){
		$weeklyLessonArray = array();
		foreach($weeklySeriesIndex as $id=>$series){
			$lessons = $this->getWeeklyLessonForSeries($series);
			$weeklyLessonArray[$series["SERIES_ID"]] = $lessons;
		}
		
		return $weeklyLessonArray;
	}
	
	private function getWeeklyLessonForSeries($series){
		$html = file_get_html($series["URL"]);
		$lessons = array();
		
		$lessonList = array();
		foreach($html->find('th') as $cell){
			$str = trim($cell->plaintext);
		
			if($str!="" AND ord($str) != 38){
				$strArray = explode(" ",$str);
				$lessonList[] = substr($strArray[1],0,3);
			}else{
				$lessonList[] = "";
			}
		}
		
		$count = 0;
		$titles = array();
		foreach($html->find('td') as $cell){
			$str = trim($cell->plaintext);
		
			$titles[$count] = $str;
			$count++;
		}
		
		if(count($lessonList) == count($titles)){
			$count = 0;
			$details = array();
			foreach($lessonList as $key=>$lesson){
				if($lesson!="" AND ord($lesson) != 38 AND $titles[$count]!="" AND ord($titles[$count]) != 38){
					$details["TITLE"] = $titles[$count];
					$details["URL"] = $series["PREFIX"].$lesson.".html";
					$lessons[$lesson] = $details;
				}
				$count++;
			}
				
		}else{
			LoggerService::error("There was some error while reading lesson index from: ".$series["URL"]);
		}
		
		ksort($lessons);
		
		return $lessons;
	}
	
}
?>