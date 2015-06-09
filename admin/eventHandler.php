<?php 
//this page is to handle all the admin events occured at client side
 require_once("../php_include/db_connection.php"); 
require_once('../PHPMailer_5.2.4/class.phpmailer.php');
require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php');
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}

	function generateRandomString($length = 10){
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
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
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';  // optional - MsgHTML will create an alternate automaticall//y
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
	session_start();
	//switch case to handle different events
	switch($_REQUEST['event']){
	case "signin":     
	
		$success=0;
		$user=$_REQUEST['username'];
		$password=$_REQUEST['password'];
		$redirect=$_REQUEST['redirect'];
		$sth=$conn->prepare("select * from admin where (name=:name or email=:email)");
		$sth->bindValue("name",$user);
		$sth->bindValue("email",$user);
		try{$sth->execute();}catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)){
			foreach($result as $row){
		
				if($row['password']==md5($password)){
					session_start();
					$success=1;
					
					$_SESSION['admin']['id']=$row['id'];
					$_SESSION['admin']['username']=$row['name'];
					$_SESSION['admin']['email']=$row['email'];
					
				}
			}
		}
		if(!$success){
			$redirect="index.php";
			$msg="Invalid Username/Password";
		}
		header("Location: $redirect?success=$success&msg=$msg");
		break;
	
	case "manager-login":  
		$success=0;
		$user=$_REQUEST['username'];
		$password=$_REQUEST['password'];
		$redirect=$_REQUEST['redirect'];
		$base=BASE_PATH;
		$add='venue/';
		$sth=$conn->prepare("select * from manager where (username=:username or email=:email) and password=:password");
		$sth->bindValue("username",$user);
		$sth->bindValue("email",$user);
		$sth->bindValue("password",$password);
		try{$sth->execute();}catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)){
			foreach($result as $row){
		
				if($row['password']==md5($password)){
					$success=1;
					
					$_SESSION['manager']['id']=$row['id'];
					$_SESSION['manager']['username']=$row['username'];
					$_SESSION['manager']['email']=$row['email'];
					
				}
				//print_r($_SESSION);die;
			}
		}
		else{
		$add="admin/";
			$redirect="manager_login.php";
			$msg="Invalid Username/Password";
		}
		header("Location: $base$add$redirect?success=$success&msg=$msg");
		break;
	
	case "add_stripe_account":
  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );
  \Stripe\Stripe::setApiKey($stripe['secret_key']);

  $country = $_POST['country'];
  $email=$_POST['email'];
  $vid=$_POST['venue_id'];
  $redirect=BASE_PATH.'admin/venues.php';
  try{
$acc =\Stripe\Account::create(
  array(
    "country" => $country,
    "managed" => 'true',
	"email" => $email
  )
); 
}
catch(Exception $e){
//echo $e->getMessage();
}
//echo $acc;die;
if($acc){
$success='1';
$msg="Stripe Account Created and Email sent for linking";
//$acc=str_replace("Stripe\Account JSON: "," ",$acc);

$sth=$conn->prepare('Insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$acc);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }
  $path=BASE_PATH.'venue/view_venue.php';
  $msg1="Link your bank account with your stripe account";
  	  $msg2="You are registered to the stripe system <br>
						<div style='font-size:16px;line-height:1.4;'>
							<p>Dear Venue Owner,</p>
							<p> Link your bank account with the existing stripe account </p>
							<p>Your account can be linked using this <a href='$path'> Gambay URL </a> </p>
							<p>Best,</p>
							<p>Gambay Users</p>
						</div>  ";
						
  $sql="select * from stripe_connect where venue_id=:vid";
  $sth=$conn->prepare($sql);
  $sth->bindValue('vid',$vid);
  try{$sth->execute();}
  catch(Exception $e){}
  $rew=$sth->fetchAll();
  if(count($rew)){
  $sql="update stripe_connect set stripe_id=:stripe_id,is_linked=0,acc_json=:acc_json where venue_id=:vid";
  $sth=$conn->prepare($sql);
    $sth->bindValue('stripe_id',$acc->id);
  $sth->bindValue('vid',$vid);
   $sth->bindValue('acc_json',$acc);
    try{$sth->execute();
  sendEmail($email,$msg1,$msg2,SMTP_EMAIL);
  }
  catch(Exception $e){
   //echo $e->getMessage();
  }
  }
else{						
 $sth=$conn->prepare('Insert into stripe_connect values(DEFAULT,:venue_id,:stripe_id,0,:acc_json)');
  $sth->bindValue('stripe_id',$acc->id);
  $sth->bindValue('venue_id',$vid);
   $sth->bindValue('acc_json',$acc);
  try{$sth->execute();
  sendEmail($email,$msg1,$msg2,SMTP_EMAIL);
  }
  catch(Exception $e){
   //echo $e->getMessage();
  } 
}
}
 header("Location: $redirect?success=$success&msg=$msg");
	break;
	
	case 'delete_stripe_account':
	$venue_id=$_REQUEST['vid'];
	$redirect='venues.php';
	 $path=BASE_PATH.'venue/view_venue.php';
	$sth=$conn->prepare('delete from stripe_connect where venue_id=:vid');
	$sth->bindValue('vid',$venue_id);
	try{$sth->execute();}
	catch(Exception $e){}
	
	$sql="select email from manager join manager_venue on manager_venue.manager_id=manager.id where venue_id=:vid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('vid',$venue_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	$email=$res[0]['email'];
	$msg1="Your Stripe Account Updated, link your bank account with your new stripe account ";
    $msg2="You are registered to the stripe system <br>
				<div style='font-size:16px;line-height:1.4;'>
					<p>Dear Venue Owner,</p>
					<p> Link your bank account with your new stripe account </p>
					<p>Your account can be linked using this <a href='$path'> Gambay URL </a> </p>
					<p>Best,</p>
					<p>Gambay Users</p>
				</div>  ";
	 sendEmail($email,$msg1,$msg2,SMTP_EMAIL);
	
	 header("Location: $redirect?success=$success&msg=$msg");
	break;
	
	case "set_charge":
	$vid=$_REQUEST['venue_id'];
	$charge=$_REQUEST['charge'];
	$redirect="venues.php";
	$sql="select * from gambay_charge where venue_id=:vid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('vid',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	
	if(!count($res)){
	$sql="insert into gambay_charge values(DEFAULT,:charge,:vid)";
	$sth=$conn->prepare($sql);
	$sth->bindValue('charge',$charge);
	$sth->bindValue('vid',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	else{
	$sql="update gambay_charge set charge=:charge where venue_id=:vid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('charge',$charge);
	$sth->bindValue('vid',$vid);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	
	 header("Location: $redirect?success=$success&msg=$msg");
	break;
	
	case "delete-customer":
	$uid=$_REQUEST['user_id'];
	$redirect="customers.php";
	$sql="select * from users where id=:uid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	
	if(count($res)){
	$sql="delete from users where id=:uid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	try{$sth->execute();
	$success='1';
	$msg="User Deleted";
	}
	catch(Exception $e){}
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
            catch(Exception $e){echo $e->getMessage();}
          }

          header("Location: $redirect?venue_id=$vid&success=$success&msg=$msg");
          break;
	
		case "manager-signin":  
		$success=0;
		$user=$_REQUEST['username'];
		$password=$_REQUEST['password'];
		$name=$_REQUEST['name'];
		$redirect=$_REQUEST['redirect'];
		$base=BASE_PATH;
		$add='venue/';
		$sth=$conn->prepare("select * from manager where (username=:username or email=:email)");
		$sth->bindValue("username",$user);
		$sth->bindValue("email",$user);
		try{$sth->execute();}catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result)){
			foreach($result as $row){
		
				if($row['password']==md5($password)){
					$success=1;
					
					$_SESSION['manager']['id']=$row['id'];
					$_SESSION['manager']['username']=$row['username'];
					$_SESSION['manager']['email']=$row['email'];
					
				}
				//print_r($_SESSION);die;
			}
		}
		if(!$success){
			$redirect="index.php";
			$msg="Invalid Username/Password";
		}
		header("Location: $base$add$redirect?success=$success&msg=$msg");
		break;
	
	case 'add-vtype':
    //print_r($_REQUEST);
    $vtype=$_REQUEST['name'];
       $vtype= strip_tags($vtype);
    $vtype= htmlspecialchars($vtype);
    if($vtype){
      $sth=$conn->prepare("insert into venuetype values(DEFAULT,:type)");
      $sth->bindValue("type",$vtype);
      try{$sth->execute();
       $vtpe_id=$conn->lastInsertId();
      }
      catch(Exception $e){
        echo $e->getMessage();
      }
      echo $vtype_id;
    }

    break;
  
    case "get-venues":
  //print_r($_REQUEST);
     $vtype_id=$_REQUEST['vtype_id'];
    $sql="SELECT * FROM `venue` where venuetype_id=:vtypeid";
    $sth=$conn->prepare($sql);
    $sth->bindValue("vtypeid",$vtype_id);
    try{$sth->execute();}
    catch(Exception $e){ echo $e->getMessage();}
    $venues=$sth->fetchAll();
    //print_r($venues);
     if(count($venues))
   echo json_encode($venues);
   else
  echo '0';
  break;
  
  case "remove-cusine":
  //print_r($_REQUEST);
     $vtype_id=$_REQUEST['vtype_id'];
    $sql="SELECT * FROM `venue` where venuetype_id=:vtypeid";
    $sth=$conn->prepare($sql);
    $sth->bindValue("vtypeid",$vtype_id);
    try{$sth->execute();}
    catch(Exception $e){ echo $e->getMessage();}
    $venues=$sth->fetchAll();
    //print_r($venues);
     if(!count($venues)){
       $sql="DELETE FROM `venuetype` where id=:vtypeid";
    $sth=$conn->prepare($sql);
    $sth->bindValue("vtypeid",$vtype_id);
    try{$sth->execute();}
    catch(Exception $e){ 
   //echo $e->getMessage();
    }
    echo '3';
     }
   else
  echo 'x';
  
  break;
	
	case "signout":
	session_start();
		unset($_SESSION);
		session_destroy();
		header("Location: index.php?success=1&msg=Signout Successful!");
		break;
		
	case "reset-password":
		$token=$_REQUEST["token"];
		$password=$_REQUEST["password"];
		$confirm=$_REQUEST["confirm"];
		//$base="http://www.code-brew.com/projects/gambay/thank_you.php";
		$base=BASE_PATH."/reset-password.php";
		
		$sql="SELECT email from users where token=:token";
		$stmt=$conn->prepare($sql);
		$stmt->bindValue("token",$token);
		try{$stmt->execute();}
		catch(Exception $e){ echo $e->getMessage();}
		$a=$stmt->fetchAll();
		$email=$a[0]['email'];
		
		if($password==$confirm){
			
				$sth=$conn->prepare("update users set password=:password where token=:token");
				$sth->bindValue("token",$token);
				$sth->bindValue("password",md5($password));
				$count=0;
				try{$count=$sth->execute();}catch(Exception $e){echo $e;}
				if($count){
					$success=1;
					$msg="Password changed successfully";
					$base="http://www.gambay.me";
					
					// email_sending
					$subject_msg="Gambay Account Password Changed!";
					$body_msg="The password for your Gambay account changed successfully! <br> To get back into your account, you'll need to reset your password.";
					sendEmail($email, $subject_msg, $body_msg, SMTP_EMAIL);
				}
			}else{
				$success=0;
				$msg="Passwords didn't match";
			}
	header("Location: $base?success=$success&msg=$msg");
	break;
	
	case "manager-signout":
	$base=BASE_PATH;
	$add="venue/index.php";
	session_start();
		unset($_SESSION);
		session_destroy();
		header("Location: $base$add?success=1&msg=Signout Successful!");
		break;
		
	case "manager-signup":

		$base=BASE_PATH;
		$redirect=$_REQUEST['redirect'];
		$key=$_REQUEST['key'];
		$username=$_REQUEST['username'];
		$email=$_REQUEST['email'];
		$name=$_REQUEST['name']?$_REQUEST['name']:"";
		$password=$_REQUEST['password'];
		$confirm=$_REQUEST['confirm'];
		$mobile=$_REQUEST['mobile']?$_REQUEST['mobile']:'';
		$image=$_FILES['pic'];
		
		if(!($username && $email && $password )){
	$success="0";
	$msg="Incomplete Parameters";
	$redirect="venue/manager_signup.php?key=".$key;
	header("Location: $base$redirect&success=$success&msg=$msg");
}


	elseif($image["error"]>0){
		$success="0";
		$msg="Invalid image";
		if($image["error"]==4) $success="1"; //image is not mandatory
	}
	//upload image
	elseif(in_array($image['type'],array("image/gif","image/jpeg","image/jpg","image/png","image/pjpeg","image/x-png")) && in_array(end(explode(".",$image['name'])), array("gif","jpeg","jpg","png"))){
		$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
		if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
			$success="1";
			$image_path=$randomFileName;
		}
		else{
			$success="0";
			$msg="Error in uploading image";
			$redirect="venue/manager_signup.php?key=".$key;
			header("Location: $base$redirect&success=$success&msg=$msg");
		}
	}
	else{
		$success="1";
		$msg="Invalid Image";
	}
		
		if($success=="1"){ 
		if($confirm==$password){
		
		$code=md5($username . rand(1,9999999));
		//echo $username.$password.$mobile.$image_path;
		$sth=$conn->prepare("update manager set password=:password, mobile_number=:mobile, token=:token,name=:name, pic=:pic where username=:username");
			$sth->bindValue("username",$username);
			$sth->bindValue("password",md5($password));
			$sth->bindValue("mobile",$mobile);
			$sth->bindValue("name",$name);
			$sth->bindValue("pic",$image_path);
			$sth->bindValue("token",$code);
			$count=0;
			try{$count=$sth->execute();}catch(Exception $e){
			//echo $e->getMessage();
			}
			
		
			if($count)
			$msg="Details updated";
			$add="venue/";
			header("Location: $base$add$redirect");
		
		}
		else{
		//delete image
	if($image_path){ if(@unlink("../uploads/$image_path")){}}
		$msg="Password Doesnot match";
		$redirect="venue/manager_signup.php?key=".$key;
		header("Location: $base$redirect&success=$success&msg=$msg");
		}
		}
		
		break;
		
	case "create-user":
	
		$redirect=$_REQUEST['redirect'];
		$username=$_REQUEST['username'];
		$email=$_REQUEST['email'];
		$name=$_REQUEST['name']?$_REQUEST['name']:"";
			
		$sth=$conn->prepare("select * from manager where username=:username or email=:email");
	$sth->bindValue("username",$username);
	$sth->bindValue("email",$email);
	
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	if(count($result)){
		$success="0";
		if($username==$result[0]['username'])
			$msg="Username is already taken";
		else
			$msg="Email is already registered";
			
			
			$redirect="create_user.php";
		
		}
		else{
		$success="0";
		$code=md5($username . rand(1,9999999));
		$sql="insert into manager values(DEFAULT,:username,:email,:name,'','',:token,'',0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("username",$username);
		$sth->bindValue("email",$email);
		$sth->bindValue("name",$name);
		$sth->bindValue("token",$code);
		
		$count1=0;
		try{$count1=$sth->execute();}catch(Exception $e){
		echo $e->getMessage();
		}
		
		if($count1){
		$success='1';
		$msg="Invitation link sent to User";
		
		
		//mail
			$smtp_username = SMTP_USER;
			$smtp_email = SMTP_EMAIL;
			$smtp_password = SMTP_PASSWORD;
			$smtp_name = SMTP_NAME;
			$subjectMail = 'Welcome to Gambay - Verify your email';
			$mail = new PHPMailer(true); 
			$mail->IsSMTP(); // telling the class to use SMTP
			try {
			  $mail->Host       = SMTP_HOST; // SMTP server
			  $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
			  $mail->SMTPAuth   = true;                  // enable SMTP authentication
			  $mail->Host       = SMTP_HOST; // sets the SMTP server
			  $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
			  $mail->Username   = $smtp_username; // SMTP account username
			  $mail->Password   = $smtp_password;        // SMTP account password
			  $mail->AddAddress($email);     // SMTP account password
			  $mail->SetFrom($smtp_email, $smtp_name);
			  $mail->AddReplyTo($smtp_email, $smtp_name);
			  $mail->Subject = $subjectMail;
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			  $mail->MsgHTML('Welcome to Gambay!  Please verify your email address<br>'.BASE_PATH.'venue/manager_signup.php?key='.$code) ;
			  if(!$mail->Send()){
				//echo json_encode(array('success'=>'0','msg'=>'Error while sending Mail'));
			  }else{
				// echo json_encode(array('success'=>'1','msg'=>'Signup Complete Verify Email'));
			  }
			} catch (phpmailerException $e) {
			  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
			 // echo $e->getMessage(); //Boring error messages from anything else!
			}
		}	
		}		
		header("Location: $redirect?success=$success&msg=$msg");
		break;
		
		
	case "add-staff":
	
		$redirect=$_REQUEST['redirect'];
		$base=BASE_PATH;
		$add="venue/";
		$user=$_REQUEST['username'];
		$email=$_REQUEST['email'];
		$password=generateRandomString();
		$vid=$_REQUEST['venue_id'];
		$mobile=$_REQUEST['mobile'] ? $_REQUEST['mobile'] : '';
		$username='bgmb_'.$user;
			
		$sth=$conn->prepare("select * from staff where username=:username ");
		$sth->bindValue("username",$username);
		//$sth->bindValue("email",$email);
	
		try{$sth->execute();}catch(Exception $e){}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		if(count($result)){
		$success="0";
		if($username==$result[0]['username'])
			$msg="Username is already taken";
		}
		else{
		$success="0";
		$code=md5($username . rand(1,9999999));
		$sql="insert into staff values(DEFAULT,'','',:venue_id,:username,'',:email,:password,0,:token,:mobile,1,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("venue_id",$vid);
		$sth->bindValue("username",$username);
		$sth->bindValue("email",$email);
		$sth->bindValue("password",md5($password));
		$sth->bindValue("token",$code);
		$sth->bindValue("mobile",$mobile);
		
		$count1=0;
		try{$count1=$sth->execute();}
		catch(Exception $e){
		echo $e->getMessage();
		}
		
		if($count1){
		$msg="Staff member successfully registered. You can add more";
		$success='1';
		
		
		//mail
			$smtp_username = SMTP_USER;
			$smtp_email = SMTP_EMAIL;
			$smtp_password = SMTP_PASSWORD;
			$smtp_name = SMTP_NAME;
			$subjectMail = 'Signin your account';
			$mail = new PHPMailer(true); 
			$mail->IsSMTP(); // telling the class to use SMTP
			try {
			  $mail->Host       = SMTP_HOST; // SMTP server
			  $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
			  $mail->SMTPAuth   = true;                  // enable SMTP authentication
			  $mail->Host       = SMTP_HOST; // sets the SMTP server
			  $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
			  $mail->Username   = $smtp_username; // SMTP account username
			  $mail->Password   = $smtp_password;        // SMTP account password
			  $mail->AddAddress($email);     // SMTP account password
			  $mail->SetFrom($smtp_email, $smtp_name);
			  $mail->AddReplyTo($smtp_email, $smtp_name);
			  $mail->Subject = $subjectMail;
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automaticall//y
			  $mail->MsgHTML('You are registered to the gambay system with following credentials<br>
			  Email=>'.$email.
			  '<br>Username=>'.$username.
			  '<br>Password=>'.$password) ;
			  if(!$mail->Send()){
				//echo json_encode(array('success'=>'0','msg'=>'Error while sending Mail'));
			  }else{
				// echo json_encode(array('success'=>'1','msg'=>'Signup Complete Verify Email'));
			  }
			} catch (phpmailerException $e) {
			  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
			 // echo $e->getMessage(); //Boring error messages from anything else!
			}
		}	
		}
	
		header("Location: $base$add$redirect?success=$success&msg=$msg");
		break;
		
	case "change-password":
	
		$success=$msg=null;
		$redirect=$_REQUEST['redirect'];
		$oldpass=$_REQUEST['oldpass'];
		$newpass=$_REQUEST['newpass'];
		
		$sth=$conn->prepare("select * from admin where password=:password");
		$sth->bindValue("password",md5($oldpass));
		
		try{$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result) && $newpass && ($newpass==$_REQUEST['confirm'])){
			$newpass=md5($newpass);
			$sth=$conn->prepare("update admin set password=:password where name=:username");
			$sth->bindValue("username",'admin');
			$sth->bindValue("password",$newpass);
			$count=0;
			try{$count=$sth->execute();}catch(Exception $e){
			echo $e->getMessage();
			}
			if($count){
				$success=1;
				$msg="Password Updated!";
			}
			else{
				$success=0;
				$msg="Invalid Request! Try Again Later!";
				$redirect="changePassword.php";
			}
		}
		else{
			$success=0;
			
			/*if($newpass) $msg="All Fields are required!"; else */
			$msg="Passwords didn't match!";
			$redirect="changePassword.php";
		}
		
		
		header("Location: $redirect?success=$success&msg=$msg");
		break;
	
	
		case "manager-change-password":
	
		$success=$msg=null;
		$base=BASE_PATH;
		$add="venue/";
		$username=$_REQUEST['username'];
		$redirect=$_REQUEST['redirect'];
		$oldpass=$_REQUEST['oldpass'];
		$newpass=$_REQUEST['newpass'];
		
		$sth=$conn->prepare("select * from manager where username=:username and password=:password");
		$sth->bindValue("password",md5($oldpass));
		$sth->bindValue("username",$username);
		try{$sth->execute();}
		catch(Exception $e){
		//echo $e->getMessage();
		}
		$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($result) && $newpass && ($newpass==$_REQUEST['confirm'])){
			$newpass=md5($newpass);
			$sth=$conn->prepare("update manager set password=:password where username=:username");
			$sth->bindValue("username",$username);
			$sth->bindValue("password",$newpass);
			$count=0;
			try{$count=$sth->execute();}catch(Exception $e){
			echo $e->getMessage();
			}
			if($count){
				$success=1;
				$msg="Password Updated!";
			}
			else{
				$success=0;
				$msg="Invalid Request! Try Again Later!";
				$redirect="changePassword.php";
			}
		}
		else{
			$success=0;
			
			/*if($newpass) $msg="All Fields are required!"; else*/
			 $msg="Passwords didn't match!";
			$redirect="changePassword.php";
		}
		
		
		header("Location: $base$add$redirect?success=$success&msg=$msg");
		break;
	
	
}	
?>
