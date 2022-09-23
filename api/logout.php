<?php
//this is an api to logout users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$access_token=$_REQUEST['access_token'];
$username=$_REQUEST['username'];

if(!($access_token )){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sql="select id from users where token=:access_token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("access_token",$access_token);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll();
	$uid=$result[0]['id'];
}

if($uid){
	$sql="update users set token=:access_token,reg_id='',apn_id='' where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("access_token",'');
	$sth->bindValue("id",$uid);
	$count=0;
	try{$count=$sth->execute();}catch(Exception $e){}
	if($count){
		$success="1";
		$msg="Logout successfully";
	}else{
		$success="0";
		$msg="Error occurred";
	}
}
else{
	$success="1";
	$msg="Invalid access token";
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