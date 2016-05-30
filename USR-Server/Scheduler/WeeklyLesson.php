<?php

class WeeklyLesson{
	private $series;
	private $lessonCode;
	private $title;
	private $url;
	
	public function getSeries(){
		return $this->series;
	}
	
	public function getLessonCode(){
		return $this->lessonCode;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getURL(){
		return $this->url;
	}
	
	public function setValue($series, $lessonCode, $title, $url){
		$this->series = $series;
		$this->lessonCode = $lessonCode;
		$this->title = $title;
		$this->url = $url;
	}
	
	public function toObjectArray($weeklyLessonArray){
		$weeklyLessons = array();
		
		foreach ($weeklyLessonArray as $series => $lessons){
			foreach($lessons as $code => $lesson){
				$weeklyLesson = new WeeklyLesson();
				$weeklyLesson->setValue($series, $code, $lesson["TITLE"], $lesson["URL"]);
				
				$weeklyLessons[] = $weeklyLesson;
			}
		}
		
		return $weeklyLessons;
	}
}

?>