<?php
//this is an api to update order status users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('OrderClass.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'root', 'codebrew2015', 'codebrew_gambay');
$db->show_errors();
error_reporting(E_ALL);
require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php');
$success=$msg="0";$data=array();
 $stripe = array(
    'secret_key'      => 'sk_test_bITZkbA6UzmyqM9cBxEVqTUB',
    'publishable_key' => 'pk_test_6nHoBlC2mNhg5JHXWqXW9vsP'
    );

	\Stripe\Stripe::setApiKey($stripe['secret_key']);

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$status=$_REQUEST['status'];
$order_id=$_REQUEST['order_id'];
$vid=$_REQUEST['venue_id'];
$staff_id=$_REQUEST['staff_id'];
$zone=$_REQUEST['zone']?$_REQUEST['zone']:19800;



	
	//refund process -- charge and application fee
$sth=$conn->prepare("select stripe_customer.*,stripe_connect.* from stripe_customer left join stripe_connect on stripe_connect.venue_id=stripe_customer.venue_id where stripe_customer.venue_id=:venue_id and stripe_customer.order_id=:order_id");
	$sth->bindValue("venue_id",$vid);
	$sth->bindValue("order_id",$order_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$stripe_result=$sth->fetchAll();
	$charge_id=$stripe_result[0]['charge_id'];
	$fee_id=$stripe_result[0]['fee_id'];
	$acc=$stripe_result[0]['acc_json'];
	$acc=str_replace("Stripe\Account JSON: "," ",$acc);
	$acc= json_decode($acc,true);
	$secr_key=$acc['keys']['secret'];
	
	
	try{
	$ch = \Stripe\Charge::retrieve($charge_id);
	$re = $ch->refunds->create();
	}
	catch(Exception $e){
	echo $e->getMessage();
	}
	echo $ch;die;
		\Stripe\Stripe::setApiKey($stripe['secret_key']);
	try{
	$fee = \Stripe\ApplicationFee::retrieve($fee_id);
	$fee->refund();
	}
	catch(Exception $e){
	echo $e->getMessage();
	}
	
	if($re){
	$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$re);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	if($fee){
	$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$fee);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	
	
	
	

  $p_status=  $p_status?  $p_status:0;
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data,"payment_status"=>$p_status));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
