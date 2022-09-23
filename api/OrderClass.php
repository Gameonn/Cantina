<?php

class OrderClass{
	
public static function get_orders($sid,$vid,$status,$zone){
	
global $conn;
	
	if($status==1){
	$sql="SELECT `order`.*,venue.*, (select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as vtype,(select pictures.url from pictures where pictures.venue_id=venue.id) as url,TRUNCATE((`order`.order_amount),2) as order_amount,`order`.id as oid,FROM_UNIXTIME( UNIX_TIMESTAMP( `order`.created_on ) +".SERVER_OFFSET."+ ({$zone}) )  as order_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.ready_time ) +".SERVER_OFFSET."+ ({$zone}) )  as ready_time1, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.closed_time) +".SERVER_OFFSET."+ ({$zone}) )  as closed_time1, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.void_time) +".SERVER_OFFSET."+ ({$zone}) )  as void_time1, staff_order.*,users.*,
	CASE 
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
                END as time_elapsed
 FROM `order` join venue on venue.id=`order`.venue_id join staff_order on staff_order.order_id=`order`.id and staff_order.venue_id=`order`.venue_id and staff_order.status=:status join users on users.id=`order`.user_id where `order`.venue_id=:venue_id and (DATE(`order`.created_on)=CURDATE() or DATE(`order`.created_on)=CURDATE()-1) ";
 	$sth=$conn->prepare($sql);
	}
	else{
		$sql="SELECT `order`.*,venue.*, (select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as vtype,(select pictures.url from pictures where pictures.venue_id=venue.id) as url,TRUNCATE((`order`.order_amount),2) as order_amount,`order`.id as oid,FROM_UNIXTIME( UNIX_TIMESTAMP( `order`.created_on ) +".SERVER_OFFSET."+ ({$zone}) )  as order_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.ready_time ) +".SERVER_OFFSET."+ ({$zone}) )  as ready_time1, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.closed_time) +".SERVER_OFFSET."+ ({$zone}) )  as closed_time1, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.void_time) +".SERVER_OFFSET."+ ({$zone}) )  as void_time1, staff_order.*,users.*,
	CASE 
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
                END as time_elapsed
 FROM `order` join venue on venue.id=`order`.venue_id join staff_order on staff_order.order_id=`order`.id and staff_order.venue_id=`order`.venue_id and staff_order.status=:status and staff_order.staff_id=:staff_id join users on users.id=`order`.user_id where `order`.venue_id=:venue_id and ( DATE(`order`.created_on)=CURDATE() or DATE(`order`.created_on)=CURDATE()-1) ";
 	$sth=$conn->prepare($sql);
	$sth->bindValue('staff_id',$sid);
	}
	
	
	$sth->bindValue('venue_id',$vid);
	$sth->bindValue('status',$status);
	
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result) ){

		$success="1";	
			foreach($result as $key=>$value){
			$obj= json_decode($value['bill_file'],true);
			
		if(!isset($final[$value['oid']])){
		
					$final[$value['oid']]=array(
					'order_id'=>$value['oid'],
		 			'time_elapsed'=>$value['time_elapsed'], 
		 			'order_time'=>$value['order_time']?$value['order_time']:'1999-05-04 03:14:10',
		 			'ready_time'=>$value['ready_time1']?$value['ready_time1']:"1999-05-04 03:14:10",
		 			'closed_time'=>$value['closed_time1']?$value['closed_time1']:"1999-05-04 03:14:10",
		 			'void_time'=>$value['void_time1']?$value['void_time1']:"1999-05-04 03:14:10",
		 			'order_amount'=>$value['order_amount']?$value['order_amount']:0,
		 			'delivery_type'=>$value['delivery_type']?$value['delivery_type']:"",
		 			'name'=>$value['username']?$value['username']:"",
		 			'email'=>$value['email']?$value['email']:"", 	
		 			 'zipcode'=>$value['zipcode']?$value['zipcode']:"",
		 			 'mobile'=>$value['mobile']?$value['mobile']:"",
		 			 'dob'=>$value['dob']?$value['dob']:"",
		 			 'user_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:BASE_PATH."timthumb.php?src=uploads/default-profilepic.jpg",
		 			 'tip'=>$obj['tip']?$obj['tip']:0,
		 			 'venue_id'=>$vid,
					'venue_name'=>$value['venue_name'],
					'address'=>$value['address']?$value['address']:"",
					'city'=>$value['city']?$value['city']:"",
					'zipcode'=>$value['zipcode']?$value['zipcode']:"",
					'state'=>$value['state']?$value['state']:"",
					'venue_longitude'=>$value['longitude'],
					'venue_latitude'=>$value['latitude'],
					'mobile'=>$value['mobile']?$value['mobile']:"",
					'venuetype'=>$value['vtype'],				
					'venue_images'=>$value['url']?BASE_PATH."timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg",
		 			 'item'=>array()
				);
				}
			if(is_array($obj)){	
			foreach($obj as $k=>$v){
			
			
			if(is_array($v)){
			foreach($v as $key1=>$val){
			
				if(!ISSET($final[$value['oid']]['item'][$val['item_id']])){			
				$final[$value['oid']]['item'][$val['item_id']]=array('item_id'=>$val['item_id'],
				'item_name'=>$val['item_name'],
				"serving_id"=>$val['serving_id']?$val['serving_id']:"",
			        "serving_name"=>$val['serving_name']?$val['serving_name']:"",
				"quantity"=>$val['quantity']?$val['quantity']:0,
				"special_instructions"=>$val['instructions']?$val['instructions']:""
				);
					}		
				}}	
			}}
		}	
		
	$data=array();
	$data2=array();
		foreach($final as $key=>$value){
		
		foreach($value['item'] as $value2){
			$data2[]=$value2;
		}
		$final[$key]['item']=$data2;
		$data2=array();
	}
	
	foreach($final as $key=>$value){
		$data[]=$value;
	}
	return $data;
		
		
		}
	}
	
