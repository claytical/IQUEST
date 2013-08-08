<?php

$dataSet = $_REQUEST['set'];
$neighborhood = $_REQUEST['neighborhood'];
$startDate = $_REQUEST['start'];
$endDate = $_REQUEST['end'];
$usePercentage = FALSE;

if ($dataSet == "flow") {
	echo getFlowData($neighborhood, $startDate, $endDate, TRUE);
}
if ($dataSet == "water" || $dataSet == "particles" || $dataSet == "taste") {
	echo getData($dataSet, $neighborhood, $startDate, $endDate);
}

if ($dataSet == "location") {
	echo getLocationData($neighborhood);
}
if ($dataSet == "neighborhoods") {
	echo getNeighborhoods();
}
if ($dataSet == "frequency") {
	echo getFrequencyData($startDate, $endDate);
}

function getNeighborhoods() {
	include("connect.php");
	$query = "SELECT id, location FROM locations ORDER BY location ASC";
	$result = $mysqli->query($query);
	foreach($result as $row) {
		$obj = new stdClass;
		$obj->id = $row["id"];
		$obj->location = $row["location"];
		$data[] = $obj;
	}
	return json_encode($data);
}

function getFrequencyData($start, $end) {
	include("connect.php");
	$data = new stdClass;

	if ($start == NULL && $end == NULL) {
		$date_query = "SELECT DISTINCT(date) as date FROM finalData ORDER BY date ASC";

	}
	else {
		$date_query = "SELECT DISTINCT(date) as date FROM finalData WHERE date <= '$end' AND date >= '$start' ORDER BY date ASC";
	}
	$dates = $mysqli->query($date_query);
	$location_query = "SELECT location FROM locations ORDER BY location ASC";
	$result = $mysqli->query($location_query);
//	$locations = ["1999-01-31"];
	$data->headers[] = ["type" => "date", "label" => "Date"];
	foreach ($result as $row) {
		$data->headers[] = ["type" => "number", "label" => $row["location"]];
	}
	//first row of chart
//	$data[] = $locations;

	foreach ($dates as $date) {
		$currentDate = $date["date"];
		$neighborhoodByDateQuery = "SELECT COUNT(finalData.water) AS reports, finalData.date, locations.location, locations.id FROM finalData INNER JOIN locations ON finalData.location = locations.id WHERE date = '$currentDate' GROUP BY locations.id, date UNION ALL SELECT 0 as reports, '$currentDate' as date, locations.location, locations.id FROM locations WHERE NOT EXISTS (SELECT * FROM finalData WHERE locations.id = finalData.location AND date = '$currentDate') ORDER BY location, date ASC";

		$statsByDate = $mysqli->query($neighborhoodByDateQuery);
		//add the date to the first column of the chart
		$stats[] = $currentDate;		
		//add the stats for each neighborhood for the specified date

		foreach ($statsByDate as $row) {
			$stats[] = intval($row["reports"]);
		}

		$data->values[] = $stats;
		unset($stats);
	}
	return json_encode($data);
	

}

function getLocationData($loc_num) {
	include("connect.php");
	$location = new stdClass;
//	getLocationData($neighborhood);
	$customer_query = "SELECT DISTINCT(number) as customers from finalData WHERE location = $loc_num";
	$result = $mysqli->query($customer_query);
	$location->customers = $result->num_rows;
	$report_query = "SELECT water as reports, date, locations.location FROM finalData INNER JOIN locations ON locations.id = finalData.location WHERE finalData.location = $loc_num  ORDER BY date ASC";
	$result = $mysqli->query($report_query);
	$row = $result->fetch_array();
	$location->name = $row["location"];
	$location->reports = $result->num_rows;
	$location->collection_start = $row["date"];

	return json_encode($location);
}

