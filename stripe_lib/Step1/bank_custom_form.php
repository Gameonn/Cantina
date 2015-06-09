<?php 

require_once("../../php_include/db_connection.php");
require_once('../stripe/init.php');
require_once('../stripe/lib/Stripe.php');
  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );

    
    \Stripe\Stripe::setApiKey($stripe['secret_key']);
    
   if($_POST){
   print_r($_POST);die;
   \Stripe\Stripe::setApiKey($stripe['secret_key']);
   
	$account = \Stripe\Account::retrieve("acct_15rTMLISz4VN6mw2");
	$account->bank_account = $_POST['stripeToken'];
	$account->save();
   
   
   	   $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$account);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }

   } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" href="../public/css/normalize.css">
    <link rel="stylesheet" href="../public/css/style.css">
  <!-- The required Stripe lib -->
  <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

  <!-- jQuery is used only for this example; it isnt required to use Stripe -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  <script type="text/javascript">
    // This identifies your website in the createToken call below
    //blanking my test key out just for stackoverflow 
    Stripe.setPublishableKey('<?php echo $stripe["publishable_key"]; ?>');
var stripeResponseHandler = function(status, response) {

    var $form = $('#inst-form');
    if (response.error)
    {
      alert("Error");
      // Not sure how to get these errors.
      $form.find('button').prop('disabled', false);
    }
    else
    {
      var token = response.id;
      $form.append($('<input type="hidden" name="stripeToken" />').val(token));
      console.log(response);
      $form.get(0).submit();
    }
  };

  // Now the handler is done, lets use it when the form is submitted.
  // On form submission execute:
  jQuery(function($) {
    $('#inst-form').submit(function(event) {
      // Get the form object.
      var $form = $(this);
     
      // Disable the submit button to prevent repeated clicks
      $form.find('button').prop('disabled', true);
      // Create a token with Stripe
      Stripe.bankAccount.createToken($form, stripeResponseHandler);
      // Prevent the form from submitting with the default action
      return false;
    });
  });
  </script>
  
  <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" > </script>
  
  <link href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel="stylesheet" type="text/css">
  
  <style type="text/css">
  	.col-sm-6.col-sm-offset-3{
  		background-color:white;
  		margin-top:50px;
  		padding:50px;
  	}
  	
  	span{
  		padding-bottom:20px;
  	}
  
  	h3{
  		text-align:center;
  	}
  	
  	.modal-dialog{
  		margin-top:100px;
  	}
  	
  	.modal-footer{
  		text-align:center;
  	}
  	
  	@media (min-width: 768px){
			.modal-dialog {
			  width: 350px;
			  
			}
  		}
  </style>
  
</head>
<body>

<div class="row">

	<div class="col-sm-6 col-sm-offset-3 ">

   <form method="POST" id="inst-form"> 	
	
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
  		Launch modal
	</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"> Charge $10 with Stripe </h3>
      </div>
      <div class="modal-body">
        

    <div class="form-row row">
      <span class="col-sm-10 col-sm-offset-1">
      <label> Bank Location </label>
            <select data-stripe="country" class="form-control">
                <option value="US">United States</option>
            </select>
      </span>
    </div>

    <div class="form-row row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Routing Number  </label>
            <input type="text" class="form-control" size="9" data-stripe="routingNumber"/>
      </span>
    </div>

    <div class="form-row row">
      <span class="col-sm-10 col-sm-offset-1">
       <label> Account Number  </label>
            <input type="text" class="form-control" size="17" data-stripe="accountNumber"/>
    	</span>
    </div>


      </div>
      <div class="modal-footer">
       <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button> -->
        
          <button type="submit" class="btn btn-primary">Make Recipient!</button>
        
      </div>
    </div>
  </div>
</div>

</form>


  <!-- <h1>Charge $10 with Stripe</h1>
           
<form method="POST" id="inst-form">    

    <div class="form-row">
      <span>
      <label> Bank Location  </label>
            <select data-stripe="country" class="form-control">
                <option value="US">United States</option>
            </select>
     </span>
    </div>

    <div class="form-row">
      <span>
      <label>
        Routing Number </label>
            <input type="text" class="form-control" size="9" data-stripe="routingNumber"/>
      	</span>
    </div>

    <div class="form-row">
      <span>
      <label>  Account Number  </label>
            <input type="text" class="form-control" size="17" data-stripe="accountNumber"/>
    	</span>
    </div>


    <button type="submit">Make Recipient!</button>
</form> -->

    	</div>
    
</div> 




</body>
</html>