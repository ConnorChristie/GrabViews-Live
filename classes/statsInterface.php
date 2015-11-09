<?php
	date_default_timezone_set("America/Chicago");
	
	$mysqli = new mysqli("localhost", "root", "", "yt_viewer");
	$timestamp = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
	
	$today = new DateTime(date("m/d/Y"));
	$seven_date = new DateTime(date("m/d/Y", $timestamp));
	
	$result = fetch_all_assoc($mysqli, "SELECT * FROM analytics");
	
	foreach ($result as $key => $value)
	{
		list($year, $month, $day) = explode("-", $value['date']);
		
		$date = new DateTime(date("m/d/Y", mktime(0, 0, 0, $month, $day, $year)));
		
		if ($date < $seven_date || $date > $today || $date == new DateTime(date("m/d/Y", mktime(0, 0, 0, 12, 31, 9999))))
		{
			unset($result[$key]);
		}
	}
	
	echo json_encode(array_reverse($result));
	
	$mysqli->close();
	
	function fetch_all_assoc($mysqli, $query) {
		$result = $mysqli->query($query);
		$assoc = array();
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$assoc[] = $row;
		}
		
		$result->close();
		
		return $assoc;
	}
?>