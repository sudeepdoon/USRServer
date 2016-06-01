<?php
require_once("DailyThoughtRestHandler.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /mobile/list/
		$dailyThoughtRestHandler = new DailyThoughtRestHandler();
		$dailyThoughtRestHandler->getAllDailyThoughts();
		break;
		
	
	case "years":
		// to handle REST Url /mobile/years/
		$dailyThoughtRestHandler = new DailyThoughtRestHandler();
		$dailyThoughtRestHandler->getAllYears();
		break;
		
	case "months":
		$dailyThoughtRestHandler = new DailyThoughtRestHandler();
		$dailyThoughtRestHandler->getAllMonths($_GET["year"]);
		break;
		
	case "dates":
		$dailyThoughtRestHandler = new DailyThoughtRestHandler();
		$dailyThoughtRestHandler->getDailyThoughts($_GET["year"], $_GET["month"]);
		break;
			
/*	case "single":
		// to handle REST Url /mobile/show/<id>/
		$mobileRestHandler = new MobileRestHandler();
		$mobileRestHandler->getAllDailyThoughts($_GET["id"]);
		break;
*/

	case "" :
		//404 - not found;
		break;
}
?>
