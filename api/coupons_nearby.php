<?php
//this is an api for search

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
error_reporting(0);

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data                  +
// +-----------------------------------+

$distance=$_REQUEST['distance'];
$flag=$_REQUEST['flag'];
$latitude=$_REQUEST['latitude'];
$longitude=$_REQUEST['longitude'];

if($flag=="miles"){
    $e=3961;
        $distance=$distance?$distance:'5000';
   } 
else{
    $e=6373;
    $distance=$distance?$distance:'6000';

}


   $searchKey.=" temp.distance < {$distance}";



// +-----------------------------------+
// + STEP 3: perform operations        +
// +-----------------------------------+
  /*  $sql="select coupons.* from coupons where expiry_date > CURDATE() and is_live=1 and is_deleted=0 and `limit`>(SELECT count(user_id) FROM `user_coupons` where user_coupons.coupon_id=coupons.id ) and venue_id IN (select venue.id as vid,
    TRUNCATE(( $e * acos( cos( radians( '{$latitude}' ) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians( '{$longitude}' ) ) + sin( radians( '{$latitude}' ) ) * sin( radians( `latitude` ) ) ) ),2) AS distance
     from venue WHERE venue.is_live=1 and venue.is_deleted=0) as temp $searchKey";*/
     
     $sql="select temp.* from (select coupons.*,venue.id as vid, venue.venue_name,venue.address,venue.city,venue.mobile, venue.zipcode,TRUNCATE(( $e * acos( cos( radians( '{$latitude}' ) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians( '{$longitude}' ) ) + sin( radians( '{$latitude}' ) ) * sin( radians( `latitude` ) ) ) ),2) AS distance,(select pictures.url from pictures where pictures.venue_id=venue.id) as venue_image
       from coupons left join venue on venue.id=coupons.venue_id and venue.is_live=1 and venue.is_deleted=0 where coupons.expiry_date > CURDATE() and coupons.is_live=1 and coupons.is_deleted=0 and coupons.`limit`>(SELECT count(user_id) FROM `user_coupons` where user_coupons.coupon_id=coupons.id )) as temp where $searchKey";
  
    $sth=$conn->prepare($sql);
    //$sth->bindValue('distance',$distance);
    try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    
if(count($result)){
        
        $success=1;
        
            
            foreach($result as $key=>$value){
            
                $data['coupon'][$key]=array('venue_id'=>$value['vid'],
                	      "venue_name"=>$value['venue_name'],
                	      "venue_address"=>$value['address'],
						  "venue_image"=>$value['venue_image']?BASE_PATH."/timthumb.php?src=uploads/".$value['venue_image']:"",
                	      "city"=>$value['city'],
                	      "zipcode"=>$value['zipcode'],
                	      "mobile"=>$value['mobile']?$value['mobile']:"",
                              "coupon_name"=>$value['coupon_name'],
				"coupon_code"=>$value['coupon_code'],
			        "coupon_value"=>$value['value']?(string)$value['value']:'0',
			        "coupon_percentage"=>$value['percentage']?(string)$value['percentage'].'%':'0',
				"expiry_date"=>$value['expiry_date'],
				"venue_id"=>$value['venue_id'],
				"status"=>$value['status'],
				"pic"=>$value['pic']?BASE_PATH."/timthumb.php?src=uploads/".$value['pic']:BASE_PATH."/timthumb.php?src=uploads/thumbnail.png",
				"limit"=>$value['limit']? $value['limit']==99999999 ? " " : (string)$value['limit'] : '0',
				"created_on"=>$value['created_on'],
				"distance"=>$value['distance']
                );
            }
            //$data['venue']=array(0=>array(),1=>array());
            
         /*   $pictures=explode(',',$result[0]['url']);
                foreach($pictures as $row){
                        $data['venue_images'][]=BASE_PATH."/timthumb.php?src=uploads/".$row;
                    }*/
 
}
else{
$success=0;
$msg="No Record Found";
}
// +-----------------------------------+
// + STEP 4: send json data            +
// +-----------------------------------+

if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));

?>
