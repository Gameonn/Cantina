<?php
require_once('../php_include/db_connection.php');

$json=json_encode($_POST);
$resultArr=json_decode($json,TRUE); //json parsing


$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
$sth->bindValue('json',$json);
try{$sth->execute();}
catch(Exception $e){
echo $e->getMessage();
}


// credit card payment
	$pClient_id="AYxtHRD6mC1QddNP0R9cteYapiAMPeaN6JG02z6xA0Y-3RQ2tW7OofUSYLaI";
	$pSecret="EN5qghCP5F2fZCkjPvWZq_Odh7hInthA07v8yHYfwTj1VPDkNccJtklE8BxE";
	$payment_id=$resultArr['response']['id'];
	
	//calling curl in credit card payment {access token is required}
	// get access token
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"https://api.sandbox.paypal.com/v1/oauth2/token");
	//curl_setopt($ch,CURLOPT_URL,"https://api.paypal.com/v1/oauth2/token");
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json","Accept-Language: en_US"));
	curl_setopt($ch, CURLOPT_USERPWD, $pClient_id.':'.$pSecret);
	$pResult = curl_exec($ch);
	curl_close($ch);
	$pResult=json_decode($pResult,true);
	$access_token=isset($pResult['access_token']) ? $pResult['access_token'] : 0;
	// get verification response using this token
	$pHttp_headers=array(
		"Content-Type: application/json",
		"Authorization: Bearer {$access_token}"
	);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"https://api.sandbox.paypal.com/v1/payments/payment/".$payment_id);
	//curl_setopt($ch,CURLOPT_URL,"https://api.paypal.com/v1/payments/payment/".$payment_id);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
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
	$pResult=json_decode($pResult,true); //json parsing
	//print_r($pResult);
	
	//print_r($pResult['transactions'][0]['amount']['total']);
	//die; 
	// get required attributes from response (state,total,currency,description,payment_method)
	// NOTE: credit card response is different than adaptive payment response, eg, no transaction id, no correlation id, no timestamp and no acknowledgement, no build.
	$status=$pResult['payments'][0]['state']; //important
	$payment_id=$pResult['payments'][0]['id']; //important
	$payment_method=$pResult['payments'][0]['payer']['payment_method'];
	$amount=$pResult['transactions'][0]['amount']['total']; //important
	$currency_code=$pResult['transactions'][0]['amount']['currency']; //important
	$description=$pResult['transactions'][0]['amount']['description'];
	$json_response=json_encode(array("payment_id"=>$payment_id,"state"=>$status,"amount"=>$amount,"currency"=>$currency_code,"payment_method"=>$payment_method,"description"=>$description));
	$j=json_encode($pResult);
	$sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
	$sth->bindValue('json',$j);
	try{$sth->execute();}
	catch(Exception $e){
	echo $e->getMessage();
	}


?>