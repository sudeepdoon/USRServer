<?php
require_once("DatabaseServices.php");
require_once("DailyThoughtsServices.php");
require_once("DailyThought.php");

set_time_limit(200);

run();

function run(){
	$dailyThoughtServices = new DailyThoughtServices();
	$tftdArray = $dailyThoughtServices->readDailyThoughts();
	
	$dailyThought = new DailyThought();
	$dailyThougts = $dailyThought->toObjectArray($tftdArray);
	
	$databaseServices = new DatabaseServices();
	$databaseServices->syncDailyThoughts($dailyThougts);
}
?>
