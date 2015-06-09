<?php
/*if(isset($_SESSION['manager']) && isset($_SESSION['manager']['id'])){
      }
     else{
	$success=0;
	//session_destroy();
	$msg="Signed Out! Sign In Again!";
	header("Location: index.php?success=$success&msg=$msg");
}*/
require_once("../php_include/db_connection.php"); 
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
 //error_reporting(1);
  
  function randomFileNameGenerator($prefix){
    $r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
    if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
    else return $r;
  }

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
  $success=0;
  $msg="";

    //switch case to handle different events
  switch($_REQUEST['event']){

    case "create-venue":   
     // print_r($_POST);die;  
    $temp=array(0,0,0,0,0,0,0);
    $days=$_REQUEST['days']?$_REQUEST['days']:"";
    foreach($days as $key=>$value){
      $temp[$value-1]=1;
    }

    $d=implode(" ",$temp);

    $success=0;
    $venue=$_REQUEST['venuename'];
    $token=$_REQUEST['token'];
    $city=$_REQUEST['city'];
    $redirect=$_REQUEST['redirect'];
    $address=$_REQUEST['address'];
    $state=$_REQUEST['state'] ? $_REQUEST['state']: "";
    $zipcode=$_REQUEST['zipcode'];
    $image=$_FILES['image'];
    $website=$_REQUEST['website']?$_REQUEST['website']:"";
    $contact_email=$_REQUEST['contact_email']?$_REQUEST['contact_email']:"";
    $paypal=$_REQUEST['paypal'] ? $_REQUEST['paypal']: "";
    $fax=$_REQUEST['fax'] ? $_REQUEST['fax']: "";
    $vtype=$_REQUEST['type'];
    $mobile=$_REQUEST['contact'] ? $_REQUEST['contact']: "";
    $parking=$_REQUEST['parking'] ? $_REQUEST['parking']: "";
    $mgid=$_REQUEST['mgid'];
    $sq_foot=$_REQUEST['square_foot'];
    $start_time=$_REQUEST['start_time'];
    $end_time=$_REQUEST['end_time'];

    $awards=$_REQUEST['awards'];
    $seats=$_REQUEST['seats'];
    $tables=$_REQUEST['tables'];
    $description=$_REQUEST['description'] ? $_REQUEST['description']: "";

    $r[]=lookup($address.$zipcode.$city);
    $latitude=$r[0]['latitude'];
    $longitude=$r[0]['longitude'];
    $sth=$conn->prepare("select id from manager where token=:token");
    $sth->bindValue("token",$token);
    try{$count1=$sth->execute();}catch(Exception $e){}
    
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    $mid=$res[0]['id'];

    $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
    if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
      $success="1";
      $url=$randomFileName;
    }
    else
    $url="home_craft_default.png";
    
    $sth=$conn->prepare("insert into venue values(DEFAULT,:venue_name,:latitude,:longitude,:address,:mobile,:city,:state,:contact_email,:website,:zipcode,:paypal,:vtype,:fax, :parking,   :description,:sq_foot,:tables,:seats,:awards,1,0,NOW())");
    $sth->bindValue("venue_name",$venue);
    $sth->bindValue("latitude",$latitude);
    $sth->bindValue("longitude",$longitude);
    $sth->bindValue("address",$address);
    $sth->bindValue("mobile",$mobile);
    $sth->bindValue("city",$city);
    $sth->bindValue("state",$state);
    $sth->bindValue("contact_email",$contact_email);
    $sth->bindValue("website",$website);
    $sth->bindValue("zipcode",$zipcode);
    $sth->bindValue("paypal",$paypal);
    $sth->bindValue("vtype",$vtype);
    $sth->bindValue("fax",$fax);
    $sth->bindValue("sq_foot",$sq_foot);
    $sth->bindValue("tables",$tables);
    $sth->bindValue("seats",$seats);
    $sth->bindValue("awards",$awards);
    $sth->bindValue("parking",$parking);
    $sth->bindValue("description",$description);
    $count=0;
    try{$count=$sth->execute();
      $vid=$conn->lastInsertId();
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
        //$result=$sth->fetchAll(PDO::FETCH_ASSOC);

    if($count){
      $msg="Venue Created";
      $sth=$conn->prepare("insert into hours_of_operation values(DEFAULT,:venue_id,:days,:start_time,:end_time,NOW())");
      $sth->bindValue("days",$d);
      $sth->bindValue("venue_id",$vid);
      $sth->bindValue("start_time",$start_time);
      $sth->bindValue("end_time",$end_time);
      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }

      $sth=$conn->prepare("insert into manager_venue values(DEFAULT,:manager_id,:venue_id,1,0)");
      $sth->bindValue("manager_id",$mgid);
      $sth->bindValue("venue_id",$vid);
      try{$sth->execute();}
      catch(Exception $e){}
      if($url){

        $sth=$conn->prepare("insert into pictures values(DEFAULT,:venue_id,:url,0)");
        $sth->bindValue("url",$url);
        $sth->bindValue("venue_id",$vid);
        try{$sth->execute();}
        catch(Exception $e){}
      }
    }
    else{
      $redirect="create_venue.php";
      $msg="Error Occured";
    }
    header("Location: $redirect?success=$success&msg=$msg");
    break;
    
     case "edit-venue":   
    $temp=array(0,0,0,0,0,0,0);
    $days=$_REQUEST['days']?$_REQUEST['days']:"";
    if($days){
    foreach($days as $key=>$value){
      $temp[$value-1]=1;
    }}

    $d=implode(" ",$temp);

    $success=0;
    $vid=$_REQUEST['venue_id'];
    $venue=$_REQUEST['venuename'];
    $token=$_REQUEST['token'];
    $city=$_REQUEST['city'];
    $redirect=$_REQUEST['redirect'];
    $address=$_REQUEST['address'];
    $state=$_REQUEST['state'] ? $_REQUEST['state']: "";
    $website=$_REQUEST['website']?$_REQUEST['website']:"";
    $contact_email=$_REQUEST['contact_email']?$_REQUEST['contact_email']:"";
    $zipcode=$_REQUEST['zipcode'];
    $image=$_FILES['image'];
    $paypal=$_REQUEST['paypal'] ? $_REQUEST['paypal']: "";
    $fax=$_REQUEST['fax'] ? $_REQUEST['fax']: "";
    $vtype=$_REQUEST['type'];
    $mobile=$_REQUEST['contact'] ? $_REQUEST['contact']: "";
    $parking=$_REQUEST['parking'] ? $_REQUEST['parking']: "";
    $mgid=$_REQUEST['mgid'];
    $sq_foot=$_REQUEST['square_foot'];
    $start_time=$_REQUEST['start_time'];
    $end_time=$_REQUEST['end_time'];

    $awards=$_REQUEST['awards'];
    $seats=$_REQUEST['seats'];
    $tables=$_REQUEST['tables'];
    $description=$_REQUEST['description'] ? $_REQUEST['description']: "";

    $r[]=lookup($address.$zipcode.$city);
    $latitude=$r[0]['latitude'];
    $longitude=$r[0]['longitude'];
    $sth=$conn->prepare("select id from manager where token=:token");
    $sth->bindValue("token",$token);
    try{$count1=$sth->execute();}catch(Exception $e){}
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    $mid=$res[0]['id'];

    $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
    if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
      $success="1";
      $url=$randomFileName;
    }
    
    $sth=$conn->prepare("update venue set venue_name=:venue_name,latitude=:latitude,longitude=:longitude,address=:address, mobile=:mobile,city=:city,state=:state, contact_email=:contact_email, website=:website, zipcode=:zipcode,paypal_email=:paypal,venuetype_id=:vtype, fax_number=:fax,parking_information=:parking, description=:description, sq_footage=:sq_foot,tables=:tables,seats=:seats,awards=:awards, created_on=NOW() where id=:id");
    $sth->bindValue("venue_name",$venue);
    $sth->bindValue("latitude",$latitude);
    $sth->bindValue("longitude",$longitude);
    $sth->bindValue("address",$address);
    $sth->bindValue("mobile",$mobile);
    $sth->bindValue("city",$city);
    $sth->bindValue("state",$state);
    $sth->bindValue("contact_email",$contact_email);
    $sth->bindValue("website",$website);
    $sth->bindValue("zipcode",$zipcode);
    $sth->bindValue("paypal",$paypal);
    $sth->bindValue("vtype",$vtype);
    $sth->bindValue("fax",$fax);
    $sth->bindValue("sq_foot",$sq_foot);
    $sth->bindValue("tables",$tables);
    $sth->bindValue("seats",$seats);
    $sth->bindValue("awards",$awards);
    $sth->bindValue("parking",$parking);
    $sth->bindValue("description",$description);
    $sth->bindValue("id",$vid);
    $count=0;
    try{$count=$sth->execute();
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
 
        //$result=$sth->fetchAll(PDO::FETCH_ASSOC);
        
      $msg="Venue Updated";
      $sth=$conn->prepare("update hours_of_operation set days=:days,start_time=:start_time,end_time=:end_time,created_on=NOW() where venue_id=:venue_id");
      $sth->bindValue("days",$d);
      $sth->bindValue("venue_id",$vid);
      $sth->bindValue("start_time",$start_time);
      $sth->bindValue("end_time",$end_time);
      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }

      if($url){

        $sth=$conn->prepare("update pictures set url=:url where venue_id=:venue_id");
        $sth->bindValue("url",$url);
        $sth->bindValue("venue_id",$vid);
        try{$sth->execute();}
        catch(Exception $e){}
      }

    header("Location: $redirect?success=$success&msg=$msg");
    break;

    case "add-category":

    $category_name=$_REQUEST['name'];
    $vid=$_REQUEST['venue_id'];

    $sth=$conn->prepare("insert into menucategory values(DEFAULT,:name,:venue_id,:pic,1,0,NOW())");
    $sth->bindValue("name",$category_name);
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("pic","");
    try{$sth->execute();
      $categoryid=$conn->lastInsertId();
    }
    catch(Exception $e){
     echo $e->getMessage();
   }
   echo $categoryid;
   break;

   case 'sub-category':

   $subcategory=$_REQUEST['name'];
   $pid=$_REQUEST['parent_id'];
   $vid=$_REQUEST['venue_id'];
   $cid=$_REQUEST['menucategory_id'];
   $sth=$conn->prepare("insert into subcategory values(DEFAULT,:venue_id,:menucategory_id,:parent_id,:name,1,0)");
   $sth->bindValue("name",$subcategory);
   $sth->bindValue("venue_id",$vid);
   $sth->bindValue("menucategory_id",$cid);
   $sth->bindValue("parent_id",$pid);
   try{$sth->execute();
    $subcategoryid=$conn->lastInsertId();
  }
  catch(Exception $e){
    echo $e->getMessage();
  }
  echo $subcategoryid;
  break;

  case 'add-serving':
  $serving=$_REQUEST['name'];
  $vid=$_REQUEST['vid'];
  if($serving){
    $sth=$conn->prepare("insert into servings values(DEFAULT,:type,:venue_id,1,0)");
    $sth->bindValue("type",$serving);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();
    $sr_id=$conn->lastInsertId();
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
    echo  $sr_id;
  }
  break;

    case 'remove-serving':
