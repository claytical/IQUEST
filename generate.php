<?php
	$locations = [102, 256, 568,252,279,626];//573, 282, 462, 869, 101];// 
	foreach ($locations as $location) {
		generate_sms($location, rand(500,1500));
	}
//	generate_sms(462, 1000);
	function generate_sms($location, $amount) {
		include ("connect.php");

		$report = array("water" => ["C", "D", "D", "D"],
			  			"particles" => ["N", "P", "N"],
			  			"taste" => ["G", "B", "G", "B", "B"],
			  			"hours" => ["H", "M", "L", "Z", "Z", "L", "L", "L", "L", "M", "M"]);

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
			$number = rand(1, 200);
			$query = "INSERT INTO reports (water, particles, taste, hours, date, number, location) VALUES ('$water', '$particles', '$taste', '$hours', '$date', '$number', $location)";
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