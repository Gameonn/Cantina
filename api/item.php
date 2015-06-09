<?php
//this is an api items

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$subcategory_id=$_REQUEST['parent_id'];
$maincategory_id=$_REQUEST['menucategory_id'];


// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+
	
	$sql="SELECT item.id as item_id,item.name as item_name,item.item_description as item_desc,servings.type as serving, item.pic as item_pic ,tax.id as tax_id,tax.tax_name,tax.description, item_tax.percentage,pricing.quantity as qty, pricing_names.name as pr_name, serving_price.serving_id as serve_id, serving_price.item_price,serving_price.agg_price 
from item left join pricing on pricing.item_id=item.id and pricing.status=1 left join pricing_names on pricing_names.id=pricing.pricing_name_id left join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 left join servings on servings.id=serving_price.serving_id left join pricing_tax on pricing_tax.serving_price_id=serving_price.id and pricing_tax.is_deleted=0 left join tax on tax.id=pricing_tax.tax_id and tax.is_live=1 and tax.is_deleted=0 left join item_tax on item_tax.tax_id=pricing_tax.tax_id and item_tax.item_id=item.id  where item.parent_id=:parent_id and item.menucategory_id=:menucategory_id and item.is_live=1 and item.is_deleted=0";

/*$sql="SELECT item.id as item_id,item.name as item_name,item.item_description as item_desc,servings.type as serving, item.pic as item_pic ,tax.id as tax_id,tax.tax_name,tax.description, category_tax.percentage,pricing.quantity as qty, pricing_names.name as pr_name, serving_price.serving_id as serve_id, serving_price.item_price,serving_price.agg_price 
from item left join pricing on pricing.item_id=item.id and pricing.status=1 left join pricing_names on pricing_names.id=pricing.pricing_name_id left join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 left join servings on servings.id=serving_price.serving_id left join pricing_tax on pricing_tax.serving_price_id=serving_price.id and pricing_tax.is_deleted=0 left join tax on tax.id=pricing_tax.tax_id and tax.is_live=1 and tax.is_deleted=0 left join category_tax on category_tax.tax_id=pricing_tax.tax_id and category_tax.menucategory_id=item.menucategory_id  where item.parent_id=:parent_id and item.menucategory_id=:menucategory_id and item.is_live=1 and item.is_deleted=0";*/
	
	$sth=$conn->prepare($sql);
	$sth->bindValue("parent_id",$subcategory_id);
	$sth->bindValue("menucategory_id",$maincategory_id);
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	if(count($result)){
	$success=1;
		//get items,pricing,servings
			foreach($result as $key=>$value){
			if(!ISSET($final[$value['item_id']])){
				$final[$value['item_id']]=array('item_id'=>$value['item_id']?$value['item_id']:"",
				'item_name'=>$value['item_name']?$value['item_name']:"",
				'item_description'=>$value['item_desc']?$value['item_desc']:"",
				'item_pic'=>$value['item_pic'] ? BASE_PATH."timthumb.php?src=uploads/".$value['item_pic'] : BASE_PATH."timthumb.php?src=uploads/home_craft_default.png",
				  //'tax'=>array(), 
				 'serving'=>array()
				);
			}
			
			if(!ISSET($final[$value['item_id']]['serving'][$value['serve_id']])){
			$final[$value['item_id']]['serving'][$value['serve_id']]=array(
				"serving_id"=>$value['serve_id']?$value['serve_id']:"",
				"serving_name"=>$value['serving']?$value['serving']:"",
				"pricing_name"=>$value['pr_name']?$value['pr_name']:"",
				"quantity"=>$value['qty']?$value['qty']:"",
				"price"=>$value['item_price']?$value['item_price']:0,
				"gambay_price"=>$value['item_price']?$value['item_price']+0.03*$value['item_price']:0,
				"aggregate_price"=>$value['agg_price']?$value['agg_price']:0,
				"tax"=>array()
				);
			}
			
			$final[$value['item_id']]['serving'][$value['serve_id']]['tax'][]=array(
				"tax_id"=>$value['tax_id']?$value['tax_id']:"",
				"tax_name"=>$value['tax_name']?$value['tax_name']:"",
				"tax_description"=>$value['description']?$value['description']:"",
				"tax_percentage"=>$value['percentage']?$value['percentage']:0
				);
				
			}
	$data=array();
	$serves=array();
	$dam=array();
	$tmp=array();
	foreach($final as $key=>$value){
		$data2=array();
		foreach($value['serving'] as $value2)
		{
			$data2[]=$value2;
		}
		$value['serving']=$data2;
		$data[]=$value;
	}
	
	
		}

		
	


// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

//if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
/*}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
*/
?>