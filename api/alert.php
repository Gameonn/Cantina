<?php
//this is an api to for order placing by users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$uid=$_REQUEST['user_id'];
$vid=$_REQUEST['venue_id'];
$oid=$_REQUEST['order_id'];
if(!($uid && $vid && $oid )){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sql="select `order`.*,`order`.created_on as oct,staff_order.* from `order` join staff_order on staff_order.order_id=`order`.id and staff_order.venue_id=`order`.venue_id and staff_order.status=2 where `order`.id=:order_id and `order`.venue_id=:venue_id and `order`.user_id=:user_id and (DATE(`order`.created_on)=CURDATE() or DATE(`order`.created_on)=CURDATE()-1 )";
	$sth=$conn->prepare($sql);
	$sth->bindValue('venue_id',$vid);
	$sth->bindValue('user_id',$uid);
	$sth->bindValue('order_id',$oid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res2=$sth->fetchAll(PDO::FETCH_ASSOC);
		
	if(count($res2)){

		$msg="Records Found";
		$success=1;
		
	foreach($res2 as $row){
	if($row['delivery_type']=='pick_up')
	$order_text='Please pick your order up from the Bar';
	else
	$order_text='Your order with id='. $row["order_id"].' will be delivered shortly at location '.$row["delivery_type"].' Thank You!';
			$data[]=array(
				"order_id"=>$row['order_id'],
				"order_time"=>$row['oct'],
				"order_text"=>$order_text,
				"status"=>'Ready',
				'delivery_type'=>$row['delivery_type']	
			);
			}
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