<?php
require_once("WeeklyLessonRestHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
	/*
	 controls the RESTful services
	 URL mapping
	 */
switch($view){
	
	case "series":
		$weeklyLessonRestHandler = new WeeklyLessonRestHandler();
		$weeklyLessonRestHandler->getAllSeries();
		break;
		
	case "lessons":
		$weeklyLessonRestHandler = new WeeklyLessonRestHandler();
		$weeklyLessonRestHandler->getAllLessons($_GET["series"]);
		break;		
}
?>