public static function get_users_orders($uid,$zone){
	
global $conn;
	
	
	$sql="SELECT `order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.status,staff_order.*,
FROM_UNIXTIME( UNIX_TIMESTAMP( `order`.created_on ) +".SERVER_OFFSET."+ ({$zone}) )  as order_time,
 FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.ready_time ) +".SERVER_OFFSET."+ ({$zone}) )  as ready_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.closed_time) +".SERVER_OFFSET."+ ({$zone}) )  as closed_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.Void_time) +".SERVER_OFFSET."+ ({$zone}) )  as void_time,`order`.id as oid,venue.id as vid,venue.*,venue.latitude as v_latitude,venue.longitude as v_longitude,venue.mobile as v_mobile,(select pictures.url from pictures where pictures.venue_id=venue.id) as url, users.*,
	CASE 
                  WHEN DATEDIFF(NOW(),`order`.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),`order`.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
                END as time_elapsed
 FROM `order` join venue on venue.id=order.venue_id join users on users.id=`order`.user_id left join staff_order on staff_order.order_id=`order`.id where `order`.user_id=:user_id ";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result) ){

		$success="1";	
			foreach($result as $key=>$value){
			$obj= json_decode($value['bill_file'],true);
			
		if(!isset($final[$value['oid']])){
		
					$final[$value['oid']]=array(
					"order_id"=>$value['oid'],
		 			'time_elapsed'=>$value['time_elapsed'], 
		 			'order_status'=>$value['status'],
		 			'order_time'=>$value['order_time']?$value['order_time']:"1999-05-04 03:14:10",
		 			'latitude'=>$value['v_latitude'],
		 			'longitude'=>$value['v_longitude'],
		 			'mobile'=>$value['v_mobile']?$value['v_mobile']:"",		 			
					'ready_time'=>$value['ready_time']?$value['ready_time']:"1999-05-04 03:14:10",
		 			'closed_time'=>$value['closed_time']?$value['closed_time']:"1999-05-04 03:14:10",
		 			'void_time'=>$value['void_time']?$value['void_time']:"1999-05-04 03:14:10",
		 			'order_amount'=>$value['order_amount']?$value['order_amount']:0,
		 			'delivery_type'=>$value['delivery_type']?$value['delivery_type']:"",
		 			'name'=>$value['username']?$value['username']:"",
		 			'email'=>$value['email']?$value['email']:"", 	
		 			 'zipcode'=>$value['zipcode']?$value['zipcode']:"",
		 			  'dob'=>$value['dob']?$value['dob']:"",
		 			 'user_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:BASE_PATH."timthumb.php?src=uploads/default-profilepic.jpg",
		 			 "venue_id"=>$value['vid'],
		 			'venue_name'=>$value['venue_name'], 
		 			'venue_address'=>$value['address'],
		 			'city'=>$value['city'],
		 			'venue_contact'=>$value['mobile']?$value['mobile']:"",	
		 			 'venue_image'=>$value['url']?BASE_PATH."timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg",
		 			 'coupon_code'=>$obj['coupon_code']?$obj['coupon_code']:0,
		 			 'tip'=>$obj['tip']?$obj['tip']:0,
		 			 'item'=>array()
				);
				}
			if(is_array($obj)){	
			foreach($obj as $k=>$v){
			
			
			if(is_array($v)){
			foreach($v as $key1=>$val){
			
				if(!ISSET($final[$value['oid']]['item'][$val['item_id']])){			
				$final[$value['oid']]['item'][$val['item_id']]=array('item_id'=>$val['item_id'],
				'item_name'=>$val['item_name'],
				"serving_id"=>$val['serving_id']?$val['serving_id']:"",
			    "serving_name"=>$val['serving_name']?$val['serving_name']:"",
				"quantity"=>$val['quantity']?$val['quantity']:0,
				"aggregate_price"=>$val['aggregate_price']?$val['aggregate_price']:0,
				"item_price"=>$val['item_price']?$val['item_price']:0,
				"special_instructions"=>$val['instructions']?$val['instructions']:""
				);
					}		
				}}	
			}}
		}	
		
	$data=array();
	$data2=array();
		foreach($final as $key=>$value){
		
		foreach($value['item'] as $value2){
			$data2[]=$value2;
		}
		$final[$key]['item']=$data2;
		$data2=array();
	}
	
	foreach($final as $key=>$value){
		$data[]=$value;
	}
	return $data;
		
		
		}
	
	
	}