function getFlowData($location, $startingDate = NULL, $endingDate = NULL, $percentage = TRUE) {
	include("connect.php");
	$data = new stdClass;

	$query = "SELECT SUM(twelve) as twelve, SUM(four) as four, SUM(three) as three, SUM(none) as none, date FROM (
		SELECT COUNT(hours) as twelve, 0 as four, 0 as three, 0 as none, date FROM finalData WHERE hours = 'H' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date 
		UNION SELECT 0 as twelve, COUNT(hours) as four, 0 as three, 0 as none, date FROM finalData WHERE hours = 'M' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date  
		UNION SELECT 0 as twelve, 0 as four, COUNT(hours) as three, 0 as none, date FROM finalData WHERE hours = 'L' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date  
		UNION SELECT 0 as twelve, 0 as four, 0 as three, COUNT(hours) as none, date FROM finalData WHERE hours = 'Z' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date
		) as tmpTable GROUP BY date";
	$result = $mysqli->query($query);
	$data->headers = [["type" => "date", "label" => "Date"],
					  ["type" => "number", "label" => "Over 12 Hours"],					
					  ["type" => "number", "label" => "4-12 Hours"],					
					  ["type" => "number", "label" => "Up to 3 Hours"],
					  ["type" => "number", "label" => "No Flow"]];			

	if ($percentage) {
		foreach($result as $row) {
			$total = (intval($row["twelve"]) + intval($row["four"]) + intval($row["three"]) + intval($row["none"]));
			$twelve = ($row["twelve"] / $total) * 100;
			$four = ($row["four"] / $total) * 100;
			$three = ($row["three"] / $total) * 100;
			$none = ($row["none"] / $total) * 100;
			$data->values[] = array($row["date"], $twelve, $four, $three, $none);

		}
	}
	else {
		foreach($result as $row) {

			$data->values[] = array($row["date"], intval($row["twelve"]), intval($row["four"]), intval($row["three"]), intval($row["none"]));
		}
	}


	return json_encode($data);

}


function getData($set, $location, $startingDate = NULL, $endingDate = NULL, $percentage = TRUE) {

	include("connect.php");
	$data = new stdClass;

	$setArray = array(
						"water" => array(
										"values" => ["C", "D"],
										"description" => ["Clear", "Discolored or Cloudy"]
										),
						"particles" => array(
										"values" => ["N", "P"],
										"description" => ["No", "Yes"]
										),
						"taste" => array(
										"values" => ["G", "B"],
										"description" => ["Tastes Good, No Odor", "Bad Taste or Odor"]
										),
						"hours" => array(
										"values" => ["H", "M", "L", "Z"],
										"description" => ["Over 12 hours", "4-12 hours", "Up to 3 hours", "No flow"]
										)
						);

	$query = "";
	if (count($setArray[$set]) == 2) {
		$query .= "SELECT SUM(good) as good, SUM(bad) as bad, date FROM (";
		foreach ($setArray[$set]["values"] as $key => $value) {
			if ($key == 0) {
				$query .= " SELECT COUNT($set) as good, 0 as bad, date FROM finalData WHERE $set = '$value' AND date > '$startingDate' AND date < '$endingDate' GROUP BY date ";
			}
			else {

				$query .= " UNION SELECT 0 as good, COUNT($set) as bad, date FROM finalData WHERE $set = '$value' AND date > '$startingDate' AND date < '$endingDate' GROUP BY date ";

			}

		}

		$query .= ") as tmpTable GROUP BY date";
		$result = $mysqli->query($query);
		$data->headers = [["type" => "date", "label" => "Date"],
						  ["type" => "number", "label" => $setArray[$set]["description"][0]],					
						  ["type" => "number", "label" => $setArray[$set]["description"][1]]];			


		if (percentage) {
			foreach($result as $row) {
				$total = (intval($row["good"]) + intval($row["bad"]));
				$good = ($row["good"] / $total) * 100;
				$bad = ($row["bad"] / $total) * 100;
				$data->values[] = array($row["date"], $good + "%", $bad + "%");

			}
		}
		else {
			foreach($result as $row) {
				$data->values[] = array($row["date"], $row["good"], $row["bad"]);
			}

		}
	}

		return json_encode($data);
}

?>