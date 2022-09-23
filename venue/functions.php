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
      
      function sendEmail($email,$subjectMail,$bodyMail,$email_back){

        $mail = new PHPMailer(true); 
        $mail->IsSMTP(); // telling the class to use SMTP
        try {
          //$mail->Host       = SMTP_HOST; // SMTP server
          $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
          $mail->SMTPAuth   = true;                  // enable SMTP authentication
          $mail->Host       = SMTP_HOST; // sets the SMTP server
          $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
          $mail->Username   = SMTP_USER; // SMTP account username
          $mail->Password   = SMTP_PASSWORD;        // SMTP account password
          $mail->AddAddress($email, '');     // SMTP account password
          $mail->SetFrom(SMTP_EMAIL, SMTP_NAME);
          $mail->AddReplyTo($email_back, SMTP_NAME);
          $mail->Subject = $subjectMail;
          $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automaticall//y
          $mail->MsgHTML($bodyMail) ;
          if(!$mail->Send()){
           $success='0';
           $msg="Error in sending mail";
         }else{
           $success='1';
         }
       } catch (phpmailerException $e) {
          $msg=$e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
          $msg=$e->getMessage(); //Boring error messages from anything else!
        }
       //echo $msg;
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
           //print_r($_POST);die;  
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

         $r[]=lookup($address.','.$zipcode.','.$city);
		 //$r1[]=lookup($zipcode);
		  if(!$r){
		  $r[]=lookup($zipcode);
		  }
		
        $latitude=$r[0]['latitude']?$r[0]['latitude']:"";
        $longitude=$r[0]['longitude']?$r[0]['longitude']:"";
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
        if(!url){
          $url="home_craft_default.png";
        }
		
		 $sql="select * from venue where venue_name=:venue_name and is_live=1 and is_deleted=0";
           $sth=$conn->prepare($sql);
           $sth->bindValue("venue_name",$venue);
           try{$sth->execute();}catch(Exception $e){
        //echo $e->getMessage();
           }
           $res=$sth->fetchAll();
		
		// select email for mail sending
		$sql1="SELECT email from users where token=:token";
		$stmt=$conn->prepare($sql1);
		$stmt->bindValue("token",$token);
		try{$stmt->execute();}
		catch(Exception $e){ 
		//echo $e->getMessage();
		}
		$a=$stmt->fetchAll();
		$email=$a[0]['email'];
		
		if(!count($res)){
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
         // echo $e->getMessage();
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
            //echo $e->getMessage();
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
		  
		  // email_sending
			$subject_msg="Venue is created successfully!";
			$body_msg="Venue is  created successfully by Gambay Admin. <br> Enjoy!";
			sendEmail($email, $subject_msg, $body_msg, SMTP_EMAIL);
		  
        }
        else{
          $redirect="create_venue.php";
          $msg="Error Occured";
        }
		}
		else{
		$success='0';
		$msg="Venue Already Existing";
		}
        header("Location: $redirect?success=$success&msg=$msg");
        break;
        
		
		case "mail_send":
		$vid=$_REQUEST['venue_id'];
		$bm=$_REQUEST['body_msg'];
		$redirect="view_venue.php";
		$success=0;$msg=0;
			$sql="select email from admin";
			$sth=$conn->prepare($sql);
		    try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
			$res=$sth->fetchAll();
			$email=$res[0]['email'];
			$sql="select venue_name from venue where id=:id";
			$sth=$conn->prepare($sql);
			$sth->bindValue('id',$vid);
		    try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
			$res1=$sth->fetchAll();
			$venue_name=$res1[0]['venue_name'];
			
			$subject_msg="Update Stripe Account Email";
			
			$body_msg="Renew stripe account For Venue. ".$venue_name."<br> " .$bm ;
		
			sendEmail($email, $subject_msg, $body_msg, SMTP_EMAIL);
			
		header("Location: $redirect?success=$success&msg=$msg");
		break;
		
		case 'get_lat_long':
		//print_r($_REQUEST);
		$address=$_REQUEST['address'];
		 $r[]=lookup($address);
		$latitude=$r[0]['latitude']?$r[0]['latitude']:"";
       $longitude=$r[0]['longitude']?$r[0]['longitude']:"";
	   if($latitude && $longitude)
	   echo '1';
	   else
	   echo '0';
		break;
		
		case 'edit-profile':
		
		$token=$_REQUEST['key'];
		$mobile=$_REQUEST['mobile'];
		$name=$_REQUEST['name'];
		$image=$_FILES['pic'];
		$redirect=$_REQUEST['redirect'];
		 $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
         if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          $success="1";
          $url=$randomFileName;
        }
		if($url)		  
		  $sth=$conn->prepare("update manager set mobile_number=:mobile,pic=:pic,name=:name where token=:token");
		  else
		  $sth=$conn->prepare("update manager set mobile_number=:mobile,name=:name where token=:token");
          $sth->bindValue("mobile",$mobile);
          if($url) $sth->bindValue("pic",$url);
          $sth->bindValue("token",$token);
		  $sth->bindValue("name",$name);
          try{$sth->execute();
		  $msg="Profile Updated";
		  $success=1;
		  }
          catch(Exception $e){
            //echo $e->getMessage();
          }
		  
		  header("Location: $redirect?success=$success&msg=$msg");
		break;
				
        case "edit-venue":  
