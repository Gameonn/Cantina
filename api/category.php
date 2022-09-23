<?php
//this is an api menu category

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

	$sql="select venue.*, (select charge from gambay_charge where gambay_charge.venue_id=venue.id) as gambay_charge,(select venuetype.type from venuetype where venuetype.id=venue.venuetype_id) as vtype,(select pictures.url from pictures where pictures.venue_id=:id) as url from venue where venue.id=:id and venue.is_live=1 and venue.is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$venue=$sth->fetchAll(PDO::FETCH_ASSOC);
 	
	$sql="select menucategory.id as main_id,menucategory.name as main_name from menucategory where menucategory.venue_id=:id and menucategory.is_live=1 and menucategory.is_deleted=0";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	if(count($result) ){
	
	$sql="SELECT pricing.quantity,(select pricing_names.name from pricing_names where pricing_names.id=pricing_name_id) as pr_name,item.id as item_id,item.name,item.item_description as item_desc,item.pic,item.menucategory_id,item_tax.percentage,serving_price.item_price,'1' as flag, serving_price.agg_price,servings.type,servings.id as serve_id, tax.tax_name,tax.description,tax.id as tax_id FROM `pricing` join item on item.id=pricing.item_id and item.is_live=1 and item.is_deleted=0 join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id join tax on tax.venue_id=pricing.venue_id and tax.is_deleted=0 join item_tax on item_tax.item_id=item.id and item_tax.tax_id=tax.id left join pricing_tax on pricing_tax.tax_id=item_tax.tax_id and pricing_tax.serving_price_id=serving_price.id where pricing.special_flag=1 and pricing.venue_id=:venue_id and pricing.status=1";
	
	//for specials category
	/*$sql="SELECT pricing.quantity,(select pricing_names.name from pricing_names where pricing_names.id=pricing_name_id) as pr_name,item.id as item_id,item.name,item.item_description as item_desc,item.pic,item.menucategory_id,category_tax.percentage,serving_price.item_price,'1' as flag, serving_price.agg_price,servings.type,servings.id as serve_id, tax.tax_name,tax.description,tax.id as tax_id FROM `pricing` join item on item.id=pricing.item_id and item.is_live=1 and item.is_deleted=0 join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id join tax on tax.is_deleted=0 join category_tax on category_tax.menucategory_id=item.menucategory_id and category_tax.tax_id=tax.id join pricing_tax on pricing_tax.tax_id=tax.id and pricing_tax.serving_price_id=serving_price.serving_id where pricing.special_flag=1 and pricing.venue_id=:venue_id and pricing.status=1"; */
	$sth=$conn->prepare($sql);
	$sth->bindValue("venue_id",$vid);
	try{$sth->execute();}catch(Exception $e){}
	$res2=$sth->fetchAll(PDO::FETCH_ASSOC);
	//print_r($res2);die;
	
	$sql="select id as sub_id, subcategory.name as sub_name, subcategory.menucategory_id, subcategory.parent_id as sub_parent_id from subcategory where venue_id=:venue_id and is_live=1 and is_deleted=0";	
	$sth=$conn->prepare($sql);
	$sth->bindValue('venue_id',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res1=$sth->fetchAll(PDO::FETCH_ASSOC);
	//print_r($res1);die;
	
	$success='1';

	$mic=categoriesToTree($res1);
	//print_r($mic);die;
	$tmp=array();
	
		foreach($venue as $key=>$value){
				if($value['venue_name']){
					$venue[$key]=array('venue_id'=>$value['id'],
					'venue_name'=>$value['venue_name'],
					'address'=>$value['address']?$value['address']:"",
					'city'=>$value['city']?$value['city']:"",
					'zipcode'=>$value['zipcode']?$value['zipcode']:"",
					'state'=>$value['state']?$value['state']:"",					
				    "gambay_fee"=>$value['gambay_charge']?$value['gambay_charge']:0,
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
			
			foreach($result as $key=>$value){
			$data['category'][]=array('main_category_id'=>$value['main_id'],'main_category_name'=>$value['main_name'],'subcategory'=>array()
				);
			}
				
			foreach($result as $key=>$value){
			
				foreach($mic as $k1=>$v1){
					if($value['main_id']==$v1['menucategory_id']){
						$result[$key]['subcategory'][]=$v1;
					}
					$tmp[]=$value;
				}
			}
			if($res2){
			foreach($res2 as $key=>$value){
			if(!ISSET($final[$value['item_id']])){
				$final[$value['item_id']]=array('item_id'=>$value['item_id'],
				'item_name'=>$value['name']?$value['name']:"",
				'item_description'=>$value['item_desc']?$value['item_desc']:"",
				'item_pic'=>$value['pic']?BASE_PATH."timthumb.php?src=uploads/".$value['pic']:BASE_PATH."timthumb.php?src=uploads/640px-DecaturBourbons.jpg",
				'specials'=>$value['flag'],
				  //'tax'=>array(), 
				 'serving'=>array()
				);
			}
			if(!ISSET($final[$value['item_id']]['serving'][$value['serve_id']])){
			$final[$value['item_id']]['serving'][$value['serve_id']]=array(
				"serving_id"=>$value['serve_id']?$value['serve_id']:"",
				"serving_name"=>$value['type']?$value['type']:"",
				"pricing_name"=>$value['pr_name']?$value['pr_name']:"",
				"quantity"=>$value['quantity']?$value['quantity']:"",
				"price"=>$value['item_price']?$value['item_price']:"",
				"aggregate_price"=>$value['agg_price']?$value['agg_price']:"",
				"tax"=>array()
				);
			}
			
			$final[$value['item_id']]['serving'][$value['serve_id']]['tax'][]=array(
				"tax_id"=>$value['tax_id']?$value['tax_id']:"",
				"tax_name"=>$value['tax_name']?$value['tax_name']:"",
				"tax_percentage"=>$value['percentage']?$value['percentage']:0,
				"tax_description"=>$value['description']?$value['description']:""
				);
			
			}
			}
			if($final){
			foreach($final as $key=>$value){
		
		$data2=array();
		foreach($value['serving'] as $value2)
		{
			$data2[]=$value2;
		}
		$value['serving']=$data2;
		
		$result[]=$value;
		
		}}
		
		}
		
	else{
			$success="0";
			$msg="No main category exists";
		}
		
	

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$result,"venue"=>$venue));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));

function categoriesToTree(&$res1) {

    $map = array(
        0 => array('subcategory' => array())
    );

    foreach ($res1 as &$category) {
        $category['subcategory'] = array();
        $map[$category['sub_id']] = &$category;
    }

    foreach ($res1 as &$category) {
        $map[$category['sub_parent_id']]['subcategory'][] = &$category;
    }

    return $map[0]['subcategory'];

}
?>