<?php
//this is an api coupon_check

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data          +
// +-----------------------------------+
$code=$_REQUEST['coupon_code'];

// +-----------------------------------+
// + STEP 3: perform operations      +
// +-----------------------------------+
//and `limit`>(SELECT count(user_id) FROM `user_coupons` where user_coupons.coupon_id=coupons.id )

//selecting all live coupons
  $sql="select * from coupons where coupon_code=:coupon_code and status=1 and is_live=1 and is_deleted=0 ";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_code",$code);
  try{$sth->execute();}catch(Exception $e){}
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);
  
  $limit=$result[0]['limit'];
  $expiry=$result[0]['expiry_date'];
  $cid=$result[0]['id'];
  
  
  //finding count of coupons to check if limit has exceeded or not
  $sql="SELECT count(user_id) as c FROM `user_coupons` where user_coupons.coupon_id=:coupon_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_id",$cid);
  try{$sth->execute();}catch(Exception $e){}
  $w=$sth->fetchAll(PDO::FETCH_ASSOC);
  $val=$w[0]['c']?$w[0]['c']:0;
  
  
  //if limit has exceeded by the no of users who used the coupon then update coupon status
    if($limit<=$val){
  $sql="update coupons set status=0, is_live=0, is_deleted=1 where coupons.id=:coupon_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_id",$cid);
  try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
  $msg="Coupon Limit Reached";
  }
  else{
  
  
  if(count($result)){
  
  $current_date=date('Y-m-d');
  
  //checking expiry of coupon
  if($expiry > $current_date){
/*  $sql="select * from user_coupons where coupon_id=:coupon_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_id",$cid);
  try{$sth->execute();}catch(Exception $e){}
  $res=$sth->fetchAll(PDO::FETCH_ASSOC);
  

  if(!count($res)){*/

  $success='1';
  //coupon data
  $data['coupon']=array(
        "coupon_name"=>$result[0]['coupon_name'],
        "coupon_code"=>$result[0]['coupon_code'],
        "coupon_value"=>$result[0]['value']?(string)$result[0]['value']:'0',
        "coupon_percentage"=>$result[0]['percentage']?(string)$result[0]['percentage'].'%':'0',
        "expiry_date"=>$result[0]['expiry_date'],
        "venue_id"=>$result[0]['venue_id'],
        "status"=>$result[0]['status'],
        "pic"=>$result[0]['pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['pic']:"",
        "limit"=>$result[0]['limit']? $result[0]['limit']==99999999 ? " " : (string)$result[0]['limit'] : '0',
        "created_on"=>$result[0]['created_on']
        );

/*}
else{
$success='0';
$msg="Coupon already used previously";
}*/
}
else{
$success="0";
$msg="Coupon Expired";
$sql="update coupons set status=0, is_live=0, is_deleted=1 where coupons.id=:coupon_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_id",$cid);
  try{$sth->execute();}catch(Exception $e){}
}


}
else{
$success='0';
$msg='Coupon code invalid';
}
}
// +-----------------------------------+
// + STEP 4: send json data        +
// +-----------------------------------+

if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>