//print_r($_POST);die;		
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

		  $r[]=lookup($address.','.$zipcode.','.$city);
		 //$r1[]=lookup($zipcode);
		  if(!$r){
		  $r[]=lookup($zipcode);
		  }
		 
		  //print_r($r1);
		  
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
		 // print_r($_FILES);echo $url;die;
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
            //echo $e->getMessage();
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
            //echo $e->getMessage();
          }

          if($url){
			$sth=$conn->prepare("select * from pictures where venue_id=:venue_id");
            $sth->bindValue("venue_id",$vid);
            try{$sth->execute();}
            catch(Exception $e){}
			$res12=$sth->fetchAll();
			if(count($res12)){
			$sth=$conn->prepare("update pictures set url=:url where venue_id=:venue_id");
            $sth->bindValue("url",$url);
            $sth->bindValue("venue_id",$vid);
            try{$sth->execute();}
            catch(Exception $e){}
			}
			else{
			$sth=$conn->prepare("Insert into pictures value(DEFAULT,:venue_id,:url,0)");
            $sth->bindValue("url",$url);
            $sth->bindValue("venue_id",$vid);
            try{$sth->execute();}
            catch(Exception $e){}
			}
          }

          header("Location: $redirect?success=$success&msg=$msg");
          break;

          case 'forgot_password':
          $email=$_REQUEST['email'];
          $redirect='forgot_password.php';
          if(!($email)){
           $success="0";
           $msg="Incomplete Parameters";
         }
         else{
           $sql="select * from manager where email=:email and is_deleted=0";
           $sth=$conn->prepare($sql);
           $sth->bindValue("email",$email);
           try{$sth->execute();}catch(Exception $e){
        //echo $e->getMessage();
           }
           $res=$sth->fetchAll();

           if(count($res)){ 
             $token=md5($email);
             $sql="update manager set token=:token where email=:email";
             $sth=$conn->prepare($sql);
             $sth->bindValue("email",$email);
             $sth->bindValue("token",$token);
             $count=0;
             try{$count=$sth->execute();}catch(Exception $e){
        //echo $e->getMessage();
             }
             if($count){
              $success="1";
              $msg="An email is sent to you";
              
              $sql="select username from manager where email=:email";
              $sth=$conn->prepare($sql);
              $sth->bindValue('email',$email);
              try{ $sth->execute();}catch(Exception $e){}
              $result=$sth->fetchAll();
              $username=ucwords($result[0]['username']);
              sendEmail($email,"Gambay - Recover Password",
                "<div style='font-size:20px;line-height:1.6;'>
                <p>Dear $username,</p>
                <br>
                <p>we have received your password reset request.</p>
                <p>Please follow the link below to set a new password:</p>
                <p><a href='".BASE_PATH."venue/reset_password.php?token={$token}'>".BASE_PATH."venue/reset_password.php?token={$token}</a></p>
                <p>In case you have any questions or have not requested to change your password, please reach out to admin@gambay.com.</p>
                <br>
                <p>Best,</p>
                <p>Gambay Users</p>
                <p><a href='http://www.gambay.com'>www.gambay.com</a></p>
              </div>"
              ,SMTP_EMAIL);
            }else{
              $success="0";
              $msg="Error occurred";
            }
          }
          else{
            $success="0";
            $msg="Invalid Email ";
          }
        }
        header("Location: $redirect?success=$success&msg=$msg");
        break;
        
        case "reset_password":
        //print_r($_REQUEST);die;
        $token=$_REQUEST["token"];
        $password=$_REQUEST["password"];
        $confirm=$_REQUEST["confirm"];
        $base=BASE_PATH.'/venue/';
		
		$sql="SELECT email from users where token=:token";
		$stmt=$conn->prepare($sql);
		$stmt->bindValue("token",$token);
		try{$stmt->execute();}
		catch(Exception $e){ 
		//echo $e->getMessage();
		}
		$a=$stmt->fetchAll();
		$email=$a[0]['email'];
		
        if($password==$confirm){
          $code=md5($token);
          $sth=$conn->prepare("update manager set password=:password,token=:code where token=:token");
          $sth->bindValue("token",$token);
          $sth->bindValue("code",$code);
          $sth->bindValue("password",md5($password));
          $count=0;
          try{$count=$sth->execute();}catch(Exception $e){
		 // echo $e->getMessage();
		  }
          if($count){
           $success=1;
           $msg="Password changed successfully";
           $add="index.php";
           $sp="?success=$success&msg={$msg}";
   
			// email_sending
			$subject_msg="Gambay Account Password Changed!";
			$body_msg="The password for your Gambay account changed successfully! <br> To get back into your account, you'll need to reset your password.";
			sendEmail($email, $subject_msg, $body_msg, SMTP_EMAIL);
         }
       }else{
        $success=0;
        $msg="Passwords didn't match";
        $add="reset_password.php?token=$token";
        $sp="&success=$success&msg={$msg}";
      }
      header("Location: $base$add$sp");
      break;

      case "add-category":

      $category_name=$_REQUEST['name'];
      $vid=$_REQUEST['venue_id'];
      $category_name= strip_tags($category_name);
      //$category_name = htmlspecialchars($category_name);
      $sth=$conn->prepare("insert into menucategory values(DEFAULT,:name,:venue_id,:pic,1,0,NOW())");
      $sth->bindValue("name",$category_name);
      $sth->bindValue("venue_id",$vid);
      $sth->bindValue("pic","");
      try{$sth->execute();
        $categoryid=$conn->lastInsertId();
      }
      catch(Exception $e){
       //echo $e->getMessage();
     }
     echo $categoryid;
     break;

     case 'sub-category':

     $subcategory=$_REQUEST['name'];
     $subcategory = strip_tags($subcategory);
     //$subcategory= htmlspecialchars($subcategory);
     $pid=$_REQUEST['parent_id'];
     $vid=$_REQUEST['venue_id'];
     $cid=$_REQUEST['menucategory_id'];
     
     $sth=$conn->prepare("select * from item where menucategory_id=:menucategory_id and parent_id=:parent_id and is_live=1 and is_deleted=0");
     $sth->bindValue("menucategory_id",$cid);
     $sth->bindValue("parent_id",$pid);

     try{$sth->execute();}
     catch(Exception $e){
      //echo $e->getMessage();
    }
    $item=$sth->fetchAll();
    if(!count($item)){
     $sth=$conn->prepare("insert into subcategory values(DEFAULT,:venue_id,:menucategory_id,:parent_id,:name,1,0)");
     $sth->bindValue("name",$subcategory);
     $sth->bindValue("venue_id",$vid);
     $sth->bindValue("menucategory_id",$cid);
     $sth->bindValue("parent_id",$pid);
     try{$sth->execute();
      $subcategoryid=$conn->lastInsertId();
    }
    catch(Exception $e){
     // echo $e->getMessage();
    }
    echo $subcategoryid;
  }
  else
    echo 0;
  break;

  case 'add-serving':
  $serving=$_REQUEST['name'];
  $serving = strip_tags($serving);
  //$serving= htmlspecialchars($serving);
  $vid=$_REQUEST['vid'];
  if($serving){
    $sth=$conn->prepare("insert into servings values(DEFAULT,:type,:venue_id,1,0)");
    $sth->bindValue("type",$serving);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();
      $sr_id=$conn->lastInsertId();
    }
    catch(Exception $e){
      //echo $e->getMessage();
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
    //echo $e->getMessage();
  }
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);

  if(!count($result)){
    $sth=$conn->prepare("update servings set is_deleted=1,is_live=0 where id=:id");
    $sth->bindValue("id",$servingid);

    try{$sth->execute();}
    catch(Exception $e){
      //echo $e->getMessage();
    }
    echo '1';
  }
  else echo '0';

  break;

  case "set_stripe_status":
   $vid=$_REQUEST['venue_id'];

  $sth=$conn->prepare("select manager.email from manager join manager_venue on manager_venue.manager_id=manager.id where manager_venue.venue_id=:venue_id ");
  
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}
  catch(Exception $e){ }
  $res=$sth->fetchAll(PDO::FETCH_ASSOC);
  $email=$res[0]['email'];
   
  $sth=$conn->prepare("select * from stripe_connect where venue_id=:venue_id");
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}
  catch(Exception $e){ }
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$msg1="Stripe Account Linking Successful";
  
  if(count($result)){
    $sth=$conn->prepare("update stripe_connect set is_linked=1 where venue_id=:id");
    $sth->bindValue("id",$vid);
    try{$sth->execute();
	sendEmail($email,$msg1,$msg1,SMTP_EMAIL);
	}
    catch(Exception $e){}
  }
  break;
  
  case 'remove-pricing-name':
      //print_r($_REQUEST);die;
  $prid=$_REQUEST['pricing_name_id'];
  $id=$_REQUEST['venue_id'];

  $sth=$conn->prepare("select * from pricing where pricing_name_id=:pr_id");
  $sth->bindValue("pr_id",$prid);

  try{$sth->execute();}
  catch(Exception $e){
   // echo $e->getMessage();
  }
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);

  if(!count($result)){
    $sth=$conn->prepare("update pricing_names set is_deleted=1,is_live=0 where id=:id");
    $sth->bindValue("id",$prid);

    try{$sth->execute();}
    catch(Exception $e){
      //echo $e->getMessage();
    }
    echo '1';
  }
  else echo '0';

  break;

  case 'add-pricing-name':
  $pname=$_REQUEST['name'];
  $pname= strip_tags($pname);
  //$pname= htmlspecialchars($pname);
  $vid=$_REQUEST['vid'];

  if($pname){
    $sth=$conn->prepare("insert into pricing_names values(DEFAULT,:name,:venue_id,1,0)");
    $sth->bindValue("name",$pname);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();
      $pr_id=$conn->lastInsertId();
    }
    catch(Exception $e){
     // echo $e->getMessage();
    }
    echo $pr_id; 
  }

  break;
  
   case 'set-status':
   print_r($_REQUEST);
  $item_id=$_REQUEST['item_id'];
  $serving_id=$_REQUEST['serving_id'];
  $flag=$_REQUEST['flag'];
  $field=$_REQUEST['field'];
  
   $sth=$conn->prepare("select pricing.id as pr_id,serving_price.id as sp_id from pricing join serving_price on serving_price.pricing_id=pricing.id and serving_price.serving_id=:serving_id where item_id=:item_id");
			$sth->bindValue("item_id",$item_id);
			$sth->bindValue("serving_id",$serving_id);
			try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
			$item_pricing=$sth->fetchAll(PDO::FETCH_ASSOC);
			$pr_id=$item_pricing[0]['pr_id'];
			
  if($field=='specials'){
  $sql="update pricing set special_flag=:sp where id=:id";
  }
  else{
  $sql="update pricing set status=:sp where id=:id";
  }
    $sth=$conn->prepare($sql);
    $sth->bindValue("id",$pr_id);
	$sth->bindValue("sp",$flag);
    try{$sth->execute();
    }
    catch(Exception $e){
      //echo $e->getMessage();
    } 
  
  break;

  case 'add-tax':
          //print_r($_REQUEST);
  $tname=$_REQUEST['name'];
   $vid=$_REQUEST['vid'];
  $tname = strip_tags($tname);
  //$tname = htmlspecialchars($tname);
  if($tname){
    $sth=$conn->prepare("insert into tax values(DEFAULT,:name,:description,:venue_id,1,0,NOW())");
    $sth->bindValue("name",$tname);
	$sth->bindValue("venue_id",$vid);
    $sth->bindValue("description","");
    try{$sth->execute();
      $tid=$conn->lastInsertId();
    }
    catch(Exception $e){
      //echo $e->getMessage();
    }
    echo $tid;
  }

  break;

  case 'add-category-tax':
          //print_r($_REQUEST);die;
  $vid=$_REQUEST['vid'];
  $menu_id=$_REQUEST['menu_id'];
  $percentage=$_REQUEST['percentage'];
  $tax_id=$_REQUEST['tax_id'];
    $tax_name=$_REQUEST['tax_name'];
  $c= sizeof($_REQUEST['menu_id']);  
  
     $sth=$conn->prepare("update tax set tax_name=:name where venue_id=:venue_id and id=:tax_id and is_deleted=0");
         $sth->bindValue("venue_id",$vid);
         $sth->bindValue("tax_id",$tax_id);
         $sth->bindValue("name",$tax_name);
         try{$sth->execute();}
         catch(Exception $e){}
  
  if($c){
    for($i=0;$i<$c;$i++){
      
      if($menu_id[$i]){
        $sth=$conn->prepare("select * from category_tax where menucategory_id=:menucategory_id and tax_id=:tax_id and is_deleted=0");
        $sth->bindValue("menucategory_id",$menu_id[$i]);
        $sth->bindValue("tax_id",$tax_id);

        try{$sth->execute();}
        catch(Exception $e){} 
        $result[$i]=$sth->fetchAll(PDO::FETCH_ASSOC);

        if(!count($result[$i])){
          $sth=$conn->prepare("insert into category_tax values(DEFAULT,:menucategory_id,:tax_id,:percentage,0)");
          $sth->bindValue("menucategory_id",$menu_id[$i]);
          $sth->bindValue("tax_id",$tax_id);
          $sth->bindValue("percentage",$percentage[$i]);
          try{$sth->execute();}
          catch(Exception $e){}
        }
        else{
         $sth=$conn->prepare("update category_tax set percentage=:percentage where menucategory_id=:menucategory_id and tax_id=:tax_id and is_deleted=0");
         $sth->bindValue("menucategory_id",$menu_id[$i]);
         $sth->bindValue("tax_id",$tax_id);
         $sth->bindValue("percentage",$percentage[$i]);
         try{$sth->execute();}
         catch(Exception $e){}
        }
    }
  }
  }

  break; 

  case 'get-tax':
          //print_r($_REQUEST);
  $menuid=$_REQUEST['menucategory_id'];
          //if($menuid){
          // $sql="SELECT * FROM `tax` where id IN (select tax_id from category_tax where menucategory_id=:menucategory_id and is_deleted=0)";
  $sql="SELECT tax.*,category_tax.percentage FROM `tax` left join category_tax on category_tax.tax_id=tax.id and category_tax.menucategory_id=:menucategory_id  where tax.id IN (select tax_id from category_tax where menucategory_id=:menucategory_id  and is_deleted=0)";
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
            //echo $e->getMessage(); 
          }
          $result=$sth->fetchAll(PDO::FETCH_ASSOC);
          $sname=$result[0]['name'];
		  
		  if(!count($result)){
		  $sql="SELECT name FROM `menucategory` where id=:id and is_live=1 and is_deleted=0";
          $sth=$conn->prepare($sql);
          $sth->bindValue("id",$menuid);
          try{$sth->execute();}catch(Exception $e){
            //echo $e->getMessage(); 
          }
          $res=$sth->fetchAll(PDO::FETCH_ASSOC);
          $sname=$res[0]['name'];
		  }
          
          $sql="SELECT subcategory.*,(select count(item.id) as count from item where item.menucategory_id=subcategory.menucategory_id and item.parent_id=subcategory.id) as count FROM `subcategory` where menucategory_id=:menucategory_id and parent_id=:parent_id and is_live=1 and is_deleted=0";
          $sth=$conn->prepare($sql);
          $sth->bindValue("menucategory_id",$menuid);
          $sth->bindValue("parent_id",$pid);
          try{$sth->execute();}catch(Exception $e){
           // echo $e->getMessage(); 
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
         // echo $e->getMessage();
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
          //echo $e->getMessage();
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
        
        $sth=$conn->prepare("update staff set mobile=:mobile,is_live=1,is_deleted=0 where venue_id=:venue_id and id=:id") ;
        $sth->bindValue("venue_id",$vid);
        $sth->bindValue("id",$staff_id);
        $sth->bindValue("mobile",$mobile);

        try{$sth->execute();}
        catch(Exception $e){
         //echo $e->getMessage();
       }
         //$success=1; 
         //$msg="Updation Sucessful";
       header("Location: $redirect?success=$success&msg=$msg"); 
       break;  
       
       case 'delete-staff':
        //print_r($_REQUEST);
       
       $vid=$_REQUEST['vid'];
       $staff_id=$_REQUEST['staff_id'];
       $sth=$conn->prepare("update staff set online=0,is_live=0,is_deleted=1 where venue_id=:venue_id and id=:id")  ;
       $sth->bindValue("venue_id",$vid);
       $sth->bindValue("id",$staff_id);

       try{$sth->execute();}
       catch(Exception $e){
         //echo $e->getMessage();
       }
       
       $sth=$conn->prepare("select * from staff where venue_id=:venue_id and id=:id and is_deleted=0");
       $sth->bindValue("venue_id",$vid);
       $sth->bindValue("id",$staff_id);

       try{$sth->execute();}
       catch(Exception $e){
       // echo $e->getMessage();
      } 
      $staff=$sth->fetchAll(PDO::FETCH_ASSOC);
      break;    
      
      
      case 'add-item':
      
      //print_r($_REQUEST);die;
      $tax=$_REQUEST['tax'];
      $taxid=$_REQUEST['tax_id'];
      $percent=$_REQUEST['percentage'];
      $image=$_FILES['image'];
      $mid=$_REQUEST['menu_id'];
      $sid=$_REQUEST['sub_id'];
      $item_desc=$_REQUEST['item_description']?$_REQUEST['item_description']:"";
      $item_descame = strip_tags($item_desc);
      //$item_desc= htmlspecialchars($item_desc);
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
        $c=count($_REQUEST['tax']);
        $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
        if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
          
          $url=$randomFileName;
        }
		
        if(!$url)
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

	  $sth=$conn->prepare("select * from gambay_charge where venue_id=:vid");
      $sth->bindValue("vid",$vid);
      try{$sth->execute();}
      catch(Exception $e){
        //echo $e->getMessage();
      }
	  $g_charge=$sth->fetchAll();
	  $g_c=$g_charge[0]['charge'];
	  $item_price=$item_price+($g_c/100);
	  
      $sth=$conn->prepare("select * from pricing_names where venue_id=:vid and name=:name and is_live=1 and is_deleted=0");
      $sth->bindValue("vid",$vid);
      $sth->bindValue("name",'Regular');
      try{$sth->execute();}
      catch(Exception $e){
        //echo $e->getMessage();
      }
      $p_res=$sth->fetchAll(PDO::FETCH_ASSOC);
      $pr_id=$p_res[0]['id'];

      if(!count($p_res)){
       $sth=$conn->prepare("insert into pricing_names values(DEFAULT,:name,:venue_id,1,0)");
       $sth->bindValue("name",'Regular');
       $sth->bindValue("venue_id",$vid);
       try{$sth->execute();
        $pr_id=$conn->lastInsertId();
      }
      catch(Exception $e){
        //echo $e->getMessage();
      }
    }

    $sth=$conn->prepare("select * from subcategory where parent_id=:pid and is_live=1 and is_deleted=0");
    $sth->bindValue("pid",$sid);
    try{$sth->execute();}
    catch(Exception $e){
      //echo $e->getMessage();
    }
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);

    if(!count($res)){
      $sth=$conn->prepare("select name from subcategory where id=:id and is_live=1 and is_deleted=0");
      $sth->bindValue("id",$sid);
      try{$sth->execute();}
      catch(Exception $e){
       // echo $e->getMessage();
      }
      $result=$sth->fetchAll(PDO::FETCH_ASSOC);
      $itemname=$result[0]['name'];
      
      $sth=$conn->prepare("select * from item where name=:name and menucategory_id=:mid and parent_id=:pid and is_live=1 and is_deleted=0");
      $sth->bindValue("mid",$mid);
	  $sth->bindValue("pid",$sid);
      $sth->bindValue("name",$itemname);
      try{$sth->execute();}
      catch(Exception $e){
	  //echo $e->getMessage();
	  }
      $item=$sth->fetchAll(PDO::FETCH_ASSOC); 
      
      $itemid=$item[0]['id'];
      
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
		$success="1";
      }
      catch(Exception $e){
        //echo $e->getMessage();
      }
    }


    if($tax){
      for($i=0;$i<=$c-1;$i++){
       $sth=$conn->prepare("select * from item_tax where item_id=:item_id and tax_id=:tax_id and is_deleted=0");
       $sth->bindValue("item_id",$itemid);
       $sth->bindValue("tax_id",$taxid[$i]);
       try{$sth->execute();}
       catch(Exception $e){
	   //echo $e->getMessage();
	   }
       $item_tax=$sth->fetchAll(PDO::FETCH_ASSOC); 
       
       if(count($item_tax)){
         $sth=$conn->prepare("update item_tax set percentage=:percentage where item_id=:item_id and tax_id=:tax_id and is_deleted=0");
         $sth->bindValue("item_id",$itemid);
         $sth->bindValue("tax_id",$taxid[$i]);
         $sth->bindValue("percentage",$percent[$i]);
         try{$sth->execute();}
         catch(Exception $e){
          //echo $e->getMessage();
        }
      }  
      else{
       $sth=$conn->prepare("insert into item_tax values(DEFAULT,:item_id,:tax_id,:percentage,0)");
       $sth->bindValue("item_id",$itemid);
       $sth->bindValue("tax_id",$taxid[$i]);
       $sth->bindValue("percentage",$percent[$i]);
       try{$sth->execute();}
       catch(Exception $e){
        //echo $e->getMessage();
      }
    }  
  }
}
$sth=$conn->prepare("select * from item_serving where item_id=:item_id and serving_id=:serving_id and is_deleted=0");
$sth->bindValue("item_id",$itemid);
$sth->bindValue("serving_id",$serveid);
try{$sth->execute();}
catch(Exception $e){
//echo $e->getMessage();
}
$item_serve=$sth->fetchAll(PDO::FETCH_ASSOC);

