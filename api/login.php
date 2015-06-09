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
$password=$_REQUEST['password'];
$latitude=$_REQUEST['latitude'];
$longitude=$_REQUEST['longitude'];
$regid=$_REQUEST['reg_id']?$_REQUEST['reg_id']:"";
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:"";

if(!($username && $password && $latitude && $longitude )){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$cmp=substr_compare($username,bgmb_,0,5,FALSE);
	
	if($cmp==0){
	$sql="SELECT staff.*,staff.id as uid,venue.*,venue.id as vid,pictures.url,venuetype.type,hours_of_operation.days,hours_of_operation.start_time,hours_of_operation.end_time FROM `staff` left join `venue` on venue.id=staff.venue_id and venue.is_live=1 and venue.is_deleted=0 left join pictures on pictures.venue_id=venue.id and pictures.is_deleted=0 left join venuetype on venuetype.id=venue.venuetype_id left join hours_of_operation on hours_of_operation.venue_id=venue.id where staff.username=:username and staff.password=:password and staff.is_live=1 and staff.is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue("username",$username);
	$sth->bindValue("password",md5($password));
	try{$sth->execute();}catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	//print_r($res);die;
	$code=md5($username.rand(1,9999999));
	if(count($res)){
	
	if($apnid)	
	$sth=$conn->prepare("update staff set online=1,token=:token,apnid=:apnid where username=:username");
	elseif($regid)
	$sth=$conn->prepare("update staff set online=1,token=:token,regid=:regid where username=:username");
	else
	$sth=$conn->prepare("update staff set online=1,token=:token where username=:username");
	
	$sth->bindValue('username',$username);
	$sth->bindValue('token',$code);
	if($apnid) $sth->bindValue('apnid',$apnid);
	if($regid) $sth->bindValue('regid',$regid);
	$coun=0;
	try{$coun=$sth->execute();}
	catch(Exception $e){}
	if($coun){
		$success="1";
		$msg="Login successful";
	
		//get profile
			$is_staff='1';
			$data['profile']=array(
				"user_id"=>$res[0]['uid'],
				"username"=>$res[0]['username'],
				//"name"=>$res[0]['name'],
				"email"=>$res[0]['email'],
				"mobile"=>$res[0]['mobile'],
				"apnid"=>$apnid ? $apnid : ($res[0]['apnid']?$res[0]['apnid']:""),
				"regid"=>$regid ? $regid : ($res[0]['regid']?$res[0]['regid']:""),
				"access_token"=>$code,
				"flag"=>'staff'
				);

					$data['office']=array('venue_id'=>$res[0]['vid'],
					'venue_name'=>$res[0]['venue_name'],
					'address'=>$res[0]['address'],
					'city'=>$res[0]['city'],
					'mobile'=>$res[0]['mobile']?$res[0]['mobile']:"",
					'state'=>$res[0]['state']?$res[0]['state']:"",
					'venue_longitude'=>$res[0]['longitude'],
					'venue_latitude'=>$res[0]['latitude'],
					'paypal'=>$res[0]['paypal']?$res[0]['paypal']:"",  
					'contact_email'=>$res[0]['contact_email']?$res[0]['contact_email']:"",
					'website'=>$res[0]['website']?$res[0]['website']:"",      		
					'fax'=>$res[0]['fax']?$res[0]['fax']:"",
					'venuetype'=>$res[0]['type'],
					"days"=>$res[0]['days']?$res[0]['days']:"",
					"start_time"=>$res[0]['start_time']?$res[0]['start_time']:"",
					"end_time"=>$res[0]['end_time']?$res[0]['end_time']:"",					
					'venue_images'=>$res[0]['url']?BASE_PATH."timthumb.php?src=uploads/".$res[0]['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
					);

			//$data['venue']=array(0=>array(),1=>array());
			/*if($final['venue']){
			foreach($final['venue'] as $m=>$n){
			$data['venue'][]=$n;
			}
			}*/
		}
		
	else{
			$success="0";
			$msg="Error Occurred";
		}
		}
	else{
		$success="0";
		$msg="Invalid username or password";
	}
	}
	
	else{
	$sql="select users.id as uid,users.username,users.name,users.email,users.zipcode,users.dob,users.mobile,users.gender, users.image, favorites.venue_id, venue.id as vid,venue.venue_name, venue.address,venue.mobile, venue.city,venue.state,venue.paypal_email as paypal,venue.contact_email,venue.website,venue.fax_number as fax,venue.parking_information as parking, venue.latitude as v_latitude,venue.longitude as v_longitude, pictures.url,venuetype.type,hours_of_operation.days,hours_of_operation.start_time,hours_of_operation.end_time,
	TRUNCATE((((acos(sin((:latitude*pi()/180)) * sin((venue.latitude*pi()/180))+cos((:latitude*pi()/180)) * cos((venue.latitude*pi()/180)) * cos(((:longitude- venue.longitude)* pi()/180))))*180/pi())*60*1.1515*1.609344),2) as distance
	 from users left join favorites on favorites.user_id=users.id and favorites.is_deleted=0 left join venue on venue.id=favorites.venue_id and venue.is_live=1 and venue.is_deleted=0 left join pictures on pictures.venue_id=venue.id and pictures.is_deleted=0 left join venuetype on venuetype.id=venue.venuetype_id left join hours_of_operation on hours_of_operation.venue_id=venue.id where users.password=:password and (users.username=:username or users.email=:email) ";
	$sth=$conn->prepare($sql);
	$sth->bindValue("username",$username);
	$sth->bindValue("email",$username);
	$sth->bindValue("latitude",$latitude);
	$sth->bindValue("longitude",$longitude);
	$sth->bindValue("password",md5($password));
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	$code=md5($username.rand(1,9999999));
	
	if(count($result) ){
	
	if($apnid)
	$sql="update users set latitude=:latitude,longitude=:longitude,apn_id=:apnid,token=:token where (username=:username or email=:email)";
	elseif($regid)
	$sql="update users set latitude=:latitude,longitude=:longitude,reg_id=:regid,token=:token where (username=:username or email=:email)";
	else
	$sql="update users set latitude=:latitude,longitude=:longitude,token=:token where (username=:username or email=:email)";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('username',$username);
	$sth->bindValue('email',$username);
	$sth->bindValue('token',$code);
	if($apnid) $sth->bindValue('apnid',$apnid);
	if($regid) $sth->bindValue('regid',$regid);
	$sth->bindValue('latitude',$latitude);
	$sth->bindValue('longitude',$longitude);
	$count=0;
	try{$count=$sth->execute();}
	catch(Exception $e){echo $e->getMessage();}

	if($count){
		$success="1";
		$msg="Login successful";
	
		//get profile
			$is_staff='0';
			$data['profile']=array(
				"user_id"=>$result[0]['uid'],
				"username"=>$result[0]['username'],
				"name"=>$result[0]['name'],
				"email"=>$result[0]['email'],
				"zipcode"=>$result[0]['zipcode']?$result[0]['zipcode']:"",
				"dob"=>$result[0]['dob']?$result[0]['dob']:"",
				"mobile"=>$result[0]['mobile']?$result[0]['mobile']:"",
				"access_token"=>$code,
				"flag"=>'user',
				"apnid"=>$apnid ? $apnid : ($res[0]['apnid']?$res[0]['apnid']:""),
				"regid"=>$regid ? $regid : ($res[0]['regid']?$res[0]['regid']:""),
				"gender"=>$result[0]['gender'],
				"user_longitude"=>$longitude,
				"user_latitude"=>$latitude,
				"image"=>$result[0]['image'] ? BASE_PATH."timthumb.php?src=uploads/".$result[0]['image'] : BASE_PATH."timthumb.php?src=uploads/Generic_profile_M.jpg"	
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
					$final['venue'][$key]=array('venue_id'=>$value['vid'],
					'venue_name'=>$value['venue_name'],
					'address'=>$value['address'],
					'city'=>$value['city'],
					'mobile'=>$value['mobile']?$value['mobile']:"",
					'state'=>$value['state']?$value['state']:"",
					'parking'=>$value['parking']?$value['parking']:"",
					'venue_longitude'=>$value['v_longitude'],
					'venue_latitude'=>$value['v_latitude'],
					'paypal'=>$value['paypal']?$value['paypal']:"",
					'contact_email'=>$value['contact_email']?$value['contact_email']:"",
					'website'=>$value['website']?$value['website']:"",        		
					'fax'=>$value['fax']?$value['fax']:"",
					'venuetype'=>$value['type'],
					"distance"=>$value['distance']?$value['distance']:0,
					"days"=>$value['days']?$value['days']:"",
					"start_time"=>$value['start_time']?$value['start_time']:"",
					"end_time"=>$value['end_time']?$value['end_time']:"",					
					'venue_images'=>$value['url']?BASE_PATH."timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
					);
				}
				
				/*$pictures=explode(',',$result[0]['url']);
				foreach($pictures as $row){
					if($row)
					$data['venue'][$key]['venue_images'][]=BASE_PATH."timthumb.php?src=uploads/".$row;
				}*/
					
			}
			//$data['venue']=array(0=>array(),1=>array());
			if($final['venue']){
			foreach($final['venue'] as $key=>$value){
			$data['venue'][]=$value;
			}
			}
		
			
			
			
		}
		
	else{
			$success="0";
			$msg="Error Occurred";
		}
		}
	else{
		$success="0";
		$msg="Invalid email or password";
	}

}
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"is_staff"=>$is_staff, "data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>