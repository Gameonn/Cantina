<?php
require_once('../php_include/db_connection.php');
require_once('../stripe_test/stripe/lib/Stripe.php');
require_once('../stripe_test/stripe/lib/Stripe/Charge.php');
require_once('../stripe_test/stripe/lib/Stripe/Customer.php');
require_once('../stripe_test/stripe/lib/Stripe/ApiRequestor.php');

  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );

$json=json_encode($_POST);

$uid=$_REQUEST['user_id'];
$oid=$_REQUEST['order_id'];
$token=trim($_REQUEST['stripe_token']);
$order_amount=$_REQUEST['order_amount'];
$stripCode = trim($_REQUEST['stripe_token']);


$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
$sth->bindValue('json',$json);
try{$sth->execute();}
catch(Exception $e){
echo $e->getMessage();
}
Stripe::setApiKey($stripe['secret_key']);
  
try{
  $customer = Stripe_Customer::create(array(
      'email' => 'jindal.ankit89@gmail.com',
      'source'  => $stripCode
  ));}
  catch(Exception $e){
  echo $e->getMessage();
  }
	//print_r($customer);
	try{
 $charge = Stripe_Charge::create(array(
    "amount" => $order_amount, // amount in cents
    "customer"=>$customer->id,
    "currency" => "usd",
    "description" => "payinguser@example.com"
  ));
  }
  catch(Exception $e){
  echo $e->getMessage();
  }
 // print_r($charge);
 // echo '<h1>Successfully charged $50.00!</h1>';

	//$resp = Stripe_Charge::retrieve($charge->id);
  	//$rt=Stripe_Charge::retrieve($charge->id);
  
	  $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$charge);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
  
   /* $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$resp);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}*/
  
echo json_encode(array('success'=>1,'msg'=>'bhej diya kya!!'));
?>