if(!count($item_serve)){
  
              /*$txid=implode(",",$t1);

              $sth=$conn->prepare("select percentage from item_tax where tax_id IN ($txid) and item_id=:item_id and is_deleted=0");
              $sth->bindValue("item_id",$itemid);
              try{$sth->execute();}
              catch(Exception $e){echo $e->getMessage();}
              $txp=$sth->fetchAll(PDO::FETCH_ASSOC);*/
              
              $sth=$conn->prepare("insert into item_serving values(DEFAULT,:item_id,:serving_id,0)");
              $sth->bindValue("serving_id",$serveid);
              $sth->bindValue("item_id",$itemid);
              try{$sth->execute();}
              catch(Exception $e){
                //echo $e->getMessage();
              }

              $sth=$conn->prepare("insert into pricing values(DEFAULT,:name_id,:qty,:special,:status,:item_id,:venue_id)");
              $sth->bindValue("name_id",$pr_id);
              $sth->bindValue("qty",99999999);
              $sth->bindValue("special",$special);
              $sth->bindValue("status",1);
              $sth->bindValue("item_id",$itemid);
              $sth->bindValue("venue_id",$vid);
              try{$sth->execute();
                $price_id=$conn->lastInsertId();
              }
              catch(Exception $e){
               // echo $e->getMessage();
              }
              
              $agg=0;
              foreach($percent as $row){
                $tmp=($row/100)*$item_price;
                $agg=$agg+$tmp;
              }
              $agg_price=$item_price+$agg;

              $sth=$conn->prepare("insert into serving_price values(DEFAULT,:pricing_id,:serving_id,:item_price,:agg_price,0)");
              $sth->bindValue("pricing_id",$price_id);
              $sth->bindValue("serving_id",$serveid);
              $sth->bindValue("item_price",$item_price);
              $sth->bindValue("agg_price",$agg_price);
              try{$sth->execute();
                $pricingid=$conn->lastInsertId();
              }
              catch(Exception $e){
               // echo $e->getMessage();
              }
              foreach($taxid as $tax){

                $sth=$conn->prepare("insert into pricing_tax values(DEFAULT,:s_pricing_id,:tax_id,0)");
                $sth->bindValue("s_pricing_id",$pricingid);
                $sth->bindValue("tax_id",$tax);
                try{$sth->execute();}
                catch(Exception $e){
                  //echo $e->getMessage();
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
          
          case 'add-pricing':
         //print_r($_REQUEST);die;
          $image=$_FILES['image'];
          $itemid=$_REQUEST['item_id'];
		  $item_desc=$_REQUEST['item_description'];
          $servingid=$_REQUEST['serving_id'];
          $item_price=$_REQUEST['item_price'];
          $qty=$_REQUEST['qty'];
		  $special=$_REQUEST['specials'];
		  $it_menu=$_REQUEST['status'];
          $pr_id=$_REQUEST['pr_id'];
          $p_name=$_REQUEST['p_name'];
          $vid=$_REQUEST['venue_id'];
          $redirect=$_REQUEST['redirect'];
          $c=count($_REQUEST['p_name']);
          $randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
          if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
            $success="1";
            $url=$randomFileName;
          }

          if($url){
            $sth=$conn->prepare("update item set pic=:pic where id=:item_id and is_deleted=0");
            $sth->bindValue("item_id",$itemid);
            $sth->bindValue("pic",$url);
            try{$sth->execute();}
            catch(Exception $e){
             // echo $e->getMessage();
            }
          }
		   
		    $sth=$conn->prepare("update item set item_description=:item_desc where id=:item_id and is_deleted=0");
            $sth->bindValue("item_id",$itemid);
			$sth->bindValue("item_desc",$item_desc);
            try{$sth->execute();}
            catch(Exception $e){
              //echo $e->getMessage();
            }
         
		  
          $sth=$conn->prepare("select group_concat(tax_id) as txid,group_concat(percentage) as txper from item_tax where item_id=:item_id and is_deleted=0");
          $sth->bindValue("item_id",$itemid);
          try{$sth->execute();}
          catch(Exception $e){
		  //echo $e->getMessage();
		  }
          $item_tax=$sth->fetchAll(PDO::FETCH_ASSOC);   
          $txid=$item_tax[0]['txid'];
          $txpercent=$item_tax[0]['txper'];
          
          $txid=explode(',',$txid);
          $txpercent=explode(',',$txpercent);
        
          for($i=0;$i<=$c-1;$i++){
		      $agg=0;
        foreach($txpercent as $row){
          $tmp=($row/100)*$item_price[$i];
          $agg=$agg+$tmp;
        }
        $agg_price=$item_price[$i]+$agg;
		//print_r($agg_price);die;
		
		  if($item_price[$i]){
		  if($qty[$i]=='Unlimited') $qty[$i]=99999999;
            $qty[$i]=$qty[$i]?$qty[$i]:99999999;
			
            //select,insert/update operations in pricing table
           $sth=$conn->prepare("select pricing.* from pricing where item_id=:item_id and pricing_name_id=:pr_id and venue_id=:venue_id");
          //$sth=$conn->prepare("select pricing.*,(select count(id) from serving_price where serving_price.pricing_id=pricing.id and serving_price.serving_id=1)as s_count,(select id from serving_price where serving_price.pricing_id=pricing.id and serving_price.serving_id=1) as sid,(select count(pricing_tax.id) from pricing_tax where pricing_tax.serving_price_id=sid ) as pt_count  from pricing where item_id=37 and pricing_name_id=2 
              //");
           $sth->bindValue("item_id",$itemid);
           $sth->bindValue("venue_id",$vid);
           $sth->bindValue("pr_id",$pr_id[$i]);
           try{$sth->execute();}
           catch(Exception $e){
		   //echo $e->getMessage();
		   }
           $pricing_update=$sth->fetchAll(PDO::FETCH_ASSOC);
           $pricingid=$pricing_update[0]['id'];
           if(count($pricing_update)){
            $sth=$conn->prepare("update pricing set quantity=:qty where item_id=:item_id and id=:id");
            $sth->bindValue("qty",$qty[$i]);
            $sth->bindValue("item_id",$itemid);
            $sth->bindValue("id",$pricingid);
            try{$sth->execute();}
            catch(Exception $e){
             // echo $e->getMessage();
            }
          }
          else{
            $sth=$conn->prepare("insert into pricing values(DEFAULT,:pr_id,:qty,0,0,:item_id,:venue_id)");
            $sth->bindValue("pr_id",$pr_id[$i]);
            $sth->bindValue("qty",$qty[$i]);
            $sth->bindValue("item_id",$itemid);
            $sth->bindValue("venue_id",$vid);
            try{$sth->execute();
             $pricingid=$conn->lastInsertId();
           }
           catch(Exception $e){
            //echo $e->getMessage();
          }
        }
        
          //select,insert/update operations in serving_price table
        $agg=0;
        foreach($txpercent as $row){
          $tmp=($row/100)*$item_price[$i];
          $agg=$agg+$tmp;
        }
        $agg_price=$item_price[$i]+(0.03*$item_price[$i])+$agg;
        $sth=$conn->prepare("select * from serving_price where serving_id=:serving_id and pricing_id=:pricing_id and is_deleted=0");
        
        $sth->bindValue("serving_id",$servingid);
        $sth->bindValue("pricing_id",$pricingid);
        try{$sth->execute();}
        catch(Exception $e){
		//echo $e->getMessage();
		}
        $serve_pricing_update=$sth->fetchAll(PDO::FETCH_ASSOC);
        $serve_pricingid=$serve_pricing_update[0]['id'];
        if(count($serve_pricing_update)){
          $sth=$conn->prepare("update serving_price set item_price=:item_price,agg_price=:agg_price where serving_id=:serving_id and pricing_id=:pricing_id and is_deleted=0 and id=:id");
          $sth->bindValue("serving_id",$servingid);
          $sth->bindValue("pricing_id",$pricingid);
          $sth->bindValue("item_price",$item_price[$i]);
          $sth->bindValue("agg_price",$agg_price);
          $sth->bindValue("id",$serve_pricingid);
          try{$sth->execute();}
          catch(Exception $e){
           // echo $e->getMessage();
          }
        }
        else{
          $sth=$conn->prepare("insert into serving_price values(DEFAULT,:pricing_id,:serving_id,:item_price,:agg_price,0)");
          $sth->bindValue("pricing_id",$pricingid);
          $sth->bindValue("serving_id",$servingid);
          $sth->bindValue("item_price",$item_price[$i]);
          $sth->bindValue("agg_price",$agg_price);
          try{$sth->execute();
           $serve_pricingid=$conn->lastInsertId();
         }
         catch(Exception $e){
          //echo $e->getMessage();
        }
      }
      
                //select,insert/update operations in pricing_tax table
      foreach($txid as $row){
       $sth=$conn->prepare("select * from pricing_tax where serving_price_id=:serving_price_id and tax_id=:tax_id and is_deleted=0");
       $sth->bindValue("serving_price_id",$serve_pricingid);
       $sth->bindValue("tax_id",$row);
       try{$sth->execute();}
       catch(Exception $e){
	   //echo $e->getMessage();
	   }
       $pricing_tax_update=$sth->fetchAll(PDO::FETCH_ASSOC);
       $pricing_taxid=$pricing_tax_update[0]['id'];
       if(!count($pricing_tax_update)){
        $sth=$conn->prepare("insert into pricing_tax values(DEFAULT,:serving_price_id,:tax_id,0)");
        $sth->bindValue("tax_id",$row);
        $sth->bindValue("serving_price_id",$serve_pricingid);
        try{$sth->execute();}
        catch(Exception $e){
         // echo $e->getMessage();
        }
      }
      
    }
  }
  }
  header("Location: $redirect?success=$success&msg=$msg");
  break;

  case 'delete-item':
  //print_r($_REQUEST);
  $item_id=$_REQUEST['item'];
  $redirect='menu_dashboard.php';
   $sth=$conn->prepare("update item set is_live=0,is_deleted=1 where id=:id");
          $sth->bindValue("id",$item_id);
          try{$sth->execute();
		  $success=1;
		  $msg="Item Deleted";
		  }
          catch(Exception $e){
            //echo $e->getMessage();
          }
		    header("Location: $redirect?success=$success&msg=$msg");
  break;
  
    case 'delete-item-serving':
  //print_r($_REQUEST);die;
  $item_id=$_REQUEST['item'];
  $serving_id=$_REQUEST['serving'];
  $redirect='menu_dashboard.php';
  
  $sth=$conn->prepare("select * from item_serving where item_id=:item_id ");
   $sth->bindValue("item_id",$item_id);
          try{$sth->execute();}
          catch(Exception $e){}
		  $it_serving=$sth->fetchAll();
		  
		 if(count($it_serving)==1){
		  $sth=$conn->prepare("delete from item where id=:item_id");
          $sth->bindValue("item_id",$item_id);
          try{$sth->execute();}
          catch(Exception $e){}
          }
  
   $sth=$conn->prepare("delete from item_serving where item_id=:item_id and serving_id=:serving_id");
          $sth->bindValue("item_id",$item_id);
		   $sth->bindValue("serving_id",$serving_id);
          try{$sth->execute();}
          catch(Exception $e){
           // echo $e->getMessage();
          }
		  
		 $sth=$conn->prepare("select pricing.id as pr_id,serving_price.id as sp_id from pricing join serving_price on serving_price.pricing_id=pricing.id and serving_price.serving_id=:serving_id where item_id=:item_id");
			$sth->bindValue("item_id",$item_id);
			$sth->bindValue("serving_id",$serving_id);
			try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
			$item_pricing=$sth->fetchAll(PDO::FETCH_ASSOC);
			$pr_id=$item_pricing[0]['pr_id'];
			$sp_id=$item_pricing[0]['sp_id'];
			
			$sth=$conn->prepare("delete from serving_price where id=:id and serving_id=:serving_id");
			$sth->bindValue("serving_id",$serving_id);
			$sth->bindValue("id",$sp_id);
			try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}		
			$sth=$conn->prepare("delete from pricing where id=:id and item_id=:item_id");
			$sth->bindValue("item_id",$item_id);
			$sth->bindValue("id",$pr_id);
			try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
			
			$sth=$conn->prepare("delete from pricing_tax where serving_price_id=:sp_id");
			$sth->bindValue("sp_id",$sp_id);
			try{$sth->execute();}
			catch(Exception $e){
			//echo $e->getMessage();
			}
		  
		  header("Location: $redirect?success=$success&msg=$msg");
  break;
  
  case 'add-coupon':
  $vid=$_REQUEST['venue_id'];
  $coupon_name=$_REQUEST['coupon_name'];

  $coupon_name = strip_tags($coupon_name);
  //$coupon_name = htmlspecialchars($coupon_name);
  $value=$_REQUEST['value'];
  $percentage=$_REQUEST['percentage'];
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
  $sth=$conn->prepare("insert into coupons values(DEFAULT,:coupon_code,:coupon_name,:value,:percentage,:expiry_date,:venue_id,:status,:limit,:pic,1,0,NOW())");
  $sth->bindValue("coupon_code",$coupon_code);
  $sth->bindValue("coupon_name",$coupon_name);
  $sth->bindValue("value",$value);
  $sth->bindValue("percentage",$percentage);
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
  $percentage=$_REQUEST['percentage'];
  $st=$_REQUEST['status'];
  if($st) $status=1;
  else $status=0;
  $limit=$_REQUEST['limit']?$_REQUEST['limit']:'99999999';
  if($limit=='Unlimited') $limit='99999999' ;
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
   $sth=$conn->prepare("update coupons set coupon_name=:coupon_name,value=:value,percentage=:percentage,expiry_date=:expiry_date,status=:status,`limit`=:limit,pic=:pic,is_live=1,is_deleted=0,created_on=NOW() where id=:id");
 else
   $sth=$conn->prepare("update coupons set coupon_name=:coupon_name,value=:value,percentage=:percentage,expiry_date=:expiry_date,status=:status,`limit`=:limit,is_live=1,is_deleted=0,created_on=NOW() where id=:id");
 $sth->bindValue("id",$coupon_id);
 $sth->bindValue("coupon_name",$coupon_name);
 $sth->bindValue("value",$value);
  $sth->bindValue("percentage",$percentage);
 $sth->bindValue("limit",$limit);
 $sth->bindValue("expiry_date",$expiry);
 $sth->bindValue("status",$status);
 if($url) $sth->bindValue("pic",$url);

 try{$sth->execute();}
 catch(Exception $e){
   //echo $e->getMessage();
 } 
 header("Location: $redirect?success=$success&msg=$msg");      
 break;

 case 'get-coupon':
          //print_r($_REQUEST);
 $vid=$_REQUEST['vid'];
 $coupon_id=$_REQUEST['coupon_id'];

 $sth=$conn->prepare("select * from coupons where venue_id=:venue_id and id=:id");
 $sth->bindValue("venue_id",$vid);
 $sth->bindValue("id",$coupon_id);

 try{$sth->execute();}
 catch(Exception $e){
 // echo $e->getMessage();
} 
$coupons=$sth->fetchAll(PDO::FETCH_ASSOC);
          //print_r($coupons);
echo json_encode($coupons);
break;

 case 'get-pricing':
    // print_r($_REQUEST);
 $sid=$_REQUEST['serving_id'];
 $item_id=$_REQUEST['item_id'];
 $serving=$_REQUEST['serving'];
 $vid=$_REQUEST['vid'];

    $sql="SELECT pricing_names.* FROM  `pricing_names` where venue_id=:venue_id and is_live=1 and is_deleted=0 and id NOT IN (SELECT pricing_names.id as pr_id from item join pricing on pricing.item_id=item.id left join pricing_names on pricing_names.venue_id=pricing.venue_id and pricing_names.id=pricing.pricing_name_id join serving_price on serving_price.serving_id=:serving_id and serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id  where item.id=:item_id and item.is_live=1 and item.is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("item_id",$item_id);
    $sth->bindValue("serving_id",$sid);
    try{$sth->execute();}
	catch(Exception $e){
	// echo $e->getMessage();
	}
    $names=$sth->fetchAll();
 
   $sql="SELECT item.id as item_id,item.name as item_name, item.item_description as item_desc,servings.type as serving, item.pic as item_pic ,pricing.quantity as qty, pricing_names.id as pr_id,pricing_names.name as pr_name, serving_price.serving_id as serve_id, serving_price.item_price,serving_price.agg_price from item join pricing on pricing.item_id=item.id left join pricing_names on pricing_names.venue_id=pricing.venue_id and pricing_names.id=pricing.pricing_name_id join serving_price on serving_price.serving_id=:serving_id and serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id  where item.id=:item_id and item.is_live=1 and item.is_deleted=0";

  $sth=$conn->prepare($sql);
  $sth->bindValue("item_id",$item_id);
  $sth->bindValue("serving_id",$sid);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }
  $pricing=$sth->fetchAll();
 
  $sql="SELECT pricing.special_flag,pricing.status from pricing join serving_price on serving_price.serving_id=:serving_id and serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 where pricing.item_id=:item_id ";

  $sth=$conn->prepare($sql);
  $sth->bindValue("item_id",$item_id);
  $sth->bindValue("serving_id",$sid);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }
  $tags=$sth->fetchAll();
  
  $sql="SELECT pricing_tax.tax_id,item_tax.percentage,tax.tax_name from pricing_tax join tax on tax.id=pricing_tax.tax_id join item_tax on item_tax.tax_id=tax.id and item_tax.item_id=:item_id and item_tax.tax_id=pricing_tax.tax_id join serving_price on serving_price.serving_id=:serving_id and serving_price.id=pricing_tax.serving_price_id where tax.venue_id=:vid group by pricing_tax.tax_id";

  $sth=$conn->prepare($sql);
  $sth->bindValue("item_id",$item_id);
  $sth->bindValue("serving_id",$sid);
   $sth->bindValue("vid",$vid);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }
  $tax=$sth->fetchAll();

