<?php
require_once("db_connection.php");
require_once("../api/fpdf.php");

class GeneralFunctions{
	
	public static function sendEmail($email,$subjectMail,$bodyMail,$email_back){

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

	public static function createPDFForOrderPlaced($order_id){
		global $conn;
		
		$sql="SELECT `order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.status,staff_order.*,`order`.created_on as oc,DATE_FORMAT(`order`.created_on,'%d-%m-%Y') as open_time, `order`.id as oid,venue.id as vid,venue.*,venue.mobile as v_mobile,(select pictures.url from pictures where pictures.venue_id=venue.id) as url, users.*,
			CASE 
                  WHEN DATEDIFF(NOW(),`order`.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),`order`.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
            END as time_elapsed
		FROM `order` join venue on venue.id=order.venue_id join users on users.id=`order`.user_id left join staff_order on staff_order.order_id=`order`.id where `order`.id=:order_id";
	    
		$sth=$conn->prepare($sql);
	    $sth->bindValue('order_id',$order_id);
	    try{$sth->execute();}catch(Exception $e){}
	    $res=$sth->fetchAll();
		
		$obj= json_decode($res[0]['bill_file'],true);
  
		$venue_name=$res[0]['venue_name'];
		$v_mobile=$res[0]['v_mobile'];
		$contact_email=$res[0]['contact_email'];
		$username=$res[0]['username'];
		$dob=$res[0]['dob'];
		$mobile=$res[0]['mobile'];
		$email=$res[0]['email'];
		$address=$res[0]['address'];
		$city=$res[0]['ciy'];
		$state=$res[0]['state'];
		$website=$res[0]['website'];
		$zipcode=$res[0]['zipcode'];
		
		$venue_id=$res[0]['vid'];
		
		$sql1="SELECT M.id, M.username FROM  `manager_venue` MV JOIN venue V ON V.id = MV.venue_id JOIN manager M ON M.id = MV.manager_id
				WHERE V.id =:venue_id";
		$sth1=$conn->prepare($sql1);
	    $sth1->bindValue('venue_id',$venue_id);
	    try{$sth1->execute();}catch(Exception $e){}
	    $res1=$sth1->fetchAll();
		$manager_name=$res1[0]['username'];
		
		$pdf= new FPDF();
		$pdf->SetAutoPageBreak(true, 0);
		$pdf->AddPage(1);
		$pdf->SetFontSize(10);
		//$pdf->Image('../uploads/4575Logo.png ',18,13,33);
		$pdf->MultiCell(100, 6, "Dear $username,");
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, 'Thank you for using Gambay to place your order. ');
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, 'Here is the receipt for your records: ');
		$pdf->Ln(3);
			
		if(is_array($obj)){	
		
			$pdf->Cell(25, 8,'Qty',1,0,'L',0);
			$pdf->Cell(40, 8,'Item',1,0,'L',0);
			$pdf->Cell(50, 8,'Serving Type',1,0,'L',0);
			$pdf->Cell(30, 8,'Sub Total',1,1,'L',0);
			
			foreach($obj as $k=>$v){
				if(is_array($v)){
					foreach($v as $key1=>$val){ 
						$px=$val['item_price'];
						$prx+=$px;
						
						$pdf->Cell(25, 6,$val['quantity'],1,0,'L',0);
						$pdf->Cell(40, 6,$val['item_name'],1,0,'L',0);
						$pdf->Cell(50, 6,$val['serving_name'],1,0,'L',0);
						$pdf->Cell(30, 6,$val['item_price'],1,1,'L',0);
						
					}
				}	
			}
		}
		
		$pdf->Ln(6);
		$pdf->Cell(25, 8,'Sub Total',1,0,'L',0);
		$pdf->Cell(40, 8, "$prx", 1,1,'L',0);
		$pdf->Cell(25, 8,'Tax',1,0,'L',0);
		$total_tax=$obj['total_tax']?$obj['total_tax']:0;
		$pdf->Cell(40, 8, "$total_tax", 1,1,'L',0);
		$pdf->Cell(25, 8,'Coupon',1,0,'L',0);
		$coupon_code =$obj['coupon_code']?$obj['coupon_code']:0;
		$pdf->Cell(40, 8, "$coupon_code", 1,1,'L',0);
		$pdf->Cell(25, 8,'Tip',1,0,'L',0);
		$tip=$obj['tip']?$obj['tip']:0;
		$pdf->Cell(40, 8, "$tip", 1,1,'L',0);
		$pdf->Cell(25, 8,'Total',1,0,'L',0);
		$total=$res[0]['order_amount'];
		$pdf->Cell(40, 8, "$total", 1,1,'L',0);
		$pdf->Ln(3);
		
