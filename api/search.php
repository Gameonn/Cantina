<?php
//this is an api for search

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
//error_reporting(0);

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data                  +
// +-----------------------------------+

$key=$_REQUEST['key'];
$distance=$_REQUEST['distance'];
$flag=$_REQUEST['flag'];
$latitude_1=$_REQUEST['latitude'];
$longitude_1=$_REQUEST['longitude'];

if($flag=="kms"){
    $e=6373;
    if($key){
      $distance=$distance?$distance:'16000';
    }
} 
else{
    $e=3961;
    if($key){
    $distance=$distance?$distance:'10000';
       }
}

//$flag=$_REQUEST['flag'];
$searchKey="where 1";
//$searchKey.=" and (temp.address like '%{$_REQUEST['key']}%' or temp.zipcode like '%{$_REQUEST['key']}%' or temp.city like '%{$_REQUEST['key']}%')";}
if($distance){
    $searchKey.=" AND temp.distance < {$distance}";
}

//geocode(lat,lng)

if($key){
    $geo = lookup($key);
    //print_r($geo);die;
    $latitude_2=$geo['latitude'];
    $longitude_2=$geo['longitude'];
    
}else{
    $latitude_2=$latitude_1;
    $longitude_2=$longitude_1;
}
// +-----------------------------------+
// + STEP 3: perform operations        +
// +-----------------------------------+
    $sql="SELECT temp.* FROM (select venue.id as vid,venue.venue_name as venue_name, venue.address as address, venue.mobile as mobile, venue.city as city,venue.zipcode as zipcode,venue.state,venue.paypal_email as paypal,venue.fax_number as fax, venue.latitude,venue.longitude,pictures.url,venuetype.type,(select charge from gambay_charge where gambay_charge.venue_id=venue.id) as gambay_charge,
    TRUNCATE(( $e * acos( cos( radians( '{$latitude_2}' ) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians( '{$longitude_2}' ) ) + sin( radians( '{$latitude_2}' ) ) * sin( radians( `latitude` ) ) ) ),2) AS distance,
    TRUNCATE(( $e * acos( cos( radians( '{$latitude_1}' ) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians( '{$longitude_1}' ) ) + sin( radians( '{$latitude_1}' ) ) * sin( radians( `latitude` ) ) ) ),2) AS u_distance
     from venue left join pictures on pictures.venue_id=venue.id and pictures.is_deleted=0 left join venuetype on venuetype.id=venue.venuetype_id WHERE venue.is_live=1 and venue.is_deleted=0) as temp $searchKey";
    //echo $sql;
    $sth=$conn->prepare($sql);
    try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    
if(count($result)){
        
        $success=1;
        
            
            foreach($result as $key=>$value){
            
                $data['venue'][$key]=array('venue_id'=>$value['vid'],
                               'venue_name'=>$value['venue_name'],'address'=>$value['address'],
				'zipcode'=>$value['zipcode'], 'distance'=>$value['u_distance'],
				 'city'=>$value['city'], 'state'=>$value['state'],'mobile'=>$value['mobile']?$value['mobile']:"",
                 'parking'=>$value['parking']?$value['parking']:"", 
                 'venue_longitude'=>$value['longitude'], 'venue_latitude'=>$value['latitude'],
                 'paypal'=>$value['paypal']?$value['paypal']:"", 
                  'fax'=>$value['fax'],'venuetype'=>$value['type'],'u_dis'=>$value['distance'],
				  "gambay_fee"=>$value['gambay_charge']?$value['gambay_charge']:0,
                'venue_images'=>$value['url']?BASE_PATH."/timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
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


//function for calculating latitude and longitude from keywords like location/zipcode
 function lookup($string)
    {
    
        $string      = str_replace(" ", "+", urlencode($string));
        $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&sensor=false";
        
        //echo $details_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        
        //print_r($response);
        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return null;
        }
    
        // print_r($response);
        $geometry = $response['results'][0]['geometry'];
        
        $longitude = $geometry['location']['lat'];
        $latitude  = $geometry['location']['lng'];
        
        $array = array(
            'latitude' => $geometry['location']['lat'],
            'longitude' => $geometry['location']['lng']
        );
        
        return $array;
        
    }
?>