//print_r($_REQUEST);die;
    $servingid=$_REQUEST['serving_id'];
    $id=$_REQUEST['venue_id'];
    
    $sth=$conn->prepare("select * from item_serving where serving_id=:serving_id and is_deleted=0");
    $sth->bindValue("serving_id",$servingid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    if(!count($result)){
      $sth=$conn->prepare("update servings set is_deleted=1,is_live=0 where id=:id");
      $sth->bindValue("id",$servingid);

      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo '1';
    }
    else echo '0';

    break;
    
        case 'remove-pricing-name':
//print_r($_REQUEST);die;
    $prid=$_REQUEST['pricing_name_id'];
    $id=$_REQUEST['venue_id'];
    
    $sth=$conn->prepare("select * from pricing where pricing_name_id=:pr_id");
    $sth->bindValue("pr_id",$prid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    if(!count($result)){
      $sth=$conn->prepare("update pricing_names set is_deleted=1,is_live=0 where id=:id");
      $sth->bindValue("id",$prid);

      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo '1';
    }
    else echo '0';

    break;

  case 'add-pricing-name':
  $pname=$_REQUEST['name'];
    $vid=$_REQUEST['vid'];
  
  if($pname){
    $sth=$conn->prepare("insert into pricing_names values(DEFAULT,:name,:venue_id,1,0)");
    $sth->bindValue("name",$pname);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();
    $pr_id=$conn->lastInsertId();
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
   echo $pr_id; 
}

    break;

    case 'add-tax':
    //print_r($_REQUEST);
    $tname=$_REQUEST['name'];
    if($tname){
      $sth=$conn->prepare("insert into tax values(DEFAULT,:name,:description,1,0,NOW())");
      $sth->bindValue("name",$tname);
      $sth->bindValue("description","");
      try{$sth->execute();
        $tid=$conn->lastInsertId();
      }
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo $tid;
    }

    break;

    case 'add-category-tax':
    //print_r($_REQUEST);
    $vid=$_REQUEST['vid'];
     $menu_id=$_REQUEST['menu_id'];
     $percentage=$_REQUEST['percentage'];
     $tax_id=$_REQUEST['tax_id'];
   $c= sizeof($_REQUEST['menu_id']);  
    if($c){
    for($i=0;$i<$c;$i++){
  
  if($percentage[$i]){
          $sth=$conn->prepare("select * from category_tax where menucategory_id=:menucategory_id and tax_id=:tax_id and is_deleted=0");
    $sth->bindValue("menucategory_id",$menu_id[$i]);
    $sth->bindValue("tax_id",$tax_id);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);

    if(!count($result )){
      $sth=$conn->prepare("insert into category_tax values(DEFAULT,:menucategory_id,:tax_id,:percentage,0)");
      $sth->bindValue("menucategory_id",$menu_id[$i]);
      $sth->bindValue("tax_id",$tax_id);
      $sth->bindValue("percentage",$percentage[$i]);
      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
     
   }
   else{
     $sth=$conn->prepare("update category_tax set percentage=:percentage where menucategory_id=:menucategory_id and tax_id=:tax_id and is_deleted=0");
      $sth->bindValue("menucategory_id",$menu_id[$i]);
      $sth->bindValue("tax_id",$tax_id);
      $sth->bindValue("percentage",$percentage[$i]);
      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
   }
  }
   }}

    break; 
    
    case 'get-tax':
    //print_r($_REQUEST);
    $menuid=$_REQUEST['menucategory_id'];
    //if($menuid){
     $sql="SELECT * FROM `tax` where id IN (select tax_id from category_tax where menucategory_id=:menucategory_id and is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    try{$sth->execute();}catch(Exception $e){ }
    $taxes=$sth->fetchAll(PDO::FETCH_ASSOC);
   if(count($taxes))
   echo json_encode($taxes);
   else
  echo '0';
 // }
  //echo '2';
    break;
    
        case 'get-subcategory':
    //print_r($_REQUEST);die;
    $menuid=$_REQUEST['menucategory_id'];//indicates the main category id
    $pid=$_REQUEST['parent_id'];// indicates the parent
    $subid=$_REQUEST['subcategory_id'];//indicates the id as in subcategory table
    
     $sql="SELECT name FROM `subcategory` where menucategory_id=:menucategory_id and id=:id and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    $sth->bindValue("id",$pid);
    try{$sth->execute();}catch(Exception $e){
    echo $e->getMessage(); 
    }
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    $sname=$result[0][0];
     $sql="SELECT * FROM `subcategory` where menucategory_id=:menucategory_id and parent_id=:parent_id and is_live=1 and is_deleted=0";
     
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    $sth->bindValue("parent_id",$pid);
    try{$sth->execute();}catch(Exception $e){
    echo $e->getMessage(); 
    }
    $subcategories=$sth->fetchAll(PDO::FETCH_ASSOC);
    //print_r($subcategories);
  if(count($subcategories))
   echo json_encode($subcategories);
   else{
$data=array('0',$sname);
 echo json_encode($data);
 
 //echo '0';
 //echo $sname;
 }
  //echo json_encode('s'=>'0','result'=>$result);
    break;

    case 'get_category_tax':
     // print_r($_REQUEST);
    $vid=$_REQUEST['venue_id'];
    $taxid=$_REQUEST['tax_id'];

    $sth=$conn->prepare("select menucategory.name,menucategory.id,category_tax.percentage from menucategory left join category_tax on category_tax.menucategory_id=menucategory.id and category_tax.tax_id=:tax_id and category_tax.is_deleted=0 where menucategory.venue_id=:venue_id and menucategory.is_live=1 and menucategory.is_deleted=0");
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("tax_id",$taxid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
    break;  
    
    case 'get-staff':
    //print_r($_REQUEST);
    $vid=$_REQUEST['vid'];
    $staff_id=$_REQUEST['staff_id'];

    $sth=$conn->prepare("select * from staff where venue_id=:venue_id and id=:id");
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$staff_id);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $staff=$sth->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($staff);
    break;
    
    case 'edit-staff':
  //print_r($_REQUEST);die;
   
    $vid=$_REQUEST['venue_id'];
    $staff_id=$_REQUEST['staff_id'];
    $username=$_REQUEST['username'];
    $email=$_REQUEST['email'];
    $mobile=$_REQUEST['mobile'];
    $redirect="manage_staff.php";
    
   $sth=$conn->prepare("update staff set mobile=:mobile,is_live=1,is_deleted=0 where venue_id=:venue_id and id=:id")	;
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$staff_id);
    $sth->bindValue("mobile",$mobile);

    try{$sth->execute();}
    catch(Exception $e){
     echo $e->getMessage();
    }
   //$success=1; 
   //$msg="Updation Sucessful";
