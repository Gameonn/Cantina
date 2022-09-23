<?php
class GeneralFunctions
{
	public static function getImagePath($file_name)
	{
		if(!empty($file_name))
		{
			return BASE_PATH."uploads/".$file_name;
				//return BASE_PATH."timthumb.php?src=uploads/".$file_name;
			}
			else
			{
				return BASE_PATH."uploads/default_256.png";
				//return BASE_PATH."timthumb.php?src=uploads/default_256.png";
				
			}
		}
		
	
	
	public static function getBasePath()
	{
	return BASE_PATH."/timthumb.php?src=uploads/";
	}
		
	public static function generateRandomString($length = 10)
	{
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}
?>