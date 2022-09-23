<?php
//this is an api to update order status users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../php_include/GeneralFunctions.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('OrderClass.php');
require_once('../GCM.php');
require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'root', 'codebrew2015', 'codebrew_gambay');
//$db->show_errors();
require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php');
$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$status=$_REQUEST['status'];
$order_id=$_REQUEST['order_id'];
$vid=$_REQUEST['venue_id'];
$staff_id=$_REQUEST['staff_id'];
$units=$_REQUEST['units_qty']?$_REQUEST['units_qty']:1;
$zone=$_REQUEST['zone']?$_REQUEST['zone']:-14400;

if(!($status && $order_id && $vid && $staff_id)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sth=$conn->prepare("select * from `order` where id=:order_id and venue_id=:venue_id");
	$sth->bindValue("order_id",$order_id);
	$sth->bindValue("venue_id",$vid);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$res=$sth->fetchAll();
	$d_type=$res[0]['delivery_type'];
	
	$sth=$conn->prepare("select * from `gambay_charge` where venue_id=:venue_id");
	$sth->bindValue("venue_id",$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res1=$sth->fetchAll();
	$ce=$res1[0]['charge'];

	$sth=$conn->prepare("select * from staff_order where order_id=:order_id and venue_id=:venue_id");
	$sth->bindValue("order_id",$order_id);
	$sth->bindValue("venue_id",$vid);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$result=$sth->fetchAll();
	$curr_status=$result[0]['status'];
	if(count($result)){

	if($status==2){
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
	$order_amount1=$stripe_res[0]['order_amount'];
	//$gambay_amount=100*(0.03*$order_amount);
	$gambay_amount=$units * $ce;
	$order_amount = round((100*$order_amount1) - $gambay_amount);
	if($order_amount<60 && order_amount1>60){
	$order_amount=60;
	$gambay_amount=(100*$order_amount1)-60;
	}
	$gambay_amount=round($gambay_amount);

	if($order_amount>=60){
	try{ 
	$stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );
	\Stripe\Stripe::setApiKey($stripe['secret_key']);
  	$charge = \Stripe\Charge::create(
    array(
      "amount" => $order_amount, 
      "currency" => "usd",
      "source" => $stripe_token,
      "description" => "Example charge",
      "application_fee" => $gambay_amount
    ),
    array("stripe_account" => $stripe_id)
  );
  }
  catch(Exception $e){
  //echo $e->getMessage();
  }
 
  //$charges=str_replace("Stripe\Charge JSON: "," ",$charge);

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
	  }
	  else{
	  $p_status=1;
	  }
  
    if($p_status){
    $sql="update staff_order set status=:status,staff_id=:staff_id,ready_time=NOW() where order_id=:order_id and venue_id=:venue_id";
  	$sth=$conn->prepare($sql);
	$sth->bindValue("status",$status);
	$sth->bindValue("order_id",$order_id);
	$sth->bindValue("venue_id",$vid);
	$sth->bindValue("staff_id",$staff_id);
	$count=0;
	try{$count=$sth->execute();
	$success="1";
	$msg="order status updated successfully";
	}catch(Exception $e){}
	}
	}

	elseif($status==3)
	$sql="update staff_order set status=:status,staff_id=:staff_id,closed_time=NOW() where order_id=:order_id and venue_id=:venue_id";
	elseif($status==4)
	$sql="update staff_order set status=:status,staff_id=:staff_id,void_time=NOW() where order_id=:order_id and venue_id=:venue_id";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue("status",$status);
	$sth->bindValue("order_id",$order_id);
	$sth->bindValue("venue_id",$vid);
	$sth->bindValue("staff_id",$staff_id);
	$count=0;
	try{$count=$sth->execute();	
	$success="1";
	$msg="order status updated successfully";
	}catch(Exception $e){}


	if($status==4){

	if($curr_status==2){
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
	$publ_key=$acc['keys']['publishable'];
	

	
  try{
  $stripe = array(
    'secret_key'      => $secr_key,
    'publishable_key' => $publ_key
    );
	\Stripe\Stripe::setApiKey($stripe['secret_key']);
	$chr = \Stripe\Charge::retrieve($charge_id);
	$re=$chr->refunds->create();
	}
	catch(Exception $e){
	//echo $e->getMessage();
	}

	try{
		 $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );
	\Stripe\Stripe::setApiKey($stripe['secret_key']);
	$fee = \Stripe\ApplicationFee::retrieve($fee_id);
	$fees=$fee->refund();
	}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	
	if($re){
	$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$re);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	if($fees){
	$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$fees);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	}
	}
	
$data['open'] = OrderClass::get_orders($staff_id,$vid,1,$zone)? OrderClass::get_orders($staff_id,$vid,1,$zone):[];
$data['ready'] = OrderClass::get_orders($staff_id,$vid,2,$zone)? OrderClass::get_orders($staff_id,$vid,2,$zone):[];
$data['closed'] = OrderClass::get_orders($staff_id,$vid,3,$zone)? OrderClass::get_orders($staff_id,$vid,3,$zone):[];
$data['_void'] = OrderClass::get_orders($staff_id,$vid,4,$zone)? OrderClass::get_orders($staff_id,$vid,4,$zone):[];


	//For GCM..................
		$message=array();
    		$reg_ids=array();
    		$message['order_id']=$order_id;
    		if($status==1){
    		$message['msg']="Your order P".$order_id." is currently in progress.";
    		}
    		elseif($status==2){
    		if($d_type=='pick_up')
    		$message['msg']="Your order P".$order_id." is ready for pickup.";
    		else
    		$message['msg']="Your order P".$order_id." is being delivered to Location ". $d_type." shortly.";
    		}
    		elseif($status==3){
    		if($d_type=='pick_up')
    		$message['msg']="Thank you for picking up Order P".$order_id.".Enjoy!";
    		else
    		$message['msg']="Your order P".$order_id." has been delivered. Enjoy!";
    		}
    		elseif($status==4){
    		$message['msg']="Your order P".$order_id." is cancelled.";
    		}
    		
    		$message['type']=1; // type denotes that this push is for user
    		$message['notification_type']=$status;
    		$sql_qry="select `order`.user_id,users.apn_id,users.reg_id from `order` left join users on users.id=`order`.user_id where `order`.id=:order_id";
    		$sth=$conn->prepare($sql_qry);
    		$sth->bindValue("order_id",$order_id);
    		try{$sth->execute();}
    		catch(Exception $e){}
    		$rst2=$sth->fetchAll(PDO::FETCH_ASSOC);
    		
    		foreach($rst2 as $key=>$value){
    			if(!empty($value['reg_id'])){
    		$reg_ids[]=$value['reg_id'];
    			}}
    		

		   foreach($rst2 as $key=>$value){
                        if(!empty($value['apn_id'])){
                        	$all_apns[]=$value['apn_id'];

                        }
                    }

			//print_r($all_apns);die;
			if($p_status){
    			if(!empty($all_apns)){
		
                                $apns->newMessage($all_apns);
                                $apns->addMessageAlert($message['msg']);
                                $apns->addMessageSound('x.wav');
                                $apns->addMessageCustom('order_id', $order_id);
                                $apns->addMessageCustom('type', $message['type']);
                                $apns->addMessageCustom('n_type', $message['notification_type']);
                                $apns->queueMessage();
								$apns->processQueue();
        
   		}
		
		// order_status==2 (ready)
			
			$sql1="SELECT U.email, U.username FROM  `order` O JOIN users U ON U.id = O.user_id WHERE O.id =:order_id";
			$sth1=$conn->prepare($sql1);
			$sth1->bindValue('order_id',$order_id);
			try{$sth1->execute();}
			catch(Exception $e){}
			$res1=$sth1->fetchAll(PDO::FETCH_ASSOC);
			$email=$res1[0]['email'];
			$username=$res1[0]['username'];
			
			$sql1="SELECT * FROM  `order` O JOIN venue V ON V.id = O.venue_id WHERE O.id =:oid";
			$sth1=$conn->prepare($sql1);
			$sth1->bindValue('oid',$order_id);
			try{$sth1->execute();}
			catch(Exception $e){}
			$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
			$venue_name=$res1[0]['venue_name'];
			
			$pdf_page=GeneralFunctions::createPDFForOrderStatus($order_id);
			$subject_msg="Gambay bill - $venue_name - Order #P$order_id";
			$body_msg="Dear $username, <br> Thank you for using Gambay. Your order is ready! <br> Here is the receipt for your records!
			 <br> Thank you very much for shopping.";
			GeneralFunctions::sendEmailWithAttachment($email, $subject_msg, $body_msg, SMTP_EMAIL);
			
			}
			else{
			if($status!=2){
				if(!empty($all_apns)){
		 
                                $apns->newMessage($all_apns);
                                $apns->addMessageAlert($message['msg']);
                                $apns->addMessageSound('x.wav');
                                $apns->addMessageCustom('order_id', $order_id);
                                $apns->addMessageCustom('type', $message['type']);
                                $apns->addMessageCustom('n_type', $message['notification_type']);
                                $apns->queueMessage();
								$apns->processQueue();
      
				}
			}
			
			$sql="SELECT U.email FROM  `order` O JOIN users U ON U.id = O.user_id WHERE O.id =:order_id";
			$sth=$conn->prepare($sql);
			$sth->bindValue('order_id',$order_id);
			try{$sth->execute();}
			catch(Exception $e){}
			$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
			$email=$res1[0]['email'];
			$username=$res1[0]['username'];
			
			$sql1="SELECT * FROM  `order` O JOIN venue V ON V.id = O.venue_id WHERE O.id =:oid";
			$sth1=$conn->prepare($sql1);
			$sth1->bindValue('oid',$order_id);
			try{$sth1->execute();}
			catch(Exception $e){}
			$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
			$venue_name=$res1[0]['venue_name'];
			
			if($status==3)
			{
				$pdf_page=GeneralFunctions::createPDFForOrderStatus($order_id);
				$subject_msg="Gambay bill - $venue_name - Order #P$order_id";
				$body_msg="Dear $username, <br> Thank you for using Gambay. Your order is closed! <br> Here is the receipt for your records! <br> Thank you very much for shopping.";
				GeneralFunctions::sendEmailWithAttachment($email, $subject_msg, $body_msg, SMTP_EMAIL);
			}
			elseif($status==4)
			{
				$pdf_page=GeneralFunctions::createPDFForOrderStatus($order_id);
				$subject_msg="Gambay bill - $venue_name - Order #P$order_id";
				$body_msg="Dear $username, <br> Thank you for using Gambay. Your order was cancelled! <br> Here is the receipt for your records! <br> Thank you very much for shopping.";
				GeneralFunctions::sendEmailWithAttachment($email, $subject_msg, $body_msg, SMTP_EMAIL);
			}
			}
			
			if($p_status){
    		if(!empty($reg_ids)){
    			GCM::send_notification($reg_ids, $message);
    		}
			}
			else{
			if($status!=2){
			if(!empty($reg_ids)){
    			GCM::send_notification($reg_ids, $message);
    		}
			}
			}
    		

}
else{
	$success="1";
	$msg="Order already processed";
	$data['open'] = OrderClass::get_orders($staff_id,$vid,1,$zone)? OrderClass::get_orders($staff_id,$vid,1,$zone):[];
	$data['ready'] = OrderClass::get_orders($staff_id,$vid,2,$zone)? OrderClass::get_orders($staff_id,$vid,2,$zone):[];
	$data['closed'] = OrderClass::get_orders($staff_id,$vid,3,$zone)? OrderClass::get_orders($staff_id,$vid,3,$zone):[];
	$data['_void'] = OrderClass::get_orders($staff_id,$vid,4,$zone)? OrderClass::get_orders($staff_id,$vid,4,$zone):[];
}

$p_status=  $p_status?  $p_status:0;
  
  if($status==2 && $p_status==0){
  $success='0';
  $msg="Payment not succeeded.";
  }
}
  
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data,"payment_status"=>$p_status));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