		$pdf->MultiCell(100, 6, 'Thank you very much for shopping by!');
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, "$manager_name");
		$pdf->MultiCell(100, 6, "$venue_name");
		$pdf->MultiCell(100, 6, "$address, $city $state, $zipcode");
		$pdf->MultiCell(100, 6, "$mobile");
		$pdf->MultiCell(100, 6, "$contact_email");
		$pdf->MultiCell(100, 6, "$website");
		$filename="invoice.pdf";
		$dir="../api/";
		$pdf->Output($dir.$filename, 'F');
		//return $pdf;
	}

	public static function createPDFForOrderStatus($order_id){
		global $conn;
		
		$sql="SELECT `order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.status,staff_order.*,`order`.created_on as oc,DATE_FORMAT(`order`.created_on,'%d-%m-%Y') as open_time, `order`.id as oid,venue.id as vid,venue.*,venue.mobile as v_mobile,(select pictures.url from pictures where pictures.venue_id=venue.id) as url, users.*,
			CASE 
                  WHEN DATEDIFF(NOW(),`order`.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),`order`.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
            END as time_elapsed
		FROM `order` join venue on venue.id=order.venue_id join users on users.id=`order`.user_id left join staff_order on staff_order.order_id=`order`.id where `order`.id=:order_id";
	    
		$sth=$conn->prepare($sql);
	    $sth->bindValue('order_id',$order_id);
	    try{$sth->execute();}catch(Exception $e){}
	    $res=$sth->fetchAll();
		
		$obj= json_decode($res[0]['bill_file'],true);
  
		$venue_name=$res[0]['venue_name'];
		$v_mobile=$res[0]['v_mobile'];
		$contact_email=$res[0]['contact_email'];
		$username=$res[0]['username'];
		$dob=$res[0]['dob'];
		$mobile=$res[0]['mobile'];
		$email=$res[0]['email'];
		$address=$res[0]['address'];
		$city=$res[0]['ciy'];
		$state=$res[0]['state'];
		$website=$res[0]['website'];
		$zipcode=$res[0]['zipcode'];
		$venue_id=$res[0]['vid'];
		
		$sql1="SELECT M.id, M.username FROM  `manager_venue` MV JOIN venue V ON V.id = MV.venue_id JOIN manager M ON M.id = MV.manager_id
				WHERE V.id =:venue_id";
		$sth1=$conn->prepare($sql1);
	    $sth1->bindValue('venue_id',$venue_id);
	    try{$sth1->execute();}catch(Exception $e){}
	    $res1=$sth1->fetchAll();
		$manager_name=$res1[0]['username'];
		
		$pdf= new FPDF();
		$pdf->SetAutoPageBreak(true, 0);
		$pdf->AddPage(1);
		$pdf->SetFontSize(10);
		//$pdf->Image('../uploads/4575Logo.png ',18,13,33);
		$pdf->MultiCell(100, 6, "Dear $username,");
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, 'Thank you for using Gambay to place your order. ');
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, 'Here is the receipt for your records: ');
		$pdf->Ln(3);
		$pdf->Cell(25, 8,'Order #',1,0,'L',0);
		$pdf->Cell(40, 8,"P$order_id",1,1,'L',0);
		/*$pdf->MultiCell(100, 6, "VENUE:  \n Venue Name: $venue_name \n Mobile: $v_mobile \n Email: $contact_email");
		$pdf->Ln(6);
		$pdf->MultiCell(100, 6, "USER:  \n User Name: $username \n DOB: $dob \n Phone: $mobile \n Email: $email");
		$pdf->Ln(6);
		*/
		if($res[0]['status']==1){
			$pdf->Cell(25, 8,'Ordered',1,0,'L',0);
			$pdf->Cell(40, 8, "$oc", 1,1,'L',0);
		}
		elseif($res[0]['status']==2){
			$oc=$res[0]['oc'];
			$ready_time=$res[0]['ready_time'];
			$pdf->Cell(25, 8,'Ordered',1,0,'L',0);
			$pdf->Cell(40, 8, "$oc", 1,1,'L',0);
			$pdf->Cell(25, 8,'Ready',1,0,'L',0);
			$pdf->Cell(40, 8, "$ready_time", 1,1,'L',0);
		}
		elseif($res[0]['status']==3){
			$oc=$res[0]['oc'];
			$ready_time=$res[0]['ready_time'];
			$closed_time=$res[0]['closed_time'];
			$pdf->Cell(25, 8,'Ordered',1,0,'L',0);
			$pdf->Cell(40, 8, "$oc", 1,1,'L',0);
			$pdf->Cell(25, 8,'Ready',1,0,'L',0);
			$pdf->Cell(40, 8, "$ready_time", 1,1,'L',0);
			$pdf->Cell(25, 8,'Closed',1,0,'L',0);
			$pdf->Cell(40, 8, "$closed_time", 1,1,'L',0);
		}
		elseif($res[0]['status']==4){
			$oc=$res[0]['oc'];
			$void_time=$res[0]['void_time'];
			$pdf->Cell(25, 8,'Ordered',1,0,'L',0);
			$pdf->Cell(40, 8, "$oc", 1,1,'L',0);
			$pdf->Cell(25, 8,'Void',1,0,'L',0);
			$pdf->Cell(40, 8, "$void_time", 1,1,'L',0);
		}
		$pdf->Ln(6);
			
		if(is_array($obj)){	
		
			$pdf->Cell(25, 8,'Qty',1,0,'L',0);
			$pdf->Cell(40, 8,'Item',1,0,'L',0);
			$pdf->Cell(50, 8,'Serving Type',1,0,'L',0);
			$pdf->Cell(30, 8,'Sub Total',1,1,'L',0);
			
			foreach($obj as $k=>$v){
				if(is_array($v)){
					foreach($v as $key1=>$val){ 
						$px=$val['item_price'];
						$prx+=$px;
						
						$pdf->Cell(25, 6,$val['quantity'],1,0,'L',0);
						$pdf->Cell(40, 6,$val['item_name'],1,0,'L',0);
						$pdf->Cell(50, 6,$val['serving_name'],1,0,'L',0);
						$pdf->Cell(30, 6,$val['item_price'],1,1,'L',0);
						
					}
				}	
			}
		}
		
		$pdf->Ln(6);
		$pdf->Cell(25, 8,'Sub Total',1,0,'L',0);
		$pdf->Cell(40, 8, "$prx", 1,1,'L',0);
		$pdf->Cell(25, 8,'Tax',1,0,'L',0);
		$total_tax=$obj['total_tax']?$obj['total_tax']:0;
		$pdf->Cell(40, 8, "$total_tax", 1,1,'L',0);
		$pdf->Cell(25, 8,'Coupon',1,0,'L',0);
		$coupon_code =$obj['coupon_code']?$obj['coupon_code']:0;
		$pdf->Cell(40, 8, "$coupon_code", 1,1,'L',0);
		$pdf->Cell(25, 8,'Tip',1,0,'L',0);
		$tip=$obj['tip']?$obj['tip']:0;
		$pdf->Cell(40, 8, "$tip", 1,1,'L',0);
		$pdf->Cell(25, 8,'Total',1,0,'L',0);
		$total=$res[0]['order_amount'];
		$pdf->Cell(40, 8, "$total", 1,1,'L',0);
		$pdf->Ln(3);
		
		$pdf->MultiCell(100, 6, 'Thank you very much for shopping by!');
		$pdf->Ln(3);
		$pdf->MultiCell(100, 6, "$manager_name");
		$pdf->MultiCell(100, 6, "$venue_name");
		$pdf->MultiCell(100, 6, "$address, $city $state, $zipcode");
		$pdf->MultiCell(100, 6, "$mobile");
		$pdf->MultiCell(100, 6, "$contact_email");
		$pdf->MultiCell(100, 6, "$website");
		$filename="Bill_file.pdf";
		$dir="../api/";
		$pdf->Output($dir.$filename, 'F');
		//return $pdf;
	}

	public static function sendEmailForOrderPlaced($email,$subjectMail,$bodyMail,$email_back){

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
	  $mail->AddAttachment("../api/invoice.pdf");
	 if(!$mail->Send()){
			$success='0';
			$msg="Error in sending mail";
	  }else{
			$success='1';
			$msg="snt";
	  }
	} catch (phpmailerException $e) {
	  $msg=$e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $msg=$e->getMessage(); //Boring error messages from anything else!
	}
	//echo $msg;
}
	
	public static function sendEmailWithAttachment($email,$subjectMail,$bodyMail,$email_back){

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
	  $mail->AddAttachment("../api/Bill_file.pdf");
	 if(!$mail->Send()){
			$success='0';
			$msg="Error in sending mail";
	  }else{
			$success='1';
			$msg="snt";
	  }
	} catch (phpmailerException $e) {
	  $msg=$e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $msg=$e->getMessage(); //Boring error messages from anything else!
	}
	//echo $msg;
}
	
}

?>