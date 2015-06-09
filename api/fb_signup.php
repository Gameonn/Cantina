<?php
//this is an api to register users on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
//random file name generator
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$fbid=$_REQUEST['fbid'];
$googleid=$_REQUEST['googleid'];
$email=$_REQUEST['email'];
$name=$_REQUEST['name']?$_REQUEST['name']:'';
$image=$_FILES['image'];
$password=isset($_REQUEST['password']) && $_REQUEST['password'] ? $_REQUEST['password'] : null;
$mobile=$_REQUEST['mobile']?$_REQUEST['mobile']:'';
$dob=$_REQUEST['dob']?$_REQUEST['dob']:'';
$zipcode=$_REQUEST['zipcode']?$_REQUEST['zipcode']:'';
$gender=$_REQUEST['gender']?$_REQUEST['gender']:'';
$longitude=$_REQUEST['longitude']?$_REQUEST['longitude']:'';
$latitude=$_REQUEST['latitude']?$_REQUEST['latitude']:'';
$username=$_REQUEST['username'];
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:"";
$regid=$_REQUEST['reg_id']?$_REQUEST['reg_id']:"";
$deviceid=$_REQUEST['device_id']?$_REQUEST['device_id']:"";

 $is_deleted=0;
 

global $conn;

if(!($email && $username && ($fbid || $googleid) && $image)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

	else{ 
	$sth=$conn->prepare("select * from users where username=:username or email=:email");
	
	$sth->bindValue("email",$email);
	$sth->bindValue('username',$username);
	try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
		$success="0";
		$msg="You are a existing user";
	}		
	
	else{	
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
		if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
			//$success="1";
			$image_path=$randomFileName;
		}
	
	$code=md5($username . rand(1,9999999));
	$sql="insert into users 
	values(DEFAULT,:fbid,:googleid,:apnid,:regid,:deviceid,:username,:name,:email,:password,:zipcode, :dob,:mobile,:image,:gender,:longitude,:latitude,:token,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("username",$username);
		if($fbid)$sth->bindValue("fbid",$fbid);
		else $sth->bindValue("fbid","");
		if($googleid)$sth->bindValue("googleid",$googleid);
		else $sth->bindValue("googleid","");		
		$sth->bindValue("apnid",$apnid);
		$sth->bindValue("regid",$regid);
		$sth->bindValue("deviceid",$deviceid);
		$sth->bindValue("name",$name);
		$sth->bindValue("email",$email);
		$sth->bindValue("password","");
		$sth->bindValue("zipcode",$zipcode);
		$sth->bindValue("dob",$dob);
		$sth->bindValue("mobile",$mobile);
		$sth->bindValue("image",$image_path);
		$sth->bindValue('gender',$gender);
		$sth->bindValue('longitude',$longitude);
		$sth->bindValue('latitude',$latitude);
		$sth->bindValue('token',$code);
		$count1=0;
		try{$count1=$sth->execute();}
		catch(Exception $e){
		echo $e->getMessage();
		}

		
		if($count1){
		$success="1";
		$msg="Users Successfully registered";
		$data=$code;
		}
	
	}	
}
if(!$code){$code="";}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"access_token"=>$code));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>