<?php
require_once('php_include/db_connection.php');

$json=json_encode($_POST);
$resultArr=json_decode($json,TRUE); //json parsing


$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
$sth->bindValue('json',$json);
try{$sth->execute();}
catch(Exception $e){
echo $e->getMessage();
}

//$payment_type=isset($resultArr['proof_of_payment']['adaptive_payment']) ? "adaptive_payment" : (isset($resultArr['proof_of_payment']['rest_api']) ? "rest_api" : 0);
//payment attributes
//$currency_code=isset($resultArr['payment']['currency_code']) ? $resultArr['payment']['currency_code'] : 0;
//$amount=$resultArr['payment']['amount'];

//if($payment_type=="adaptive_payment"){ // adaptive payment

	$pUser_id="jindal.ankit89-facilitator_api1.gmail.com";
	$pPassword="QKY6ZFXTLLAL29L7";
	$pSignature="AiPC9BjkCyDFQXbSkoZcgqH3hpacAdXck1YOI6LXyjykDyYbwTyDv63";
	$pay_key=$resultArr['proof_of_payment']['adaptive_payment']['pay_key'];
	$app_id=$resultArr['proof_of_payment']['adaptive_payment']['app_id'];
	
	// calling curl for adaptive payment {no access token required in adaptive payments}
	$pHttp_headers=array(
		"X-PAYPAL-SECURITY-USERID: {$pUser_id}",
		"X-PAYPAL-SECURITY-PASSWORD: {$pPassword}",
		"X-PAYPAL-SECURITY-SIGNATURE: {$pSignature}",
		"X-PAYPAL-APPLICATION-ID: {$app_id}",
		"X-PAYPAL-REQUEST-DATA-FORMAT: NV",
		"X-PAYPAL-RESPONSE-DATA-FORMAT: JSON"
	);
	$pReq_data="payKey={$pay_key}&requestEnvelope.errorLanguage=en_US";	 //NV format
	
	// Step 2: POST IPN data back to PayPal to validate
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,"https://svcs.sandbox.paypal.com/AdaptivePayments/PaymentDetails?".$pReq_data);
	//curl_setopt($ch,CURLOPT_URL,"https://svcs.paypal.com/AdaptivePayments/PaymentDetails?".$pReq_data);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $pReq_data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $pHttp_headers);
	$pResult = curl_exec($ch);
	if(!$pResult) {
		$success=0;
		$msg="Server Error: No response from Paypal";
	}
	curl_close($ch);
	$pResult=json_decode($pResult,true);

//}
?>