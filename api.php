<?php

$dataSet = $_REQUEST['set'];
$neighborhood = $_REQUEST['neighborhood'];
$startDate = $_REQUEST['start'];
$endDate = $_REQUEST['end'];

if ($dataSet == "flow") {
	echo getFlowData($neighborhood, $startDate, $endDate);
}
if ($dataSet == "water" || $dataSet == "particles" || $dataSet == "taste") {
	echo getData($dataSet, $neighborhood, $startDate, $endDate);
}

if ($dataSet == "location") {
	echo getLocationData($neighborhood);
}

function getLocationData($loc_num) {
	include("connect.php");
	$location = new stdClass;
//	getLocationData($neighborhood);
	$customer_query = "SELECT DISTINCT(number) as customers from finalData WHERE location = $loc_num";
	$result = $mysqli->query($customer_query);
	$location->customers = $result->num_rows;
	$row = $result->fetch_array();
	$report_query = "SELECT water as reports FROM finalData WHERE location = $loc_num  ORDER BY date ASC";
	$result = $mysqli->query($report_query);
	$location->reports = $result->num_rows;
	$location->collection_start = $row['date'];

	return json_encode($location);
}

function getFlowData($location, $startingDate = NULL, $endingDate = NULL) {
	include("connect.php");

	$query = "SELECT SUM(twelve) as twelve, SUM(four) as four, SUM(three) as three, SUM(none) as none, date FROM (
		SELECT COUNT(hours) as twelve, 0 as four, 0 as three, 0 as none, date FROM finalData WHERE hours = 'H' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date 
		UNION SELECT 0 as twelve, COUNT(hours) as four, 0 as three, 0 as none, date FROM finalData WHERE hours = 'M' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date  
		UNION SELECT 0 as twelve, 0 as four, COUNT(hours) as three, 0 as none, date FROM finalData WHERE hours = 'L' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date  
		UNION SELECT 0 as twelve, 0 as four, 0 as three, COUNT(hours) as none, date FROM finalData WHERE hours = 'Z' AND location = $location AND date > '$startingDate' AND date < '$endingDate' GROUP BY date
		) as tmpTable GROUP BY date";
	$result = $mysqli->query($query);
	$data[] = ["date", "Over 12 Hours", "4-12 Hours", "Up to 3 Hours", "No Flow"];
	foreach($result as $row) {
		$data[] = array($row["date"], intval($row["twelve"]), intval($row["four"]), intval($row["three"]), intval($row["none"]));
	}

	return json_encode($data);

}


function getData($set, $location, $startingDate = NULL, $endingDate = NULL) {

	include("connect.php");

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
		$data[] = ["date", $setArray[$set]["description"][0], $setArray[$set]["description"][1]];


		foreach($result as $row) {
			$total = (intval($row["good"]) + intval($row["bad"]));
			$good = ($row["good"] / $total) * 100;
			$bad = ($row["bad"] / $total) * 100;
			$data[] = array($row["date"], $good + "%", $bad + "%");
		}
	}

		return json_encode($data);
}

?>