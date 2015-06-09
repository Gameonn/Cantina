<?php
class VenueClass{
	public static function getAllCoupons($startpoint,$limit,$sortby,$token){
	global $conn;
    $sql="SELECT id from manager where token=:token and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("token",$token);
    try{$sth->execute();}catch(Exception $e){}
    $mgid=$sth->fetchAll();
    $mid=$mgid[0]['id'];

    $sql="SELECT venue_id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("manager_id",$mid);
    try{$sth->execute();}catch(Exception $e){}
    $r=$sth->fetchAll();
  	   $vid=$r[0]['venue_id'];
		
		$whereClause = "where venue_id='{$vid}' and is_deleted=0";
		
		$orderClause = " ORDER BY coupons.id ASC ";
		
		switch ($sortby) {
		  case "1":	//Coupon name
		  
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.coupon_name DESC ";
			
			$sql_with_limit =
			"SELECT * from coupons 
			$whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons
			$whereClause $orderClause ";
			
			break;
		  case "2": //expiry date
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.expiry_date DESC";
			
			$sql_with_limit =
			"SELECT * from coupons $whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $orderClause ";
			break;
		  case "3": //value of coupon
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.value DESC ";
			
			$sql_with_limit =
			"SELECT * from coupons $whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $orderClause ";
			break;
		  	
		  default:
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.created_on DESC ";
			
			$sql_with_limit =
			"SELECT * from coupons $whereClause
			$orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause
			$orderClause ";
		}
		
		$result = $conn->query($sql_with_limit);	
		$listing = array();
		$resultSet = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC))
		{
			$listing[] = $row;
		}
		$resultSet["listing"] = $listing;
		
		$result = $conn->query($sql_without_limit);	
		$listing = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC))
		{
			$listing[] = $row;
		}
		$resultSet["count"] = count($listing);
		
		return $resultSet;
	}
	
	public static function getLimit($id){
	global $conn;
  //finding count of coupons to check if limit has exceeded or not
  $sql="SELECT count(user_id) as c FROM `user_coupons` where user_coupons.coupon_id=:coupon_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("coupon_id",$id);
  try{$sth->execute();}catch(Exception $e){}
  $w=$sth->fetchAll(PDO::FETCH_ASSOC);
  $val=$w[0]['c']?$w[0]['c']:0;
	
	return $val;
	}
	
public static function getFilteredCoupons($startpoint,$limit,$filterby,$token,$sortby){

	global $conn;
    $sql="SELECT id from manager where token=:token and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("token",$token);
    try{$sth->execute();}catch(Exception $e){}
    $mgid=$sth->fetchAll();
    $mid=$mgid[0]['id'];

    $sql="SELECT venue_id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("manager_id",$mid);
    try{$sth->execute();}catch(Exception $e){}
    $r=$sth->fetchAll();
  	   $vid=$r[0]['venue_id'];
		
		
		$whereClause = "where venue_id='{$vid}'";
		
		$orderClause = " ORDER BY coupons.id ASC ";
		$new_var=$filterby.$sortby;
		switch ($new_var) {
		  case "231":	//Live coupons sortby name
			$current_date=date('Y-m-d');
			$where1="and status=1 and expiry_date > '{$current_date}' and is_live=1 and is_deleted=0";
		    $orderClause = "GROUP BY coupons.id ORDER BY coupons.coupon_name ASC ";
			
			$sql_with_limit =
			"SELECT * from coupons 
			$whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons
			$whereClause $where1 $orderClause ";
			
			break;
			
			 case "232":	//Live coupons sortby date
		    $current_date=date('Y-m-d');
			$where1="and status=1 and expiry_date > '{$current_date}' and is_live=1 and is_deleted=0";
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.expiry_date DESC ";
			$sql_with_limit =
			"SELECT * from coupons 
			$whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons
			$whereClause $where1 $orderClause ";
			
			break;
			
			 case "233":	//Live coupons sortby value
		    $current_date=date('Y-m-d');
			$where1="and status=1 and expiry_date > '{$current_date}' and is_live=1 and is_deleted=0";
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.value DESC ";
			$sql_with_limit =
			"SELECT * from coupons 
			$whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons
			$whereClause $where1 $orderClause ";
			
			break;
			
		 case "241": //coupons expired
		  $current_date=date('Y-m-d');
		  $orderClause = "GROUP BY coupons.id ORDER BY coupons.coupon_name ASC ";
			$where1="and (status=0 or expiry_date < '{$current_date}')";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			
			 case "242": //coupons expired
		  $current_date=date('Y-m-d');
		  $orderClause = "GROUP BY coupons.id ORDER BY coupons.expiry_date DESC ";
			$where1="and (status=0 or expiry_date < '{$current_date}')";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			 case "243": //coupons expired
		  $current_date=date('Y-m-d');
		  $orderClause = "GROUP BY coupons.id ORDER BY coupons.value DESC ";
			$where1="and (status=0 or expiry_date < '{$current_date}')";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			
			case "251": //coupons all
			  $current_date=date('Y-m-d');
			  $orderClause = "GROUP BY coupons.id ORDER BY coupons.coupon_name ASC ";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			
			 case "252": //coupons all
		  $current_date=date('Y-m-d');
		  $orderClause = "GROUP BY coupons.id ORDER BY coupons.expiry_date DESC ";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			 case "253": //coupons all
		  $current_date=date('Y-m-d');
		  $orderClause = "GROUP BY coupons.id ORDER BY coupons.value DESC ";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
			
		/*  case "25": //Coupons with limit
			$where1="and limit<9999";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
		  case "26": //coupons without any limit
			$where1="and limit==99999999";
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;
		  case "27": //coupons deleted
			$where1="and is_live=0 and is_deleted=1";			
			$sql_with_limit =
			"SELECT * from coupons $whereClause $where1 $orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $where1 $orderClause ";
			break;*/
		 		  	
		  default:
			$orderClause = " GROUP BY coupons.id ORDER BY coupons.created_on DESC ";
			
			$sql_with_limit =
			"SELECT * from coupons $whereClause 
			$orderClause
			LIMIT {$startpoint}, {$limit}";
			
			$sql_without_limit = "SELECT * from coupons $whereClause $orderClause ";
		}
		
		$result = $conn->query($sql_with_limit);	
		$listing = array();
		$resultSet = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC))
		{
			$listing[] = $row;
		}
		$resultSet["listing"] = $listing;
		
		$result = $conn->query($sql_without_limit);	
		$listing = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC))
		{
			$listing[] = $row;
		}
		$resultSet["count"] = count($listing);
		
		return $resultSet;
	}	
	}

?>