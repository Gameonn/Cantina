<?php
//this is an api staff_order 

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('OrderClass.php');
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$sid=$_REQUEST['staff_id'];
$vid=$_REQUEST['venue_id'];

$zone=$_REQUEST['zone']?$_REQUEST['zone']:-14400;

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+


$data['open'] = OrderClass::get_orders($sid,$vid,1,$zone)? OrderClass::get_orders($sid,$vid,1,$zone):[];
$data['ready'] = OrderClass::get_orders($sid,$vid,2,$zone)? OrderClass::get_orders($sid,$vid,2,$zone):[];
$data['closed'] = OrderClass::get_orders($sid,$vid,3,$zone)? OrderClass::get_orders($sid,$vid,3,$zone):[];
$data['_void'] = OrderClass::get_orders($sid,$vid,4,$zone)? OrderClass::get_orders($sid,$vid,4,$zone):[];
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($data){
$success='1';
$msg='Records Found';
}
else{
$success=0;
$msg='No Records Found';
}

if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));


?>