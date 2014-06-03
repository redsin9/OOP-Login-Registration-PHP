<?php

function escape($string)
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}



function ParseDates ($data)
{
	$results = array();
	$pattern = '/' . '(\d{2}\/\d{2}\/\d{4} - ){1,2}.+<br\/>' . '/';


	// check if the date string exists
	if (preg_match($pattern, $data, $matches))
	{
		// extract start date - end date - time
		$dates = preg_split('/ - /', $matches[0]);

		$date1 = explode("/", $dates[0]);
		$date2 = array(0,0,0);
		$time = '';

		// check if end date exists
		if (count($dates) == 3)
		{
			$date2 = explode("/", $dates[1]);
			$time = $dates[2];
		}
		else
		{
			$time = $dates[1];
		}

		$time = substr($time, 0, strpos($time, "<br/>"));

		$results = array 
		(
			'start' => array
			(
				'day' => $date1[0],
				'month' => $date1[1],
				'year' => $date1[2]
			),

			'end' => array
			(
				'day' => $date2[0],
				'month' => $date2[1],
				'year' => $date2[2]
			),

			'time' => $time
		);
	}

	return $results;
}


?>