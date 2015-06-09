<?php

require_once("../php_include/config.php");
require_once("../GCM.php");

$reg_ids[]='APA91bFtq1RfR5d6ZFBewaytCpIacdRzKTaGrATdyytxQkOacDAaLSb7IohawC7_c6fokY0580NMar_roQ6b16CYLymvsb0r_UxIsVv11jc6axTYyFCRoKM7-mG7Ltz6derxlBXsLq3U';

if(!empty($reg_ids)){
	$push_data=array('push_type'=>'6','data'=>array('message'=>'Dummy push to gcm user'));
		try{
			
			GCM::send_notification($reg_ids,$push_data);
			
		}catch(Exception $e){
		//echo $e->getMessage();
		}
}