header("Location: $redirect?success=$success&msg=$msg"); 
    break;  
    
    case 'delete-staff':
  //print_r($_REQUEST);
   
    $vid=$_REQUEST['vid'];
    $staff_id=$_REQUEST['staff_id'];
   $sth=$conn->prepare("update staff set is_live=0,is_deleted=1 where venue_id=:venue_id and id=:id")	;
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$staff_id);

    try{$sth->execute();}
    catch(Exception $e){
     echo $e->getMessage();
    }
     
    $sth=$conn->prepare("select * from staff where venue_id=:venue_id and id=:id and is_deleted=0");
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$staff_id);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $staff=$sth->fetchAll(PDO::FETCH_ASSOC);
    break;    
    
    case 'add-item':
//print_r($_REQUEST);die;
  $image=$_FILES['image'];
  $mid=$_REQUEST['menu_id'];
  $sid=$_REQUEST['sub_id'];
  $item_desc=$_REQUEST['item_description']?$_REQUEST['item_description']:"";
  /*$s2=$_REQUEST['levelmenu'];
  $s3=$_REQUEST['downmenu'];
  $s4=$_REQUEST['maxmenu'];
  $s5=$_REQUEST['dropmenu'];*/
  $special=$_REQUEST['specials']?1:0;
  $is_live=$_REQUEST['item_menu']?1:0;
  $serveid=$_REQUEST['serving_id'];
  $item_price=$_REQUEST['reg_price'];
  $vid=$_REQUEST['venue_id'];
  $redirect='add_item.php';
  $t1=$_REQUEST['taxes'];
  $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
          else
        $url="default_RecipeImage_332x284.jpg";
