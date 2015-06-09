<?php
//this is an api category

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$vid=$_REQUEST['venue_id'];



// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sql="select * from coupons where venue_id=:venue_id and expiry_date > CURDATE() and is_live=1 and is_deleted=0 and `limit`>(SELECT count(user_id) FROM `user_coupons` where user_coupons.coupon_id=coupons.id)";
	$sth=$conn->prepare($sql);
	$sth->bindValue("venue_id",$vid);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
	
	$success='1';
	//coupon data
	foreach($result as $key=>$value){
	$data['coupon'][$key]=array(
				"coupon_name"=>$value['coupon_name'],
				"coupon_code"=>$value['coupon_code'],
			        "coupon_value"=>$value['value']?(string)$value['value']:'0',
			        "coupon_percentage"=>$value['percentage']?(string)$value['percentage'].'%':'0',
				"expiry_date"=>$value['expiry_date'],
				"venue_id"=>$value['venue_id'],
				"status"=>$value['status'],
				"pic"=>$value['pic']?BASE_PATH."/timthumb.php?src=uploads/".$value['pic']:BASE_PATH."/timthumb.php?src=uploads/thumbnail.png",
				"limit"=>$value['limit']? $value['limit']==99999999 ? " " : (string)$value['limit'] : '0',
				"created_on"=>$value['created_on']
				);
	
	
	}

}

else{

$success='0';
$msg="No Coupon found";
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
