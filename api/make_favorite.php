<?php
//this is an api category

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$vid=$_REQUEST['venue_id'];
$uid=$_REQUEST['user_id'];


// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

$sql="select venue_id from favorites where user_id=:user_id and venue_id=:venue_id and is_deleted=0";
$sth=$conn->prepare($sql);
$sth->bindValue("user_id",$uid);
$sth->bindValue("venue_id",$vid);
$c1=0;//c1 is variable denoting the execution of above sql query
try{$c1=$sth->execute();}
catch(Exception $e){}
$r=$sth->fetchAll(PDO::FETCH_ASSOC);

if(count($r)){
$success=1;
$msg='Venue already in favorites list';

}
else{

$sql="insert into favorites values(DEFAULT,:venue_id,:user_id,0,NOW())";
$sth=$conn->prepare($sql);
$sth->bindValue('venue_id',$vid);
$sth->bindValue('user_id',$uid);
$count=0;
try{$count=$sth->execute();}
catch(Exception $e){echo $e->getMessage();}

if($count){
	$sql="select users.username,users.id,users.latitude,users.longitude, favorites.venue_id, venue.venue_name, venue.address, venue.city,venue.state,venue.paypal_email as paypal,venue.fax_number as fax,venue.parking_information as parking, venue.latitude as v_latitude,venue.longitude as v_longitude,pictures.url,(select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as venue_type,
	TRUNCATE((((acos(sin((users.latitude*pi()/180)) * sin((venue.latitude*pi()/180))+cos((users.latitude*pi()/180)) * cos((venue.latitude*pi()/180)) * cos(((users.longitude- venue.longitude)* pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance
	 from users join favorites on users.id=favorites.user_id and favorites.is_deleted=0 join venue on favorites.venue_id=venue.id left join pictures on pictures.venue_id=venue.id where user_id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("user_id",$uid);
	try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result) ){
	

		$success="1";
		$msg="Favorite list updated";
	
		 
		
		//get profile
			$data['profile']=array(
				"username"=>$result[0]['username'],
				"distance"=>$result[0]['distance']
				
			);
		
			foreach($result as $key=>$value){
			
				$data['venue'][$key]=array('venue_name'=>$value['venue_name'],'address'=>$value['address'],'city'=>$value['city'],'state'=>$value['state'],
				'parking'=>$value['parking'],'venue_longitude'=>$value['v_longitude'],'venue_latitude'=>$value['v_latitude'],'paypal'=>$value['paypal'],        			'fax'=>$value['fax'],'venuetype'=>$value['venue_type'],
				'venue_image'=>$value['url']? BASE_PATH."timthumb.php?src=uploads/".$value['url']:""
				);
			}
			//$data['venue']=array(0=>array(),1=>array());
			
			/*$pictures=explode(',',$result[0]['url']);
				foreach($pictures as $row){
						$data['venue_images'][]=BASE_PATH."/timthumb.php?src=uploads/".$row;
					}*/
			
			
			
		
		

	}

}
else{
$success=0;
$msg="Error occured";
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