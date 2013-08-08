<?php

$dataSet = $_REQUEST['set'];
$neighborhood = $_REQUEST['neighborhood'];
$startDate = $_REQUEST['start'];
$endDate = $_REQUEST['end'];
$usePercentage = FALSE;

if ($dataSet == "index") {
	echo getIndex($neighborhood, $startDate, $endDate);
}
if ($dataSet == "flow") {
	echo getFlowData($neighborhood, $startDate, $endDate, TRUE);
}
if ($dataSet == "water" || $dataSet == "particles" || $dataSet == "taste") {
	echo getData($dataSet, $neighborhood, $startDate, $endDate);
}

if ($dataSet == "location") {
	echo getLocationData($neighborhood, $startDate, $endDate);
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

function getIndex($location, $start, $end) {
	include("connect.php");
	$points = array("water" => 		array("C" => 1, "D" => 0),
					"particles" => 	array("N" => 1, "P" => 0),
					"taste" =>		array("G" => 1, "B" => 0),
					"hours" =>		array("H" => 3, "L" => 2, "M" => 1, "Z" => 0));

	if ($location == "all") {
		$location_query = "SELECT id, location FROM locations ORDER BY location ASC";
		$locations = $mysqli->query($location_query);
		$data = array();
		foreach ($locations as $location) {
			$index_query = "SELECT water, particles, taste, hours FROM finalData WHERE location = " .$location["id"]." AND date >= '$start' AND date <= '$end'";
			$result = $mysqli->query($index_query);
			$total = $result->num_rows;
			$index = 0;
			foreach($result as $row) {
				$index += $points["water"][$row["water"]];
				$index += $points["particles"][$row["particles"]];
				$index += $points["taste"][$row["taste"]];
				$index += $points["hours"][$row["hours"]];

			}
			$data[] = [$location["location"], round($index/($total*4) * 100,2)];

		}
		return json_encode($data);
	}
	else {
		$index_query = "SELECT water, particles, taste, hours FROM finalData WHERE location = $location AND date >= '$start' AND date <= '$end'";
		$result = $mysqli->query($index_query);
		$total = $result->num_rows;
		$index = 0;
		foreach($result as $row) {
			$index += $points["water"][$row["water"]];
			$index += $points["particles"][$row["particles"]];
			$index += $points["taste"][$row["taste"]];
			$index += $points["hours"][$row["hours"]];

		}
	
	return round($index/($total*4) * 100,2);

}
//	0 to 6


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
	$data->headers[] = ["type" => "date", "label" => "Date"];
	foreach ($result as $row) {
		$data->headers[] = ["type" => "number", "label" => $row["location"]];
	}

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

function getLocationData($loc_num, $start = '2000-01-01', $end = '2000-01-01') {
	include("connect.php");
	$location = new stdClass;
	$customer_query = "SELECT DISTINCT(number) as customers, COUNT(water) as reports from finalData WHERE location = $loc_num AND date >= '$start' AND date <= '$end' GROUP BY customers";
	$result = $mysqli->query($customer_query);
	$location->customers = $result->num_rows;
	$unique_reports = array();
	foreach($result as $row) {
		$unique_reports[] = $row["reports"];
	}
	$report_query = "SELECT water as reports, date, locations.location FROM finalData INNER JOIN locations ON locations.id = finalData.location WHERE finalData.location = $loc_num AND date >= '$start' AND date <= '$end' ORDER BY date ASC";
	$result = $mysqli->query($report_query);
	$row = $result->fetch_array();
	$location->name = $row["location"];
	$location->reports = $result->num_rows;
	$location->collection_start = $row["date"];
	$water_query = "SELECT water ";
//	most common water color, hours of flow, particles, taste
/*$c = array_count_values($stuff); 
$val = array_search(max($c), $c);
*/
	$location->median = calculate_median($unique_reports);
	$location->average = round($location->reports / $location->customers, 2);

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
	$sum_total_query = "SELECT SUM(twelve) as twelve, SUM(four) as four, SUM(three) as three, SUM(none) as none FROM (" . $query . ") as tmpTable2";
	$result = $mysqli->query($query);
	$data->headers = [["type" => "date", "label" => "Date"],
					  ["type" => "number", "label" => "Over 12 Hours"],					
					  ["type" => "number", "label" => "4-12 Hours"],					
					  ["type" => "number", "label" => "Up to 3 Hours"],
					  ["type" => "number", "label" => "No Flow"]];			

	$descriptions = ["Over 12 Hours", "4-12 Hours", "Up to 3 Hours", "No Flow"];

	if ($percentage) {
		foreach($result as $row) {
			$total = (intval($row["twelve"]) + intval($row["four"]) + intval($row["three"]) + intval($row["none"]));
			$twelve = round(($row["twelve"] / $total) * 100,2);
			$four = round(($row["four"] / $total) * 100,2);
			$three = round(($row["three"] / $total) * 100,2);
			$none = round(($row["none"] / $total) * 100,2);
			$data->values[] = array($row["date"], $twelve, $four, $three, $none);

		}
	}
	else {
		foreach($result as $row) {

			$data->values[] = array($row["date"], intval($row["twelve"]), intval($row["four"]), intval($row["three"]), intval($row["none"]));
		}
	}

	$result = $mysqli->query($sum_total_query);
	$row = $result->fetch_array();
	$highestCount = -1;
	$i = 0;
	$pos = 999;
	$values[] = $row['twelve'];
	$values[] = $row['four'];
	$values[] = $row['three'];
	$values[] = $row['none'];
	foreach($values as $value) {
		if ($value > $highestCount) {
			$pos = $i;
			$highestCount = $value;
		}
		$i++;
	}
	$data->sums = $values;
	$data->highest = $descriptions[$pos];
//	$data->totals = $descriptions;


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
										"description" => ["Not Present", "Present"]
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

		$goodCount = 0;
		$badCount = 0;
		if ($percentage) {
			foreach($result as $row) {
				$total = (intval($row["good"]) + intval($row["bad"]));
				$good = round(($row["good"] / $total) * 100,2);
				$bad = round(($row["bad"] / $total) * 100,2);
				$data->values[] = array($row["date"], $good + "%", $bad + "%");
				if ($good < $bad) {
					$badCount++;
				}
				else {
					$goodCount++;
				}
			}
		}
		else {
			foreach($result as $row) {
				$data->values[] = array($row["date"], $row["good"], $row["bad"]);
				if ($row["good"] < $row["bad"]) {
					$badCount++;
				}
				else {
					$goodCount++;
				}
			}

		}

		if ($goodCount < $badCount) {
			$data->highest = $setArray[$set]["description"][1];
		}
		else {
			$data->highest = $setArray[$set]["description"][0];

		}
	}

		return json_encode($data);
}

function calculate_median($arr) {
    sort($arr);
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $arr[$middleval];
        $high = $arr[$middleval+1];
        $median = (($low+$high)/2);
    }
    return $median;
}

?>