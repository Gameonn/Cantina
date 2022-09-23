<?php
//this is an api to for order placing by users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../php_include/GeneralFunctions.php");
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
//error_reporting(E_ALL);
require_once('../GCM.php');
//require_once ('../easyapns/apns.php');
//require_once('../easyapns/classes/class_DbConnect.php');
//$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_gambay');
//$db->show_errors();
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$uid=$_REQUEST['user_id'];
$vid=$_REQUEST['venue_id'];
$coupon=$_REQUEST['coupon_code']?$_REQUEST['coupon_code']:'';
$delivery_type=$_REQUEST['delivery_type'];
$bill=$_REQUEST['bill_file'];
$payment_status=$_REQUEST['payment_status']?$_REQUEST['payment_status']:0;
$tip=$_REQUEST['tip']?$_REQUEST['tip']:'';
$order_amount=$_REQUEST['order_amount'];
$stripe_token=$_REQUEST['stripe_token'];
$value=($tip*$order_amount)/100;

$zone=$_REQUEST['zone']?$_REQUEST['zone']:-14400;
if(!($uid && $vid && $delivery_type && $bill && $order_amount)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sql="select * from manager_venue where venue_id=:venue_id and is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('venue_id',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res2=$sth->fetchAll(PDO::FETCH_ASSOC);
	$mid=$res2[0]['manager_id'];
	
	/*$sql="select temp.* from (select staff.*, (select count(staff_order.staff_id) from staff_order where staff_order.venue_id=staff.venue_id and staff_order.staff_id=staff.id and staff_order.status=1 and DATE(`staff_order`.created_on)=CURDATE()) as count from staff where staff.venue_id=:venue_id and staff.online=1 and is_live=1 and is_deleted=0) as temp order by count";
	$sth=$conn->prepare($sql);
	$sth->bindValue('venue_id',$vid);
	try{$sth->execute();}
	catch(Exception $e){echo $e->getMessage();}
	$staff=$sth->fetchAll(PDO::FETCH_ASSOC);
	$stid=$staff[0]['id'];*/
	
	if($coupon){
	$sql="select * from coupons where coupon_code=:code and is_live=1 and is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('code',$coupon);
	try{$sth->execute();}
	catch(Exception $e){
	//echo $e->getMessage();
	}
	$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
	$cid=$res1[0]['id'];
	if(!$cid)
	$cid=0;
	}
	else
	$cid=0;
	//date_default_timezone_set("EST");
	$code=rand(10000,999999);
	//$t=date('m-d-Y H:i:s');
	
	
	$sql="insert into `order` values(DEFAULT,:user_id,:venue_id,:coupon_id,:delivery_type,:order_amount,:bill_file,:payment_status,:confirmation_code,NOW())";

		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("venue_id",$vid);
		$sth->bindValue("coupon_id",$cid);
		$sth->bindValue("delivery_type",$delivery_type);
		$sth->bindValue("order_amount",$order_amount);
		$sth->bindValue("bill_file",$bill);
		$sth->bindValue("payment_status",$payment_status);
		$sth->bindValue("confirmation_code",$code);
		
		$count=0;
		try{$count=$sth->execute();
		$oid=$conn->lastInsertId();
		}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		
		$sql="select FROM_UNIXTIME( UNIX_TIMESTAMP( `order`.created_on) +".SERVER_OFFSET."+ ({$zone}) )  as order_time from `order` where id=:id";
		$sth=$conn->prepare($sql);
		$sth->bindValue('id',$oid);
		try{$sth->execute();}
		catch(Exception $e){echo $e->getMessage();}
		$res2=$sth->fetchAll(PDO::FETCH_ASSOC);
		$t=$res2[0]['order_time'];
			
		$sql="insert into `user_coupons` values(DEFAULT,:user_id,:coupon_id,:order_id,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("coupon_id",$cid);
		$sth->bindValue("order_id",$oid);	
		$count1=0;
		try{$count1=$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		
		$sql="insert into `staff_order` values(DEFAULT,:order_id,:venue_id,:staff_id,:status,NOW(),0,0,0)";

		$sth=$conn->prepare($sql);
		$sth->bindValue("staff_id",0);
		$sth->bindValue("status",1);
		$sth->bindValue("order_id",$oid);
		$sth->bindValue("venue_id",$vid);	
		$count2=0;
		try{$count2=$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		
		$sql="insert into `tip` values(DEFAULT,:manager_id,:user_id,:value,:order_id,NOW())";

		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("manager_id",$mid);
		$sth->bindValue("value",$value);
		$sth->bindValue("order_id",$oid);
			
		$count3=0;
		try{$count3=$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}	

		$sql="insert into `stripe_customer` values(DEFAULT,:user_id,:venue_id,:stripe_token,:order_id,:order_amount,'','',0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("user_id",$uid);
		$sth->bindValue("stripe_token",$stripe_token);
		$sth->bindValue("venue_id",$vid);	
		$sth->bindValue("order_amount",$order_amount);
		$sth->bindValue("order_id",$oid);
			
		$count4=0;
		try{$count4=$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}	
		
		if($count && $count2 && $count3){
		$msg="Order Placed";
		$success=1;
		
		//get order code
			$data['order']=array(
				"confirmation_code"=>$code,
				"order_id"=>$oid,
				"order_time"=>$t
				
			);
		
		//For GCM..................
			$message=array();
	    		$reg_ids=array();
	    		$message['order_id']=$oid;
	    		if($delivery_type=='pick_up')
	    		$message['msg']="New Order - Pickup(P#{$oid})";
	    		else
	    		$message['msg']="New Order(P#{$oid}) - Delivery({$delivery_type})";
	    		
	    		$message['type']=2;// type denotes that this push is for bartender
	    		$message['notification_type']=0;
	    		$sql_qry="select staff.apnid as apn_id,staff.regid as reg_id from `order` left join staff on staff.is_deleted=0 and staff.online=1 and 
	    		staff.venue_id=`order`.venue_id left join staff_order on staff_order.order_id=`order`.id where `order`.id=:order_id";
	    		$sth=$conn->prepare($sql_qry);
	    		$sth->bindValue("order_id",$oid);
	    		try{$sth->execute();}
	    		catch(Exception $e){
	    		//echo $e->getMessage();
	    		}
	    		$rst2=$sth->fetchAll(PDO::FETCH_ASSOC);
	    		
	    		foreach($rst2 as $key=>$value){
	    		 if(!empty($value['reg_id'])){
	    				$reg_ids[]=$value['reg_id'];
	    				
	    			}
	    			}
	    		/*foreach($rst2 as $key=>$value){	
	    			if(!empty($value['apn_id'])){
	    			
	    				$apns->newMessage($value['apn_id']);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('x.wav');
					$apns->addMessageCustom('order_id', $order_id);
					$apns->addMessageCustom('type', $message['type']);
					$apns->addMessageCustom('n_type', $message['notification_type']);
					$apns->queueMessage();
					$apns->processQueue();
	    			}
	    			else{
	    			}
	    			}*/
	    		
	    		if(!empty($reg_ids))
	    		{	
	    			
	    			//print_r($reg_ids);die;
	    			//print_r($message);
	    			GCM::send_notification($reg_ids, $message);
	    		}
	   
		
			//order place successful
			$sql="select * from users where id=:uid";
			$sth=$conn->prepare($sql);
			$sth->bindValue('uid',$uid);
			try{$sth->execute();}
			catch(Exception $e){}
			$res4=$sth->fetchAll(PDO::FETCH_ASSOC);
			$email=$res4[0]['email'];
			$username=$res4[0]['username'];
			
			$sql1="SELECT * FROM  `order` O JOIN venue V ON V.id = O.venue_id WHERE O.id =:oid";
			$sth1=$conn->prepare($sql1);
			$sth1->bindValue('oid',$oid);
			try{$sth1->execute();}
			catch(Exception $e){}
			$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
			$venue_name=$res1[0]['venue_name'];
			
			$pdf_page=GeneralFunctions::createPDFForOrderPlaced($oid);
			$subject_msg="Gambay bill - $venue_name - Order #P$oid";
			$body_msg="Dear $username, <br> Thank you for using Gambay to place your order. <br> Here is the receipt for your records!";
			GeneralFunctions::sendEmailForOrderPlaced($email, $subject_msg, $body_msg, SMTP_EMAIL);
			
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