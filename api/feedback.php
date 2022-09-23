<?php
//this is an api to get feedback of users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$venue_id=$_REQUEST['venue_id'];
$user_id=$_REQUEST['user_id'];
$feedback=$_REQUEST['feedback'];

if(!($venue_id && $user_id && $feedback)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sql="insert into feedback values(DEFAULT,:user_id,:venue_id,:comments,0,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue("user_id",$user_id);
	$sth->bindValue("venue_id",$venue_id);
	$sth->bindValue("comments",$feedback);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	$success='1';
	$msg="Thank You for your feedback";
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