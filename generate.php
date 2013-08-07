<?php

	generate_sms(573, 1000);
	function generate_sms($location, $amount) {
		include ("connect.php");

		$report = array("water" => ["C", "D"],
			  			"particles" => ["N", "P"],
			  			"taste" => ["G", "B"],
			  			"hours" => ["H", "M", "L", "Z"]);

		$start = 0;
		while ($start <= $amount) {
			$w = array_rand($report["water"], 1);
			$water = $report["water"][$w];
			$p = array_rand($report["particles"], 1);
			$particles = $report["particles"][$p];
			$t = array_rand($report["taste"], 1);
			$taste = $report["taste"][$t];
			$h = array_rand($report["hours"], 1);
			$hours = $report["hours"][$h];
			$date = randomDate("2012-01-01", "2013-12-31");
			$number = rand(1000000000, 1100000000);
			$query = "INSERT INTO finalData (water, particles, taste, hours, date, number, location) VALUES ('$water', '$particles', '$taste', '$hours', '$date', '$number', $location)";
			$mysqli->query($query);

    		$start++;  
		}


		echo "Created $amount entries for $location";
	}

	function randomDate($start_date, $end_date)
	{
	    // Convert to timetamps
	    $min = strtotime($start_date);
	    $max = strtotime($end_date);

	    // Generate random number using above bounds
	    $val = rand($min, $max);

	    // Convert back to desired date format
	    return date('Y-m-d', $val);
	}


?>