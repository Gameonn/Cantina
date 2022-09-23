<?php
//this is an api to get all requests
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("phpInclude/dbconnection.php");

$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$access_token=$_REQUEST['access_token'];
$min_price=isset($_REQUEST['min_price']) ? $_REQUEST['min_price'] : 0;
$country=isset($_REQUEST['country']) && strtolower($_REQUEST['country'])!="all" ? $_REQUEST['country'] : "";
$charity=isset($_REQUEST['charity']) && $_REQUEST['charity']!="All" ? $_REQUEST['charity'] : "";

if(!($access_token)){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sql="select vipid from token where token=:access_token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("access_token",$access_token);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll();
	$vipid=$result[0]['vipid'];
}

if($vipid){
	$whereClause="where booking.status=0 and booking.vipid=:vipid";
	if($min_price) $whereClause.=" and booking.offer>=:min_price";
	if($country) $whereClause.=" and booking.recipient_city=:country";
	if($charity) $whereClause.=" and charity.name=:charity";
	
	$orderBy="order by booking.requested_on desc";
	switch($_REQUEST['sort_by']){
		case "Date": break;
		case "Name": $orderBy="order by booking.recipient_name asc"; break;
		case "Offer price": $orderBy="order by booking.offer desc"; break;
	}
	$page=$_REQUEST['page'] ? $_REQUEST['page'] : 1;
	$limit=$_REQUEST['limit'] ? $_REQUEST['limit'] : 10;
	$startIndex=($page-1)*$limit;
	
	//total records
	$sql="select count(booking.id) as total from booking left join fan on fan.fanid=booking.fanid left join charity on charity.id=booking.charity_id $whereClause";
	$sth=$conn->prepare($sql);
	$sth->bindValue("vipid",$vipid);
	if($min_price) $sth->bindValue("min_price",$min_price);
	if($country) $sth->bindValue("country",$country);
	if($charity) $sth->bindValue("charity",$charity);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll();
	$total_records=$result[0][0];
	$total_pages=ceil($result[0][0]/$limit);
	
	$sql="select booking.id as request_id,booking.not_for_me,booking.recipient_name,booking.recipient_age,booking.recipient_city,IF(booking.recipient_pic is null,'".BASE_PATH."/timthumb.php?src=uploads/dumy_user.jpg',CONCAT('".BASE_PATH."/timthumb.php?src=uploads/"."',booking.recipient_pic)) as `recipient_pic`,TRIM(TRAILING ' ' FROM CONCAT(fan.first_name,' ',fan.last_name)) as name,fan.first_name,YEAR(CURRENT_TIMESTAMP) - YEAR(fan.dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(fan.dob, 5)) as age,fan.country,DATE_FORMAT(booking.requested_on,'%d-%b-%y') as `date`,IF(fan.pic is null,'".BASE_PATH."/timthumb.php?src=uploads/dumy_user.jpg',CONCAT('".BASE_PATH."/timthumb.php?src=uploads/"."',fan.pic)) as `pic`,booking.text,booking.category,booking.offer,charity.name as charity,booking.pictures from booking left join fan on fan.fanid=booking.fanid left join charity on charity.id=booking.charity_id $whereClause $orderBy limit $startIndex,$limit";
	$sth=$conn->prepare($sql);
	$sth->bindValue("vipid",$vipid);
	if($min_price) $sth->bindValue("min_price",$min_price);
	if($country) $sth->bindValue("country",$country);
	if($charity) $sth->bindValue("charity",$charity);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	if(count($result)){
		$success="1";
		$msg="Records Found";
		
		$data["requests"]=$result;
		
		foreach($result as $key=>$row){
			$pictures=unserialize($row['pictures']);
			if(!is_array($pictures)) $pictures=array();
			$data["requests"][$key]['pictures']=array();
			foreach($pictures as $value){
				$data["requests"][$key]['pictures'][]=BASE_PATH."/timthumb.php?src=uploads/".$value;
			}
		
			//check if the recipient is same or different
			if($row["not_for_me"]){
				$data["requests"][$key]["name"]=$row["recipient_name"];
				$data["requests"][$key]["first_name"]=explode(" ",$row["recipient_name"])[0];
				$data["requests"][$key]["age"]=$row["recipient_age"];
				$data["requests"][$key]["country"]=$row["recipient_city"];
				$data["requests"][$key]["pic"]=$row["recipient_pic"];
			}
			
			unset($data["requests"][$key]["not_for_me"]);
			unset($data["requests"][$key]["recipient_name"]);
			unset($data["requests"][$key]["recipient_age"]);
			unset($data["requests"][$key]["recipient_city"]);
			unset($data["requests"][$key]["recipient_pic"]);
		}
	}
	else{
		$success="1";
		$msg="No Record Found";
		$data["requests"]=array();
	}
	
	//get statistics
	$sql="select vip.vipid as pk,vip.status,vip.stage_name,vip.pic,vip.intro_text,vip.price_suggestion,vip.donation_share,vip_stats.*,bank.beneficiary_name,bank.bank_account_number,bank.bank_name,bank.iban,bank.bic from vip left join vip_stats on vip_stats.vipid=vip.vipid left join vip_payment_info as bank on bank.vipid=vip.vipid where vip.vipid=:vipid";
	$sth=$conn->prepare($sql);
	$sth->bindValue("vipid",$vipid);
	try{$sth->execute();}catch(Exception $e){}
	$stats_result=$sth->fetchAll();
	
	$data['statistics']=array(
		"accepted"=>$stats_result[0]['req_accepted'] ? $stats_result[0]['req_accepted'] : "0",
		"rejected"=>$stats_result[0]['req_rejected'] ? $stats_result[0]['req_rejected'] : "0",
		"open"=>$stats_result[0]['req_open'] ? $stats_result[0]['req_open'] : "0",
		"total_proceeds"=>$stats_result[0]['total_proceeds'] ? $stats_result[0]['total_proceeds'] : "0",
		"money_earned"=>$stats_result[0]['total_earned'] ? $stats_result[0]['total_earned'] : "0",
		"money_donated"=>$stats_result[0]['total_donated'] ? $stats_result[0]['total_donated'] : "0",
		"responsiveness"=>round($stats_result[0]['responsiveness'],0) ? $stats_result[0]['responsiveness'] : "0",
		"availability"=>round($stats_result[0]['availability'],0) ? $stats_result[0]['availability'] : "0",
		"answer_quality"=>round($stats_result[0]['quality'],0) ? $stats_result[0]['quality'] : "0"
	);
	
	//calculate account balance
	$sql="select sum(booking.earned) as balance from greeting left join booking on booking.id=greeting.booking_id where greeting.vipid=:vipid";
	$sth=$conn->prepare($sql);
	$sth->bindValue("vipid",$vipid);
	try{$sth->execute();}catch(Exception $e){}
	$balance_result=$sth->fetchAll();
	$account_balance=$balance_result[0][0];
	
	$sql="select sum(money) as paid from vip_transaction where vipid=:vipid";
	$sth=$conn->prepare($sql);
	$sth->bindValue("vipid",$vipid);
	try{$sth->execute();}catch(Exception $e){}
	$balance_result=$sth->fetchAll();
	$account_balance-=$balance_result[0][0];
	
	//get bank details
	$data['bank_details']=array(
		"beneficiary_name"=>$stats_result[0]['beneficiary_name'],
		"bank_account_number"=>$stats_result[0]['bank_account_number'],
		"bank_name"=>$stats_result[0]['bank_name'],
		"iban"=>$stats_result[0]['iban'],
		"bic"=>$stats_result[0]['bic'],
		"account_balance"=>$account_balance,
		"status"=>1
	);
}
else{
	$success="0";
	$msg="Invalid access token";
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
echoJSON(array("success"=>$success,"msg"=>$msg,"total_records"=>$total_records,"data"=>$data));
?>