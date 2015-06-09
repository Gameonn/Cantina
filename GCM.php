<?php
class GCM
{

public static function send_notification($registration_ids, $message)
	{
	$url = 'https://android.googleapis.com/gcm/send';
	$fields = array(
        'registration_ids' => $registration_ids,
	'delay_while_idle' => true,
	'data' => $message
	);
	$headers = array(
	'Authorization: key='.AUTH_KEY,
	'Content-Type: application/json'
	);
	// Open connection
	$ch = curl_init();
    
	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1);//extra
	//curl_setopt($curl, CURLOPT_NOSIGNAL, 1);//extra
	
	// Disabling SSL Certificate support temporarly
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
	// Execute post
	$result = curl_exec($ch);
	if ($result === FALSE) {
	die('Curl failed: ' . curl_error($ch));
	}
    
	// Close connection
	curl_close($ch);
		//echo var_dump($result);
	}

}
?>