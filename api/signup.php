<?php
//this is an api to register users on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
//error_reporting(E_ALL);
require_once("../php_include/db_connection.php");
require_once("../php_include/GeneralFunctions.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
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
$username=$_REQUEST['username'];
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
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:"";
$regid=$_REQUEST['reg_id']?$_REQUEST['reg_id']:"";
$deviceid=$_REQUEST['device_id']?$_REQUEST['device_id']:"";

 $is_deleted=0;

global $conn;

if(!($username && $email && $password)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}


	else{

if($image){
$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
		if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
			$image_path=$randomFileName;
	}
	}
	else{
	$image_path="";
	}
 
	$sth=$conn->prepare("select * from users where username=:username or email=:email");
	$sth->bindValue("username",$username);
	$sth->bindValue("email",$email);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	if(count($result)){
		$success="0";
		$u=strcasecmp($username,$result[0]['username']);
		if(!$u){
		$msg="Username is already taken";
			}
		else{
			$msg="Email is already registered";
			}
		
		
	//delete image
	if($image_path){ if(@unlink("../uploads/$image_path")){}}
	}
	else{	
	$success='1';	
	$code=md5($username . rand(1,9999999));
	$sql="insert into users values(DEFAULT,:fbid,:googleid,:apnid,:regid,:deviceid,:username,:name,:email,:password,:zipcode,:dob,:mobile,:image,:gender,:longitude,:latitude,:token,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("username",$username);
		$sth->bindValue("fbid",'');
		$sth->bindValue("googleid",'');
		$sth->bindValue("apnid",$apnid);
		$sth->bindValue("regid",$regid);
		$sth->bindValue("deviceid",$deviceid);
		$sth->bindValue("name",$name);
		$sth->bindValue("email",$email);
		$sth->bindValue("password",md5($password));
		$sth->bindValue("zipcode",$zipcode);
		$sth->bindValue("dob",$dob);
		$sth->bindValue("mobile",$mobile);
		$sth->bindValue("image",$image_path);
		$sth->bindValue('gender',$gender);
		$sth->bindValue('longitude',$longitude);
		$sth->bindValue('latitude',$latitude);
		$sth->bindValue('token',$code);
		
		
		$count1=0;
		try{$count1=$sth->execute();}catch(Exception $e){echo $e->getMessage();}
		
		if($count1)
		$msg="User Successfully registered";
		$data=$code;
		$subject_msg="Thank you for registration! <br> Your are Welcomed to Gambay System.";
		$body_msg="You are successfully registered!";
		GeneralFunctions::sendEmail($email, $subject_msg, $body_msg, SMTP_EMAIL);
		//mail($email, $subject_msg, $body_msg, SMTP_EMAIL);
		
		}
	
	}	

	if(!$code){$code="";}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"access_token"=>$code));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
