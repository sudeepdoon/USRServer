<?php

require_once("SimpleRest.php");
require_once("Schedule.php");

class ScheduleRestHandler extends SimpleRest{
	
	function getSchedule(){
		$schedule = new Schedule();
		$rawData = $schedule->getSchedule();
		
		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No data found!');
		} else {
			$statusCode = 200;
		}
		
		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
		
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}

	public function encodeHtml($responseData) {
	
		$htmlResponse = "<table border='1'>";
		foreach($responseData as $key=>$value) {
			$htmlResponse .= "<tr>";
			foreach($value as $innerKey=>$innerValue) {
				$htmlResponse .= "<td>". $innerKey. "</td><td>". $innerValue. "</td>";
			}
	
			$htmlResponse .= "</tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;
	}
	
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;
	}
	
	public function encodeXml($responseData) {
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><dailyThoughts></dailyThoughts>');
		foreach($responseData as $key=>$value) {
			foreach($value as $innerKey=>$innerValue) {
				//$xml->addChild($key, $value);
				$xml->addChild($innerKey, $innerValue);
			}
		}
		return $xml->asXML();
	}
	
}

?>