$data=array('names'=>$names,'pricing'=>$pricing,'tags'=>$tags,'tax'=>$tax);
echo json_encode($data);
break;

case 'delete-coupon':
        //print_r($_REQUEST);

$vid=$_REQUEST['vid'];
$coupon_id=$_REQUEST['coupon_id'];
$sth=$conn->prepare("update coupons set status=0,is_live=0,is_deleted=1 where venue_id=:venue_id and id=:id") ;
$sth->bindValue("venue_id",$vid);
$sth->bindValue("id",$coupon_id);

try{$sth->execute();}
catch(Exception $e){
 //echo $e->getMessage();
}

$sth=$conn->prepare("select * from coupons where venue_id=:venue_id and id=:id and is_deleted=0");
$sth->bindValue("venue_id",$vid);
$sth->bindValue("id",$coupon_id);

try{$sth->execute();}
catch(Exception $e){
  //echo $e->getMessage();
} 
$coupons=$sth->fetchAll(PDO::FETCH_ASSOC);
          //print_r($coupons);
         //echo json_encode($coupons);
break;    

 case 'set-pricing':
     print_r($_REQUEST);
 $vid=$_REQUEST['venue_id'];
 $naming_id=$_REQUEST['naming_id'];

 $sth=$conn->prepare("select * from pricing where venue_id=:venue_id and pricing_name_id=:naming_id");
 $sth->bindValue("venue_id",$vid);
  $sth->bindValue("naming_id",$naming_id);
 try{$sth->execute();}
 catch(Exception $e){
  //echo $e->getMessage();
} 
$naming=$sth->fetchAll(PDO::FETCH_ASSOC);

