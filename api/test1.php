<?php
//this is an api to update order status users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('OrderClass.php');
/*require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'root', 'codebrew2015', 'codebrew_gambay');
$db->show_errors();*/

require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php');
error_reporting(E_ALL);
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


	$sth=$conn->prepare("select stripe_connect.*,stripe_customer.* from stripe_connect left join stripe_customer on stripe_customer.venue_id=stripe_connect.venue_id where stripe_customer.venue_id=:venue_id and stripe_customer.order_id=:order_id");
	$sth->bindValue("venue_id",$vid);
	$sth->bindValue("order_id",$order_id);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$stripe_res=$sth->fetchAll();
	
	$stripe_token=$stripe_res[0]['stripe_token'];
	$stripe_id=$stripe_res[0]['stripe_id'];
	$order_amount=$stripe_res[0]['order_amount'];
	$gambay_amount=(0.06*$order_amount);
	$order_amount = round($order_amount - $gambay_amount);
	$gambay_amount=round($gambay_amount);

	\Stripe\Stripe::setApiKey($stripe['secret_key']);
 /*try{
	$c_charge = \Stripe\Charge::create(
    array(
      "amount" => $order_amount, // amount in cents
      "currency" => "usd",
      "source" => $stripe_token,
      "description" => "Example charge",
      "application_fee" => $gambay_amount // amount in cents
    ),
    array("stripe_account" => $stripe_id)
  );
  }
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  
  try{
	$chr = \Stripe\Charge::retrieve("ch_15tbPxFsASAfeUDxcxSyzGdi");
	$re=$chr->refunds->create();
	}
	catch(Exception $e){
	echo $e->getMessage();
	}
	echo $chr;echo $re;die;
  /*try{
  \Stripe\Stripe::setApiKey("sk_test_bITZkbA6UzmyqM9cBxEVqTUB");
$bl_t=\Stripe\BalanceTransaction::retrieve("txn_15tZVRFsASAfeUDxxLcWlemI");
  
  }
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  if($charge){
  $st=$charge->status;
  $p_status=($st=="succeeded")?1:0;
  	  $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$charge);
	try{$sth->execute();}
	catch(Exception $e){}
		
  $sql="update stripe_customer set charge_id=:charge_id,fee_id=:fee_id,status=:status where stripe_token=:stripe_token";
  $sth=$conn->prepare($sql);
  $sth->bindValue('charge_id',$charge->id);
  $sth->bindValue('fee_id',$charge->application_fee);
  $sth->bindValue('stripe_token',$stripe_token);
  $sth->bindValue('status',$p_status);
  try{$sth->execute();}
  catch(Exception $e){}
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
