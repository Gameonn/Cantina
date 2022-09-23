<?php
require_once("../php_include/db_connection.php");

$username=$_REQUEST['username'];
$regid=$_REQUEST['regid'];
$token=$_REQUEST['token'];

if(!($username && $regid && $token) )
{
	global $conn;
	
	
	$cmp=substr_compare($username,bgmb_,0,5,FALSE);
	
	if($cmp==0){
	$sql="UPDATE `staff` SET regid=:regid WHERE username=:username and token=:token";
	$sth=$conn->$prepare($sql);
	$sth->bindValue('regid',$regid);
	$sth->bindValue('username',$username);
	$sth->bindValue('token',$token);
	
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	}
	
	else{
	$sql="UPDATE `users` SET reg_id=:regid WHERE username=:username and token=:token";
	$sth=$conn->$prepare($sql);
	$sth->bindValue('regid',$regid);
	$sth->bindValue('username',$username);
	$sth->bindValue('token',$token);
	
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	}
	echo json_encode(array('success'=>'1','msg'=>'Updated'));
	
}
else
{
	echo json_encode(array('success'=>'0','msg'=>'Incomplete Parameters'));
}

?>