public static function get_venue_details($vid){
	
global $conn;
	
	
		$sql="select venue.*, (select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as vtype,(select pictures.url from pictures where pictures.venue_id=:id) as url from venue where venue.id=:id and venue.is_live=1 and venue.is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$venue=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($venue) ){

		$success="1";	
	foreach($venue as $key=>$value){
				if($value['venue_name']){
					$venue[$key]=array('venue_id'=>$value['id'],
					'venue_name'=>$value['venue_name'],
					'address'=>$value['address']?$value['address']:"",
					'city'=>$value['city']?$value['city']:"",
					'zipcode'=>$value['zipcode']?$value['zipcode']:"",
					'state'=>$value['state']?$value['state']:"",
					'parking'=>$value['parking']?$value['parking']:"",
					'venue_longitude'=>$value['longitude'],
					'venue_latitude'=>$value['latitude'],
					'paypal'=>$value['paypal']?$value['paypal']:"",        		
					'fax'=>$value['fax']?$value['fax']:"",
					'mobile'=>$value['mobile']?$value['mobile']:"",
					'venuetype'=>$value['vtype'],
					"distance"=>$value['distance']?$value['distance']:0,				
					'venue_images'=>$value['url']?BASE_PATH."timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
					);
				}
			
			}
	return $venue;
		
		
		}
	
	
	}
	
	
