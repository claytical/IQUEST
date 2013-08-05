<?php
$dataSet = $_REQUEST['set'];
$neighborhood = $_REQUEST['neighborhood'];
$startDate = $_REQUEST['start'];
$endDate = $_REQUEST['end'];
echo getData($dataSet, $neighborhood, $startDate, $endDate);

function getData($set, $location, $startingDate = NULL, $endingDate = NULL) {

	include("connect.php");

	$setArray = array(
						"water" => array(
										"values" => ["C", "D"],
										"description" => ["Clear", "Discolored or cloudy"]
										),
						"particles" => array(
										"values" => ["N", "P"],
										"description" => ["No", "Yes"]
										),
						"taste" => array(
										"values" => ["G", "B"],
										"description" => ["Tastes good, no odor", "Bad taste or odor"]
										),
						"hours" => array(
										"values" => ["H", "M", "L", "Z"],
										"description" => ["Over 12 hours", "4-12 hours", "Up to 3 hours", "No flow"]
										)
						);


/*
	$setArray = array(
						"water" => ["D", "D"],
						"particles" => ["N", "P"],
						"taste" => ["G", "B"],
						"hours" => ["H", "M", "L", "Z"]);

*/
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
			$data[] = array($row["date"], intval($row["good"]), intval($row["bad"]));
		}
	}

/*
	else {

		$query .= "SELECT SUM(twelve) as twelve, SUM(four) as four, SUM(three) as three, SUM(none) as none, date FROM (";
		foreach ($setArray[$set] as $key => $value) {
			if ($key == 0) {
				$query .= " SELECT COUNT($set) as good, 0 as bad, date FROM finalData WHERE $set = '$value' AND date > '$startingDate' AND date < '$endingDate' GROUP BY date ";
			}
			else {

				$query .= " UNION SELECT 0 as good, COUNT($set) as bad, date FROM finalData WHERE $set = '$value' AND date > '$startingDate' AND date < '$endingDate' GROUP BY date ";

			}

		}

		$query .= ") as tmpTable GROUP BY date";
		$result = $mysqli->query($query);
		$data[] = ["date", "good", "bad"];


		foreach($result as $row) {
			$data[] = array($row["date"], $row["good"], $row["bad"]);
		}

	}	
*/


		return json_encode($data);
}

?>