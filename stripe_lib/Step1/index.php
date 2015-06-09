<?php 

require_once("../../php_include/db_connection.php");
require_once('../stripe/init.php');
require_once('../stripe/lib/Stripe.php');
  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  
    <title>Wilde Things</title>
    <link rel="stylesheet" href="../public/css/normalize.css">
    <link rel="stylesheet" href="../public/css/style.css">
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    
    <script type="text/javascript">
  // This identifies your website in the createToken call below
  Stripe.setPublishableKey("<?php echo $stripe['publishable_key']; ?>");
  
</script>
  </head>
  <body>
  <div id="container">
<?php
\Stripe\Stripe::setApiKey($stripe['secret_key']);
  

  if ($_POST) {
  
 
\Stripe\Stripe::setApiKey($stripe['secret_key']);
  $user_id=1;
  
  $token = $_POST['stripeToken'];
    $a=json_encode($_POST);
	
/*$acc=\Stripe\Account::create(
  array(
    "country" => "US",
    "managed" => true
  )
);
$token="tok_15rXJ8HPiBMaAbxHxpQTIasb";
$acc=str_replace("Stripe\Account JSON: "," ",$acc);

  $sth=$conn->prepare('Insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$acc);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  
	$c_charge = \Stripe\Charge::create(
    array(
      "amount" => 1000, // amount in cents
      "currency" => "usd",
      "source" => $token,
      "description" => "Example charge",
      "application_fee" => 123 // amount in cents
    ),
    array("stripe_account" => 'acct_15rTMLISz4VN6mw2')
  );
	   $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$c_charge);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }
	$pClient_id="ca_60yGIrgBVtAXtIWVMMKjKe2xOY7za8I1";
	$pSecret=$stripe['secret_key'];

	$pHttp_headers=array(
    "Content-Type: application/json",
    "Authorization: Bearer {$stripe['secret_key']}"
 	 );
  
	$authorization_code = "ac_63dZqsgytJrBCWppsRFLlnKJfeM2sIMH";

$params = array();
$params['grant_type'] = 'authorization_code';
$params['bank_account'] = $token;
$params['code']=$authorization_code;

$url = 'https://connect.stripe.com/oauth/token';

$ch = curl_init();
$header = array ();
curl_setopt( $ch, CURLOPT_HTTPHEADER, $pHttp_headers );
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_POST, true );
curl_setopt( $ch, CURLOPT_POSTFIELDS,json_encode($params));

$response = curl_exec( $ch );
$response=json_decode($response,true);
$access_token=$response['access_token'];


    $data=array(
  'bank_account'=> $token
  );
$pHttp_headers=array(
		"Content-Type: application/x-www-form-urlencoded",
		"Authorization: Bearer {$stripe['secret_key']}"
	);


  	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"https://api.stripe.com/v1/accounts/acct_15rTMLISz4VN6mw2/bank_accounts");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $pHttp_headers);
	//curl_setopt($ch, CURLOPT_USERPWD,$stripe['secret_key']);
	$pResult = curl_exec($ch);
	curl_close($ch);
	$pResult=json_decode($pResult,true);
	print_r($pResult);die;

  
  // Create a Customer
/*$customer = \Stripe\Customer::create(array(
  "source" => $token,
  "description" => "Example customer")
);

  $charge = \Stripe\Charge::create(array(
  "amount" => 1000, # amount in cents, again
  "currency" => "usd",
  "customer" => $customer->id)
);*/
   //$rt=Stripe_Charge::retrieve($charge->id); 
	   
	/*$acc=	\Stripe\Account::create(
		  array(
		    "country" => "US",
		    "managed" => true
		  )
		);
  
  
  
  // Charge the Customer instead of the card
  /*$charge=Stripe_Charge::create(array(
    "amount" => 1000, # amount in cents, again
    "currency" => "usd",
    "customer" => $customer->id)
  );
     $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$acc);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  

  
  // Save the customer ID in your database so you can use it later
   /* $sth=$conn->prepare('insert into stripe_customer values(DEFAULT,:user_id,:customer_id)');
  $sth->bindValue('user_id',$user_id);
  $sth->bindValue('customer_id',$customer->id);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }*/
  /*
  $sql="select * from stripe_customer where user_id=:user_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue('user_id',$user_id);
  try{$sth->execute();}
  catch(Exception $e){}
  $res=$sth->fetchAll();
  $customerId=$res[0]['customer_id'];
  //saveStripeCustomerId($user, $customer->id);
  
  // Later...
  //$customerId = getStripeCustomerId($user);
  
  $ch=Stripe_Charge::create(array(
    "amount"   => 150,
    "currency" => "usd",
    "customer" => $customerId)
  );*/
 /* 
     $sth=$conn->prepare('Insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$c_charge);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }*/
  /*
  $chr = Stripe_Charge::retrieve($ch->id);
  $re = $chr->refund();
  $sth=$conn->prepare('Insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$re);
  try{$sth->execute();}
  catch(Exception $e){
  //echo $e->getMessage();
  }
  
/*  Stripe_Customer::retrieve("cu_15owIOHPiBMaAbxHLpZQT8FW")->sources->all(array(
  'limit'=>1, 'object' => 'card'));*/
    
  }
  else
  { ?>
    <h2>Wilde Things</h2>
    <h3>Purchase a quote by Oscar Wilde today! Only $535! Limited supply and going fast, buy now!!</h3>

    <form action="index.php" method="post"> 
    
         <script src="https://button.stripe.com/v1/button.js" class="stripe-button"
        data-key="<?php echo $stripe['publishable_key']; ?>"
        data-amount=53
        data-image="/square-logo.png"
        data-description="One Wilde quote"
        data-label="Buy"></script> 
    </form>
  <?php
  }
?>
  </div><!-- #container -->
  </body>
</html>