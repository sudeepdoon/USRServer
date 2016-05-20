<?php

include './simple_html_dom.php';

set_time_limit(200);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "USR";

// Create connection
$con = mysql_connect($servername, $username, $password) or  
    die("Could not connect: " . mysql_error()); 

mysql_select_db($dbname);

$html = file_get_html('http://www.sda-archives.com/tftd/tftd/index.html');

	$monthNo = array("Jan" => "1", "Feb" => "2", "Mar" => "3", "Apr" => "4", "May" => "5", "June" => "6", "July" => "7", "Aug" => "8", "Sept" => "9", "Oct" => "10", "Nov" => "11", "Dec" => "12"); 

	$monthLinks = array("Jan" => "jan", "Feb" => "feb", "Mar" => "mar", "Apr" => "apr", "May" => "may", "June" => "jun", "July" => "jul", "Aug" => "aug", "Sept" =>  "sept", "Oct" => "oct", "Nov" => "nov", "Dec" => "dec"); 

$years = array();
$monthArray = array();
//Storing the years in an array
$count = 0;
foreach($html->find('th') as $year){
  $text = $year->plaintext;
  $years[$count] = trim(substr($text,0,strlen($text)-1));
  $count++;
}

//Storing the months or each year in an array
$year = 0;
$months = 0;
foreach($html->find('td') as $cell){
  $text = trim($cell->plaintext);
  if($months == 12){
    $year++;
    $months = 0;
  }

  if($text != '' AND ord($text) != 38){
    $monthArray[$year][$months] = $text;
  }
  
  $months++;    
}



// Getting details for the month's index page also.
foreach ($years as $key => $year) {
	$monthIndex = array();
	foreach ($monthArray[$key] as $monthKey => $month)
	{
		$monthIndex[$month] = getMonthIndex($year,$month);
		foreach ($monthIndex[$month] as $date => $title)
			{

				$link = "http://www.sda-archives.com/tftd/".$year."/".$monthLinks[$month]."/tftd_mmddyy.html";

				$SQL = "INSERT INTO TFTD_INDEX (TFTD_YEAR, TFTD_MONTH, TFTD_DATE, TFTD_TITLE, TFTD_URL) VALUES (".$year.",".$monthNo[$month].",".$date.",'".str_replace("'", "''", $title)."','".$link."')";
				//echo $SQL;

				mysql_query($SQL) or die(mysql_error());
			}
		
	}
}

echo "Done...";

mysql_close($con);



function getMonthIndex($year,$month){


	$monthLinks = array("Jan" => "jan", "Feb" => "feb", "Mar" => "mar", "Apr" => "apr", "May" => "may", "June" => "jun", "July" => "jul", "Aug" => "aug", "Sept" =>  "sept", "Oct" => "oct", "Nov" => "nov", "Dec" => "dec"); 


	if($year == "2006" AND $month == "Dec") return array();
	if($year == "2009" AND $month == "July") return array();
	if($year == "2010" AND $month == "July") return array();

	$link = "http://www.sda-archives.com/tftd/tftd/".$year."/".$monthLinks[$month]."/index.html";

	if($year == "2011" AND $month == "July") $link = "http://www.sda-archives.com/tftd/tftd/2011/july/index.html";
	if($year == "2012" AND $month == "July") $link = "http://www.sda-archives.com/tftd/tftd/2012/july/index.html";
	if($year == "2013" AND $month == "Sept") $link = "http://www.sda-archives.com/tftd/tftd/2013/sep/index.html";
	if($year == "2014" AND $month == "Sept") $link = "http://www.sda-archives.com/tftd/tftd/2014/sep/index.html";
	if($year == "2015" AND $month == "Sept") $link = "http://www.sda-archives.com/tftd/tftd/2015/sep/index.html";

	$html = file_get_html($link);

	$dates = array();
	foreach($html->find('th') as $cell){
		$str = trim($cell->plaintext);
		if($str!="" AND ord($str) != 38){
			$strArray = explode(" ",$str);
			$dates[] = intval(substr($strArray[1],0,2));
		}
	}

	$count = 0;
	$monthIndex = array();
	foreach($html->find('td') as $cell){
		$str = trim($cell->plaintext);

		if ($str != '' AND ord($str) != 38){
			$monthIndex[$dates[$count]] = $str;
			$count++;
		}
	}

	ksort($monthIndex);

	return $monthIndex;
}

?>