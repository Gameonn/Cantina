<?php
//this is an api venues

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

// No input parameters

// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sql="select venue.id as vid,venue.venue_name as venue_name, venue.address as address, venue.mobile as mobile, venue.city as city,venue.zipcode as zipcode,venue.state,venue.paypal_email as paypal,venue.fax_number as fax, venue.latitude,venue.longitude,pictures.url,venuetype.type,(select charge from gambay_charge where gambay_charge.venue_id=venue.id) as gambay_charge
     from venue left join pictures on pictures.venue_id=venue.id and pictures.is_deleted=0 left join venuetype on venuetype.id=venue.venuetype_id WHERE venue.is_live=1 and venue.is_deleted=0";
	$sth=$conn->prepare($sql);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
	
	$success='1';
	$msg="Venues Listing";
	   foreach($result as $key=>$value){
            
                $data['venue'][$key]=array('venue_id'=>$value['vid'],
                               'venue_name'=>$value['venue_name'],'address'=>$value['address'],
				'zipcode'=>$value['zipcode'],
				 'city'=>$value['city'], 'state'=>$value['state'],'mobile'=>$value['mobile']?$value['mobile']:"",
                 'parking'=>$value['parking']?$value['parking']:"", 
                 'venue_longitude'=>$value['longitude'], 'venue_latitude'=>$value['latitude'],
                 'paypal'=>$value['paypal']?$value['paypal']:"", 
                  'fax'=>$value['fax'],'venuetype'=>$value['type'],
				    "gambay_fee"=>$value['gambay_charge']?$value['gambay_charge']:0,
                'venue_images'=>$value['url']?BASE_PATH."/timthumb.php?src=uploads/".$value['url']:BASE_PATH."timthumb.php?src=uploads/abt-us-sdimg.jpg"
                );
            }

}

else{

$success='0';
$msg="No Venue found";
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
