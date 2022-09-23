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

$email=$_REQUEST['email'];
$fbid=$_REQUEST['fbid'];
$latitude=$_REQUEST['latitude'];
$longitude=$_REQUEST['longitude'];
$googleid=$_REQUEST['googleid'];
$regid=$_REQUEST['reg_id'];
$apnid=$_REQUEST['apn_id'];
if(!($fbid || $googleid) && $email && $longitude && $latitude){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+
	if($fbid){
		$sql="select users.id as uid,users.username,users.name,users.email,users.zipcode,users.latitude as u_latitude,users.longitude as u_longitude,users.dob,users.mobile,users.gender, users.image, favorites.venue_id,venue.id as vid, venue.venue_name, venue.address, venue.city,venue.state,venue.paypal_email as paypal,venue.fax_number as fax,venue.parking_information as parking, venue.latitude as v_latitude,venue.longitude as v_longitude,(select group_concat(pictures.url separator ',') from pictures where pictures.venue_id=venue.id and pictures.is_deleted=0) as url,(select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as venue_type,hours_of_operation.days, hours_of_operation.start_time, hours_of_operation.end_time,
	TRUNCATE((((acos(sin((:latitude*pi()/180)) * sin((venue.latitude*pi()/180))+cos((:latitude*pi()/180)) * cos((venue.latitude*pi()/180)) * cos(((:longitude- venue.longitude)* pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance
	 from users left join favorites on favorites.user_id=users.id and favorites.is_deleted=0 left join venue on venue.id=favorites.venue_id and venue.is_live=1 and venue.is_deleted=0 left join hours_of_operation on hours_of_operation.venue_id=venue.id where users.fbid=:fbid or users.email=:email ";
	
	 $sth=$conn->prepare($sql);
	 $sth->bindValue("fbid",$fbid);

	}
	
	elseif($googleid){
		$sql="select users.id as uid,users.username,users.name,users.email,users.zipcode,users.latitude as u_latitude,users.longitude as u_longitude,users.dob,users.mobile,users.gender, users.image, favorites.venue_id,venue.id as vid, venue.venue_name, venue.address, venue.city,venue.state,venue.paypal_email as paypal,venue.fax_number as fax,venue.parking_information as parking, venue.latitude as v_latitude,venue.longitude as v_longitude,(select group_concat(pictures.url separator ',') from pictures where pictures.venue_id=venue.id and pictures.is_deleted=0) as url,(select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as venue_type,hours_of_operation.days, hours_of_operation.start_time, hours_of_operation.end_time,
	TRUNCATE((((acos(sin((:latitude*pi()/180)) * sin((venue.latitude*pi()/180))+cos((:latitude*pi()/180)) * cos((venue.latitude*pi()/180)) * cos(((:longitude- venue.longitude)* pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance
	 from users left join favorites on favorites.user_id=users.id and favorites.is_deleted=0 left join venue on venue.id=favorites.venue_id and venue.is_live=1 and venue.is_deleted=0 left join hours_of_operation on hours_of_operation.venue_id=venue.id where users.googleid=:googleid or users.email=:email ";
	 
	 $sth=$conn->prepare($sql);
	 $sth->bindValue("googleid",$googleid);

	}
	 $sth->bindValue("latitude",$latitude);
	$sth->bindValue("longitude",$longitude);
	$sth->bindValue("email",$email);
	
	
	try{$sth->execute();}catch(Exception $e){
	echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	
	$code=md5($fbid.rand(1,9999999));
	
	if(count($result)){
	
	if($apnid)
	$sql="update users set latitude=:latitude,longitude=:longitude,apn_id=:apnid,token=:token where email=:email";
	elseif($regid)
	$sql="update users set latitude=:latitude,longitude=:longitude,reg_id=:regid,token=:token where email=:email";
	else
	$sql="update users set latitude=:latitude,longitude=:longitude,token=:token where email=:email";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	$sth->bindValue('token',$code);
	if($apnid) $sth->bindValue('apnid',$apnid);
	if($regid) $sth->bindValue('regid',$regid);
	$sth->bindValue("latitude",$latitude);
	$sth->bindValue("longitude",$longitude);
	$count=0;
	try{$count=$sth->execute();}
	catch(Exception $e){}
	if($count){
		$success="1";
		$msg="Login successful";
	
		 
		
		//get profile
			$data['profile']=array(
				"user_id"=>$result[0]['uid'],
				"username"=>$result[0]['username'],
				"name"=>$result[0]['name'],
				"email"=>$result[0]['email'],
				"zipcode"=>$result[0]['zipcode'],
				"dob"=>$result[0]['dob'],
				"mobile"=>$result[0]['mobile'],
				"gender"=>$result[0]['gender'],
				"user_longitude"=>$result[0]['u_longitude']?$result[0]['u_longitude']:"",
				"user_latitude"=>$result[0]['u_latitude']?$result[0]['u_latitude']:"",
				"image"=>$result[0]['image'] ? BASE_PATH."/timthumb.php?src=uploads/".$result[0]['image'] : "",
				"access_token"=>$code,
				"distance"=>$result[0]['distance']?$result[0]['distance']:0
				
			);
			/*
			$length=sizeof($result);
			$data['venue']=array(
			for($i=0;$i<$length;$i++){
				"venue_name"=>$result[$i]['venue_name'],
				"address"=>$result[$i]['address'],
				"city"=>$result[$i]['city'],
				"state"=>$result[$i]['state'],
				"parking"=>$result[$i]['parking'],
				"venue_longitude"=>$result[$i]['v_longitude'],
				"venue_latitude"=>$result[$i]['v_latitude'],
				"paypal"=>$result[$i]['paypal'],
				"fax"=>$result[$i]['fax']
				}
			);
			*/
			foreach($result as $key=>$value){
				if($value['venue_name']){
				$data['venue'][$key]=array('venue_id'=>$value['vid'],
				'venue_name'=>$value['venue_name'],
				'address'=>$value['address'],
				'city'=>$value['city'],
				'state'=>$value['state']?$value['state']:"",
				'parking'=>$value['parking']?$value['parking']:"",
				'venue_longitude'=>$value['v_longitude'],
				'venue_latitude'=>$value['v_latitude'],
				'paypal'=>$value['paypal']?$value['paypal']:"",        		
				'fax'=>$value['fax']?$value['fax']:"",
				'venuetype'=>$value['venue_type'],
				"days"=>$value['days']?$value['days']:"",
				"start_time"=>$value['start_time']?$value['start_time']:"",
				"end_time"=>$value['end_time']?$value['end_time']:"",
				'venue_images'=>$value['url']? BASE_PATH."/timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
				);
				}
			}
			//$data['venue']=array(0=>array(),1=>array());
			
			/*$pictures=explode(',',$result[0]['url']);
				foreach($pictures as $row){
						if($row)
						$data['venue_images'][]=BASE_PATH."/timthumb.php?src=uploads/".$row;
					}*/
			
			
			
		}
		
	else{
			$success="0";
			$msg="Error Occurred";
		}
		}
	else{
		$success="0";
		$msg="User not registered";
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