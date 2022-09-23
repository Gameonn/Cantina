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
$username=$_REQUEST['username'];
$googleid=$_REQUEST['googleid'];
$email=$_REQUEST['email'];
$name=$_REQUEST['name']?$_REQUEST['name']:'';
$password=isset($_REQUEST['password']) && $_REQUEST['password'] ? $_REQUEST['password'] : null;
$mobile=$_REQUEST['mobile']?$_REQUEST['mobile']:'';
$dob=$_REQUEST['dob']?$_REQUEST['dob']:'';
$zipcode=$_REQUEST['zipcode']?$_REQUEST['zipcode']:'';
$gender=$_REQUEST['gender']?$_REQUEST['gender']:'';
$longitude=$_REQUEST['longitude']?$_REQUEST['longitude']:'';
$latitude=$_REQUEST['latitude']?$_REQUEST['latitude']:'';


 $is_deleted=0;
 

global $conn;

if(!($email || $fbid || $googleid)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

else{ 
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
		if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
			//$success="1";
			$image_path=$randomFileName;
		}
	
	$sql="update users set username=:username,name=:name,zipcode=:zipcode,dob=:dob,mobile=:mobile,gender=:gender where fbid=:fbid or email=:email or googleid=:googleid";
		$sth=$conn->prepare($sql);
		$sth->bindValue("username",$username);
		$sth->bindValue("name",$name);
		$sth->bindValue("zipcode",$zipcode);
		$sth->bindValue("dob",$dob);
		$sth->bindValue("mobile",$mobile);
		$sth->bindValue('gender',$gender);
		$sth->bindValue('fbid',$fbid);
		$sth->bindValue('email',$email);
		$sth->bindValue('googleid',$googleid);
		
		$count1=0;
		try{$count1=$sth->execute();}catch(Exception $e){
		echo $e->getMessage();
		}
		
		if($count1){
		$success="1";
		$msg="Users Successfully updated";
		
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