foreach($naming as $name){
  $sth=$conn->prepare("update pricing set status=0 where venue_id=:venue_id and item_id=:item_id");
            $sth->bindValue("venue_id",$vid);
			$sth->bindValue("item_id",$name['item_id']);
            try{$sth->execute();}
            catch(Exception $e){
              //echo $e->getMessage();
            }
}

  $sth=$conn->prepare("update pricing set status=1 where venue_id=:venue_id and pricing_name_id=:naming_id");
            $sth->bindValue("venue_id",$vid);
			$sth->bindValue("naming_id",$naming_id);
            try{$sth->execute();}
            catch(Exception $e){
             // echo $e->getMessage();
            }

break;

case 'remove-category':

$categoryid=$_REQUEST['menucategory_id'];
$sth=$conn->prepare("select * from subcategory where menucategory_id=:menucategory_id and is_live=1 and is_deleted=0");
$sth->bindValue("menucategory_id",$categoryid);

try{$sth->execute();}
catch(Exception $e){
  //echo $e->getMessage();
}
$result=$sth->fetchAll();

          $sth=$conn->prepare("select * from item where menucategory_id=:menucategory_id and parent_id=0 and is_live=1 and is_deleted=0");
          $sth->bindValue("menucategory_id",$categoryid);

          try{$sth->execute();}
          catch(Exception $e){
            //echo $e->getMessage();
          }
          $item=$sth->fetchAll();

          if(!count($result) && !count($item)){
            $sth=$conn->prepare("update menucategory set is_deleted=1,is_live=0 where id=:id");
            $sth->bindValue("id",$categoryid);

            try{$sth->execute();}
            catch(Exception $e){
              //echo $e->getMessage();
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
           // echo $e->getMessage();
          }
          $result=$sth->fetchAll(PDO::FETCH_ASSOC);
          
           $sth=$conn->prepare("select * from item where menucategory_id=:menucategory_id and parent_id=:parent_id and is_live=1 and is_deleted=0");
          $sth->bindValue("menucategory_id",$categoryid);
          $sth->bindValue("parent_id",$subcategoryid);

          try{$sth->execute();}
          catch(Exception $e){
           // echo $e->getMessage();
          }
          $item=$sth->fetchAll();

          if(!count($result) && !count($item)){
            $sth=$conn->prepare("update subcategory set is_deleted=1,is_live=0 where id=:id");
            $sth->bindValue("id",$subcategoryid);

            try{$sth->execute();}
            catch(Exception $e){
              //echo $e->getMessage();
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
                //echo $e->getMessage();
              }
              
              $sth=$conn->prepare("insert into item_serving values(DEFAULT,:item_id,:serving_id,0)");
              $sth->bindValue("serving_id",$serveid);
              $sth->bindValue("item_id",$itemid);
              try{$sth->execute();}
              catch(Exception $e){
                //echo $e->getMessage();
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
                    //echo $e->getMessage();
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
                    //echo $e->getMessage();
                  }
                  foreach($t1 as $tax){

                    $sth=$conn->prepare("insert into pricing_tax values(DEFAULT,:s_pricing_id,:tax_id,0)");
                    $sth->bindValue("s_pricing_id",$pricingid);
                    $sth->bindValue("tax_id",$tax);
                    try{$sth->execute();}
                    catch(Exception $e){
                     // echo $e->getMessage();
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
                //echo $e->getMessage();
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