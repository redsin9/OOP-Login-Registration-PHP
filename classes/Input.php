<?php

class Input
{
	// check if there is any post and get just submitted
	public static function Exists ($type = 'post')
	{
		switch ($type) 
		{
			case 'post':
				return (!empty($_POST)) ? true : false;
				break;
			
			case 'get':
				return (!empty($_GET)) ? true : false;
				break;

			default:
				return false;		
				break;
		}
	}



	public static function Get($item)
	{
		$value = null;

		if (isset($_POST[$item]))
		{
			$value = $_POST[$item];
		}
		else if (isset($_GET[$item]))
		{
			$value = $_GET[$item];
		}
		
		return $value;
	}
}



?>