/*if($s5)
$sid=$s5;
elseif($s4)
$sid=$s4;
elseif($s3)
$sid=$s3;
elseif($s2)
$sid=$s2;
elseif($s1)
$sid=$s1;
else
$sid=0;*/

$sth=$conn->prepare("select * from subcategory where parent_id=:pid and is_live=1 and is_deleted=0");
    $sth->bindValue("pid",$sid);
    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    
    if(!count($res)){
    $sth=$conn->prepare("select name from subcategory where id=:id and is_live=1 and is_deleted=0");
    $sth->bindValue("id",$sid);
    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    $itemname=$result[0][0];
  
  $sth=$conn->prepare("select * from item where name=:name and menucategory_id=:mid and is_live=1 and is_deleted=0");
        $sth->bindValue("mid",$mid);
        $sth->bindValue("name",$itemname);
        try{$sth->execute();}
        catch(Exception $e){echo $e->getMessage();}
        $item=$sth->fetchAll(PDO::FETCH_ASSOC); 
  $itemid=$item[0]['id'];
  
  $sth=$conn->prepare("select * from item_serving where item_id=:item_id and serving_id=:serving_id and is_deleted=0");
        $sth->bindValue("item_id",$itemid);
        $sth->bindValue("serving_id",$serveid);
        try{$sth->execute();}
        catch(Exception $e){echo $e->getMessage();}
        $item_serve=$sth->fetchAll(PDO::FETCH_ASSOC);
        
        if(!count($item_serve)){
  
        $txid=implode(",",$t1);

        $sth=$conn->prepare("select percentage from category_tax where tax_id IN ($txid) and menucategory_id=:mid and is_deleted=0");
        $sth->bindValue("mid",$mid);
        try{$sth->execute();}
        catch(Exception $e){echo $e->getMessage();}
        $txp=$sth->fetchAll(PDO::FETCH_ASSOC);
   
        if(!count($item)){
  	$sth=$conn->prepare("insert into item values(DEFAULT,:name,:item_desc,:menucategory_id,:parent_id,:is_live,0,:image)");
        $sth->bindValue("name",$itemname);
        $sth->bindValue("item_desc",$item_desc);
        $sth->bindValue("menucategory_id",$mid);
        $sth->bindValue("parent_id",$sid);
        $sth->bindValue("image",$url);
        $sth->bindValue("is_live",$is_live);
        try{$sth->execute();
          $itemid=$conn->lastInsertId();
        }
        catch(Exception $e){
          echo $e->getMessage();
        }
        }
        
        $sth=$conn->prepare("insert into item_serving values(DEFAULT,:item_id,:serving_id,0)");
        $sth->bindValue("serving_id",$serveid);
        $sth->bindValue("item_id",$itemid);
        try{$sth->execute();}
        catch(Exception $e){
          echo $e->getMessage();
        }

            $sth=$conn->prepare("insert into pricing values(DEFAULT,:name_id,:qty,:special,:status,:item_id,:venue_id)");
            $sth->bindValue("name_id",2);
              $sth->bindValue("qty",99999999);
              $sth->bindValue("special",$special);
              $sth->bindValue("status",1);
            $sth->bindValue("item_id",$itemid);
            $sth->bindValue("venue_id",$vid);
            try{$sth->execute();
              $price_id=$conn->lastInsertId();
            }
            catch(Exception $e){
              echo $e->getMessage();
            }
            
            $agg=0;
            foreach($txp as $row){
              $tmp=($row['percentage']/100)*$item_price;
              $agg=$agg+$tmp;
            }
            $agg_price=$item_price+(0.03*$item_price)+$agg;

            $sth=$conn->prepare("insert into serving_price values(DEFAULT,:pricing_id,:serving_id,:item_price,:agg_price,0)");
            $sth->bindValue("pricing_id",$price_id);
            $sth->bindValue("serving_id",$serveid);
            $sth->bindValue("item_price",$item_price);
            $sth->bindValue("agg_price",$agg_price);
            try{$sth->execute();
              $pricingid=$conn->lastInsertId();
            }
            catch(Exception $e){
              echo $e->getMessage();
            }
            foreach($t1 as $tax){

              $sth=$conn->prepare("insert into pricing_tax values(DEFAULT,:s_pricing_id,:tax_id,0)");
              $sth->bindValue("s_pricing_id",$pricingid);
              $sth->bindValue("tax_id",$tax);
              try{$sth->execute();}
              catch(Exception $e){
                echo $e->getMessage();
              }
            }
         $msg="Item Successfully added";   
        }
        else{
        $msg="Item Serving Already added";
        $success='0';
        }
        
        }
        else{
        $msg="Child Category of last selected category exists";
        $success='0';
        }
       
         header("Location: $redirect?success=$success&msg=$msg");

    break; 
    
    case 'add-coupon':
     $vid=$_REQUEST['venue_id'];
      $coupon_name=$_REQUEST['coupon_name'];
       $value=$_REQUEST['value'];
       $st=$_REQUEST['status'];
       if($st) $status=1;
       else $status=0;
        $limit=$_REQUEST['limit']?$_REQUEST['limit']:'99999999';
      $expiry=$_REQUEST['expiry'];
       $image=$_FILES['image']?$_FILES['image']:""; 
       $coupon_code=rand(10000,999999);
       $redirect="coupons.php";
         $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
        else
        $url="Coupon-def.png";
     $sth=$conn->prepare("insert into coupons values(DEFAULT,:coupon_code,:coupon_name,:value,:expiry_date,:venue_id,:status,:limit,:pic,1,0,NOW())");
              $sth->bindValue("coupon_code",$coupon_code);
              $sth->bindValue("coupon_name",$coupon_name);
              $sth->bindValue("value",$value);
              $sth->bindValue("limit",$limit);
              $sth->bindValue("expiry_date",$expiry);
              $sth->bindValue("venue_id",$vid);
              $sth->bindValue("status",$status);
              $sth->bindValue("pic",$url);
              
              try{$sth->execute();}
              catch(Exception $e){
               // echo $e->getMessage();
              } 
              header("Location: $redirect?success=$success&msg=$msg");      
    break;
    
        case 'edit-coupon':
  //print_r($_REQUEST);die;
     $coupon_id=$_REQUEST['coupon_id']; 
     $vid=$_REQUEST['venue_id'];
      $coupon_name=$_REQUEST['coupon_name'];
       $value=$_REQUEST['value'];
       $st=$_REQUEST['status'];
       if($st) $status=1;
       else $status=0;
        $limit=$_REQUEST['limit']?$_REQUEST['limit']:'99999999';
      $expiry=$_REQUEST['expiry'];
       $image=$_FILES['image']; 
       $coupon_code=rand(10000,999999);
       $redirect="coupons.php";
         $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
      if($url)
     $sth=$conn->prepare("update coupons set coupon_name=:coupon_name,value=:value,expiry_date=:expiry_date,status=:status,`limit`=:limit,pic=:pic,created_on=NOW() where id=:id");
     else
     $sth=$conn->prepare("update coupons set coupon_name=:coupon_name,value=:value,expiry_date=:expiry_date,status=:status,`limit`=:limit,created_on=NOW() where id=:id");
              $sth->bindValue("id",$coupon_id);
              $sth->bindValue("coupon_name",$coupon_name);
              $sth->bindValue("value",$value);
              $sth->bindValue("limit",$limit);
              $sth->bindValue("expiry_date",$expiry);
              $sth->bindValue("status",$status);
             if($url) $sth->bindValue("pic",$url);
              
              try{$sth->execute();}
              catch(Exception $e){
               echo $e->getMessage();
              } 
              header("Location: $redirect?success=$success&msg=$msg");      
    break;

 case 'get-coupon':
    //print_r($_REQUEST);
    $vid=$_REQUEST['vid'];
    $coupon_id=$_REQUEST['coupon_id'];

    $sth=$conn->prepare("select * from coupons where venue_id=:venue_id and id=:id and is_deleted=0");
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$coupon_id);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $coupons=$sth->fetchAll(PDO::FETCH_ASSOC);
    //print_r($coupons);
   echo json_encode($coupons);
    break;
    
    case 'delete-coupon':
  //print_r($_REQUEST);
   
    $vid=$_REQUEST['vid'];
    $coupon_id=$_REQUEST['coupon_id'];
   $sth=$conn->prepare("update coupons set status=0,is_live=0,is_deleted=1 where venue_id=:venue_id and id=:id")	;
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$coupon_id);

    try{$sth->execute();}
    catch(Exception $e){
     echo $e->getMessage();
    }
     
    $sth=$conn->prepare("select * from coupons where venue_id=:venue_id and id=:id and is_deleted=0");
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("id",$coupon_id);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    } 
    $coupons=$sth->fetchAll(PDO::FETCH_ASSOC);
    //print_r($coupons);
   //echo json_encode($coupons);
    break;    


    case 'remove-category':

    $categoryid=$_REQUEST['menucategory_id'];
    $sth=$conn->prepare("select * from subcategory where menucategory_id=:menucategory_id and is_live=1 and is_deleted=0");
    $sth->bindValue("menucategory_id",$categoryid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $result=$sth->fetchAll();
    
    /*$sth=$conn->prepare("select * from item where menucategory_id=:menucategory_id and parent_id=0 and is_live=1 and is_deleted=0");
    $sth->bindValue("menucategory_id",$categoryid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $item=$sth->fetchAll();*/

    if(!count($result)){
      $sth=$conn->prepare("update menucategory set is_deleted=1,is_live=0 where id=:id");
      $sth->bindValue("id",$categoryid);

      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo '1';
    }
    else echo '0';
    break;

    case 'remove-sub-category':

    $categoryid=$_REQUEST['menucategory_id'];
    $subcategoryid=$_REQUEST['subcategory_id'];
    $sth=$conn->prepare("select * from subcategory where menucategory_id=:menucategory_id and parent_id=:pid and is_live=1 and is_deleted=0");
    $sth->bindValue("menucategory_id",$categoryid);
    $sth->bindValue('pid',$subcategoryid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    
 /*      $sth=$conn->prepare("select * from item where menucategory_id=:menucategory_id and parent_id=:parent_id and is_live=1 and is_deleted=0");
    $sth->bindValue("menucategory_id",$categoryid);
    $sth->bindValue("parent_id",$subcategoryid);

    try{$sth->execute();}
    catch(Exception $e){
      echo $e->getMessage();
    }
    $item=$sth->fetchAll();*/

    if(!count($result)){
      $sth=$conn->prepare("update subcategory set is_deleted=1,is_live=0 where id=:id");
      $sth->bindValue("id",$subcategoryid);

      try{$sth->execute();}
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo '1';
    }
    else echo '0';

    break;

    


    case "add-category1":

    $category_name=$_REQUEST['category_name'];
    $tax_name=$_REQUEST['tax_name'];
    $percentage=$_REQUEST['percentage'];
    $tax_desc=$_REQUEST['tax_desc'];
    $servings=$_REQUEST['serving_type'];
    $image=$_FILES['image'];
    $vid=$_REQUEST['venue_id'];
    $redirect="menu_items.php";
    $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
    if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
      $success="1";
      $url=$randomFileName;
    }

    $sth=$conn->prepare("insert into menucategory values(DEFAULT,:name,:venue_id,:pic,1,0,NOW())");
    $sth->bindValue("name",$category_name);
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("pic",$url);
    try{$sth->execute();
      $categoryid=$conn->lastInsertId();
    }
    catch(Exception $e){
        //echo $e->getMessage();
    }

    $sth=$conn->prepare("insert into tax values(DEFAULT,:tax_name,:percentage,:description,1,0,NOW())");
    $sth->bindValue("tax_name",$tax_name);
    $sth->bindValue("percentage",$percentage);
    $sth->bindValue("description",$tax_desc);
    try{$sth->execute();
      $taxid=$conn->lastInsertId();
    }
    catch(Exception $e){
        //echo $e->getMessage();
    }

    $sth=$conn->prepare("insert into category_tax values(DEFAULT,:menucategoryid,:tax_id,0)");
    $sth->bindValue("menucategoryid",$categoryid);
    $sth->bindValue("tax_id",$taxid);
    try{$sth->execute();}
    catch(Exception $e){
        //echo $e->getMessage();
    }

    if($servings){
      foreach($servings as $serving){
        $sth=$conn->prepare("insert into servings values(DEFAULT,:type,:menucategoryid)");
        $sth->bindValue("type",$serving);
        $sth->bindValue("menucategoryid",$categoryid);
        try{$sth->execute();}
        catch(Exception $e){
        //echo $e->getMessage();
        }
      }
    }

    header("Location: $redirect?success=$success&msg=$msg");
    break;

    case 'sub-category1':

    $subcategories=$_REQUEST['subcategory_name'];
    $pid=$_REQUEST['parent_id'];
    $vid=$_REQUEST['venue_id'];
    $cid=$_REQUEST['category_id'];
        //if(!$pid)
    $redirect="menu_items.php";
        //else
        //$redirect="subcategory_list.php?menu_id=1"
    foreach($subcategories as $subcategory){
      $sth=$conn->prepare("insert into subcategory values(DEFAULT,:venue_id,:menucategory_id,:parent_id,:name,1,0)");
      $sth->bindValue("name",$subcategory);
      $sth->bindValue("venue_id",$vid);
      $sth->bindValue("menucategory_id",$cid);
      $sth->bindValue("parent_id",$pid);
      try{$sth->execute();
        $sub_categoryid=$conn->lastInsertId();
      }
      catch(Exception $e){
        //echo $e->getMessage();
      }
    }
    header("Location: $redirect?success=$success&msg=$msg");
    break;

    case 'add-item1':

    $cid=$_REQUEST['menucategory_id'];
    $itemname=$_REQUEST['itemname'];
    $serveid=$_REQUEST['serving_id'];
    
    $item_price=$_REQUEST['item_price'];
    $qty=$_REQUEST['qty'];
    $image=$_FILES['image'];
    $sp=$_REQUEST['special_flag'];
        $aid=$_REQUEST['pricing_name_id'];//active id
        $vid=$_REQUEST['vid'];
        $pid=$_REQUEST['pid'];
        $c=count($_REQUEST['p_name']);
        $redirect="add_item.php";
        $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
        $t1=$_REQUEST['taxes'];
        $txid=implode(",",$t1);

        $sth=$conn->prepare("select percentage from tax where id IN ($txid) and is_live=1 and is_deleted=0");
        try{$sth->execute();}
        catch(Exception $e){}
        $txp=$sth->fetchAll();

        $sth=$conn->prepare("insert into item values(DEFAULT,:name,:menucategory_id,:parent_id,1,0,:image)");
        $sth->bindValue("name",$itemname);
        $sth->bindValue("menucategory_id",$cid);
        $sth->bindValue("parent_id",$pid);
        $sth->bindValue("image",$url);
        try{$sth->execute();
          $itemid=$conn->lastInsertId();
        }
        catch(Exception $e){
          echo $e->getMessage();
        }
        
        $sth=$conn->prepare("insert into item_serving values(DEFAULT,:item_id,:serving_id,0)");
        $sth->bindValue("serving_id",$serveid);
        $sth->bindValue("item_id",$itemid);
        try{$sth->execute();}
        catch(Exception $e){
          echo $e->getMessage();
        }
        
        
        for($i=0;$i<=$c-1;$i++){
          if($item_price[$i]){
            $sth=$conn->prepare("insert into pricing values(DEFAULT,:name_id,:qty,:special,:status,:item_id,:venue_id)");
            $sth->bindValue("name_id",$i+1);
            if($qty[$i]){
              $sth->bindValue("qty",$qty[$i]);
            }
            else{
              $sth->bindValue("qty",99999999);
            }
            if($i+1==$aid){
              $sth->bindValue("special",1);
              $sth->bindValue("status",1);
            }
            else{
              $sth->bindValue("special",0);
              $sth->bindValue("status",0);
            }
            $sth->bindValue("item_id",$itemid);
            $sth->bindValue("venue_id",$vid);
            
            try{$sth->execute();
              $price_id=$conn->lastInsertId();
            }
            catch(Exception $e){
              echo $e->getMessage();
            }
            
            $agg=0;
            foreach($txp as $row){
              $tmp=($row['percentage']/100)*$item_price[$i];
              $agg=$agg+$tmp;
            }
            $agg_price=$item_price[$i]+(0.03*$item_price[$i])+$agg;

            $sth=$conn->prepare("insert into serving_price values(DEFAULT,:pricing_id,:serving_id,:item_price,:agg_price,0)");
            $sth->bindValue("pricing_id",$price_id);
            $sth->bindValue("serving_id",$serveid);
            $sth->bindValue("item_price",$item_price[$i]);
            $sth->bindValue("agg_price",$agg_price);
            try{$sth->execute();
              $pricingid=$conn->lastInsertId();
            }
            catch(Exception $e){
              echo $e->getMessage();
            }
            foreach($t1 as $tax){

              $sth=$conn->prepare("insert into pricing_tax values(DEFAULT,:s_pricing_id,:tax_id,0)");
              $sth->bindValue("s_pricing_id",$pricingid);
              $sth->bindValue("tax_id",$tax);
              try{$sth->execute();}
              catch(Exception $e){
                echo $e->getMessage();
              }
            }
            
          }
        }
        header("Location: $redirect?menu_id=$cid&pid=$pid");
        
        break;
        
        case 'add_tax':
        //print_r($_REQUEST);die;
        $categoryid=$_REQUEST['menucategory_id'];
        $tax_name=$_REQUEST['tax_name'];
        $percentage=$_REQUEST['percentage'];
        $tax_desc=$_REQUEST['tax_desc'];
        
        //if(!$pid)
        $redirect="menu_items.php";
        //else
        //$redirect="subcategory_list.php?menu_id=1"
        $sth=$conn->prepare("insert into tax values(DEFAULT,:tax_name,:percentage,:description,1,0,NOW())");
        $sth->bindValue("tax_name",$tax_name);
        $sth->bindValue("percentage",$percentage);
        $sth->bindValue("description",$tax_desc);
        try{$sth->execute();
          $taxid=$conn->lastInsertId();
        }
        catch(Exception $e){
          echo $e->getMessage();
        }
        
        $sth=$conn->prepare("insert into category_tax values(DEFAULT,:menucategoryid,:tax_id,0)");
        $sth->bindValue("menucategoryid",$categoryid);
        $sth->bindValue("tax_id",$taxid);
        try{$sth->execute();}
        catch(Exception $e){
        //echo $e->getMessage();
        }
        header("Location: $redirect?success=$success&msg=$msg");
        break;
        
        case 'delete-tax':
        
        $taxid=$_REQUEST['tax_id'];
        $categoryid=$_REQUEST['menucategory_id'];
        $redirect="menu_items.php";
        
        foreach($taxid as $key=>$value){
          $sth=$conn->prepare("update category_tax set is_deleted=1 where tax_id=:tax_id and menucategory_id=:menucategory_id");
          $sth->bindValue("tax_id",$value);
          $sth->bindValue("menucategory_id",$categoryid);
          
          try{$sth->execute();}
          catch(Exception $e){
        //echo $e->getMessage();
          }
        }
        header("Location: $redirect?success=$success&msg=$msg");
        
        break;
        
      }
 
      
      ?>