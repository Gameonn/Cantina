<?php
//this is an api history 

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('OrderClass.php');

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$uid=$_REQUEST['user_id'];
$zone=$_REQUEST['zone']?$_REQUEST['zone']:19800;


// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

$data['history'] = OrderClass::get_users_orders($uid,$zone)? OrderClass::get_users_orders($uid,$zone):[];

if($data['history']){
$success='1';
$msg='Records Found';
}
else{
$success=0;
$msg='No Records Found';
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