public static function get_order_by_id($uid,$oid,$zone){
	
global $conn;
	
	
	$sql="SELECT `order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.status,staff_order.*,`order`.id as oid, FROM_UNIXTIME( UNIX_TIMESTAMP( `order`.created_on ) +".SERVER_OFFSET."+ ({$zone}) )  as order_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.ready_time ) +".SERVER_OFFSET."+ ({$zone}) )  as ready_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.closed_time) +".SERVER_OFFSET."+ ({$zone}) )  as closed_time, FROM_UNIXTIME( UNIX_TIMESTAMP( `staff_order`.Void_time) +".SERVER_OFFSET."+ ({$zone}) )  as void_time,venue.id as vid,venue.*,venue.latitude as v_latitude,venue.longitude as v_longitude,venue.mobile as v_mobile,(select pictures.url from pictures where pictures.venue_id=venue.id) as url, users.*,
	CASE 
                  WHEN DATEDIFF(NOW(),`order`.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),`order`.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
                END as time_elapsed
 FROM `order` join venue on venue.id=order.venue_id join users on users.id=`order`.user_id left join staff_order on staff_order.order_id=`order`.id where `order`.user_id=:user_id and `order`.id=:order_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	$sth->bindValue('order_id',$oid);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result) ){

		$success="1";	
			foreach($result as $key=>$value){
			$obj= json_decode($value['bill_file'],true);
			
		if(!isset($final[$value['oid']])){
		
					$final[$value['oid']]=array(
					"order_id"=>$value['oid'],
		 			'time_elapsed'=>$value['time_elapsed'], 
		 			'open_time'=>$value['order_time'],
		 			'ready_time'=>$value['ready_time']?$value['ready_time']:"1999-05-04 03:14:10",
		 			'closed_time'=>$value['closed_time']?$value['closed_time']:"1999-05-04 03:14:10",
		 			'void_time'=>$value['void_time']?$value['void_time']:"1999-05-04 03:14:10",
		 			
		 			'latitude'=>$value['v_latitude'],
		 			'longitude'=>$value['v_longitude'],
					'order_status'=>$value['status'],
		 			'order_amount'=>$value['order_amount']?$value['order_amount']:0,
		 			'delivery_type'=>$value['delivery_type']?$value['delivery_type']:"",
		 			'name'=>$value['username']?$value['username']:"",
		 			'email'=>$value['email']?$value['email']:"", 	
		 			 'zipcode'=>$value['zipcode']?$value['zipcode']:"",
		 			 'mobile'=>$value['mobile']?$value['mobile']:"",
		 			  'dob'=>$value['dob']?$value['dob']:"",
		 			 'user_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:BASE_PATH."timthumb.php?src=uploads/default-profilepic.jpg",
		 			 "venue_id"=>$value['vid'],
		 			'venue_name'=>$value['venue_name'], 
		 			'venue_address'=>$value['address']?$value['address']:"",
		 			'city'=>$value['city']?$value['city']:"",
		 			'venue_contact'=>$value['v_mobile']?$value['v_mobile']:"",	
		 			 'venue_image'=>$value['url']?BASE_PATH."timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg",
		 			 'coupon_code'=>$obj['coupon_code']?$obj['coupon_code']:0,
		 			 'tip'=>$obj['tip']?$obj['tip']:0,
		 			 'total_tax'=>$obj['total_tax']?$obj['total_tax']:0,
		 			 'item'=>array()
				);
				}
			if(is_array($obj)){	
			foreach($obj as $k=>$v){
			
			
			if(is_array($v)){
			foreach($v as $key1=>$val){
			
				if(!ISSET($final[$value['oid']]['item'][$key1])){			
				$final[$value['oid']]['item'][$key1]=array(
				'item_id'=>$val['item_id'],
				'item_name'=>$val['item_name'],
				"serving_id"=>$val['serving_id']?$val['serving_id']:"",
			    "serving_name"=>$val['serving_name']?$val['serving_name']:"",
				"quantity"=>$val['quantity']?$val['quantity']:0,
				"aggregate_price"=>$val['aggregate_price']?$val['aggregate_price']:0,
				"item_price"=>$val['item_price']?$val['item_price']:0,
				"special_instructions"=>$val['instructions']?$val['instructions']:""
				);
					}		
				}}	
			}}
		}	
		
	$data=array();
	$data2=array();
		foreach($final as $key=>$value){
		
		foreach($value['item'] as $value2){
			$data2[]=$value2;
		}
		$final[$key]['item']=$data2;
		$data2=array();
	}
	
	foreach($final as $key=>$value){
		$data[]=$value;
	}
	return $data;
		
		
		}
	
	
	}
}
	
	
	
?>	
