<?php
require_once("ScheduleRestHandler.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

switch($view){
	
	case "all":
		$scheduleRestHandler = new ScheduleRestHandler();
		$scheduleRestHandler->getSchedule();
		break;
		
	case "" :
		//404 - not found;
		break;		
	
}
?>