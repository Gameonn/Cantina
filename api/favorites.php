<?php
//this is an api to login users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$username=$_REQUEST['username'];
$latitude=$_REQUEST['latitude'];
$longitude=$_REQUEST['longitude'];

if(!($username && $latitude && $longitude )){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+
	$sql="select users.id,favorites.venue_id, venue.venue_name, venue.address,venue.mobile, venue.city,venue.state,venue.paypal_email as paypal,venue.fax_number as fax,venue.parking_information as parking, venue.latitude as v_latitude,venue.longitude as v_longitude,pictures.url,venuetype.type, hours_of_operation.days, hours_of_operation.start_time, hours_of_operation.end_time,
	TRUNCATE((((acos(sin((:latitude*pi()/180)) * sin((venue.latitude*pi()/180))+cos((:latitude*pi()/180)) * cos((venue.latitude*pi()/180)) * cos(((:longitude- venue.longitude)* pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance
	 from users left join favorites on favorites.user_id=users.id and favorites.is_deleted=0 left join venue on venue.id=favorites.venue_id and venue.is_live=1 and venue.is_deleted=0 left join pictures on pictures.venue_id=venue.id left join venuetype on venuetype.id=venue.venuetype_id left join hours_of_operation on hours_of_operation.venue_id=venue.id where users.username=:username and users.is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue("username",$username);
	$sth->bindValue("latitude",$latitude);
	$sth->bindValue("longitude",$longitude);
	
	try{$sth->execute();}catch(Exception $e){$e->getMessage();}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	
	if(count($result) ){
	
		$success=1;
		$msg="Favorite Venues";
	
		 
		
		//get profile
			$data['profile']=array(
				"user_id"=>$result[0]['id'],
				"distance"=>$result[0]['distance']?$result[0]['distance']:0
				
			);
			
			foreach($result as $key=>$value){
				if($value['venue_name']){
				$final['venue'][$key]=array('venue_name'=>$value['venue_name'],
							'address'=>$value['address'],
							'city'=>$value['city'],
							'state'=>$value['state']?$value['state']:"",
							'mobile'=>$value['mobile']?$value['mobile']:"",
							'parking'=>$value['parking']?$value['parking']:"",
							'venue_longitude'=>$value['v_longitude'],
							'venue_latitude'=>$value['v_latitude'],
							'paypal'=>$value['paypal']?$value['paypal']:"",        			
							'fax'=>$value['fax']?$value['fax']:"",
							'venuetype'=>$value['type'],
							"days"=>$value['days']?$value['days']:"",
							"start_time"=>$value['start_time']?$value['start_time']:"",
							"end_time"=>$value['end_time']?$value['end_time']:"",
							'venue_images'=>$value['url']? BASE_PATH."/timthumb.php?src=uploads/".$value['url']:BASE_PATH."/timthumb.php?src=uploads/abt-us-sdimg.jpg"
				);
				}
			}
			if($final['venue']){
			foreach($final['venue'] as $key=>$value){
			$data['venue'][]=$value;
			}
			}
			//$data['venue']=array(0=>array(),1=>array());
			
			/*$pictures=explode(',',$result[0]['url']);
				foreach($pictures as $row){
				if($row)
						$data['venue_images'][]=BASE_PATH."/timthumb.php?src=uploads/".$row;
					}*/
			
			
			
		}
		


}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>