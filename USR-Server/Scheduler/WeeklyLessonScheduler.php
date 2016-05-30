<?php
require_once("DatabaseServices.php");
require_once("Logger.php");
require_once("WeeklyLessonServices.php");
require_once("WeeklyLesson.php");

set_time_limit(200);
run();

function run(){
	LoggerService::info("Starting the Weekly Lesson scheduler");
	
	$databaseServices = new DatabaseServices();
	$databaseServices->connectDatabase();
	LoggerService::info("Connected to database");
	
	$weeklyLessonServices = new WeeklyLessonServices();
	$weeklyLessonArray = $weeklyLessonServices->readWeeklyLessons($databaseServices);
	
	$weeklyLesson = new WeeklyLesson();
	$weeklyLessons = $weeklyLesson->toObjectArray($weeklyLessonArray);
	LoggerService::info("Read Weekly Lessons: ".count($weeklyLessons));
	
	$databaseServices->syncWeeklyLessons($weeklyLessons);
	
	$databaseServices->closeDatabase();
	LoggerService::info("Closed database connection");
}
?>