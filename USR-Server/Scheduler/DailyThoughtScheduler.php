<?php
require_once("DatabaseServices.php");
require_once("DailyThoughtsServices.php");
require_once("DailyThought.php");
require_once("Logger.php");

set_time_limit(200);
run();

function run(){
	LoggerService::info("Starting the scheduler");
	
	$databaseServices = new DatabaseServices();
	$databaseServices->connectDatabase();
	LoggerService::info("Connected to database");
	
	$dailyThoughtServices = new DailyThoughtServices();
	$tftdArray = $dailyThoughtServices->readDailyThoughts();
	
	$dailyThought = new DailyThought();
	$dailyThougts = $dailyThought->toObjectArray($tftdArray);
	LoggerService::info("Read daily thoughts: ".count($dailyThougts));
	
	
	$databaseServices->syncDailyThoughts($dailyThougts);
	$databaseServices->closeDatabase();
	LoggerService::info("Closed database connection");
}
?>
