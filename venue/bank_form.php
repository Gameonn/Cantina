<!-- The required Stripe lib -->
  <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
  <!-- jQuery is used only for this example; it isnt required to use Stripe -->
   <script type="text/javascript">
    // This identifies your website in the createToken call below
    //blanking my test key out just for stackoverflow 
    Stripe.setPublishableKey('<?php echo $stripe["publishable_key"]; ?>');
var stripeResponseHandler = function(status, response) {

    var $form = $('#inst-form');
    if (response.error)
    {
      alert("Enter Valid Details");
      // Not sure how to get these errors.
      $form.find('button').prop('disabled', false);
    }
    else
    {
      var token = response.id;
      $form.append($('<input type="hidden" name="stripeToken" />').val(token));
      console.log(response);
	    var event="set_stripe_status";
	  var venue_id="<?php echo $vid; ?>";
	   $.post("functions.php",
        {
            event: event,
			venue_id: venue_id
        },
            function(data){
			console.log(data);        
            }
        ); 
	  
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
   
  <style type="text/css">
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
  

<div class="row">
	<div class="col-sm-6 col-sm-offset-3" style="background-color:white;margin-top:50px;padding:50px;">
   <form method="POST" id="inst-form"> 	
<!-- Modal -->
<div class="modal fade" id="link_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: #f0f0f2;border-radius: 6px;">
        <a class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img src="../uploads/close-gbN3V5bjbhRxDClwGYFQ.png"> </span></a>
        <h3 class="modal-title" id="myModalLabel" style="text-align:center;"> Stripe Linking </h3>
      </div>
      <div class="modal-body">
     
    <div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
      <label> Bank Location </label>
            <select data-stripe="country" class="form-control">
                <option value="US">United States</option>
            </select>
      </span>
    </div>

    <div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Routing Number  </label>
            <input type="text" class="form-control" size="9" data-stripe="routingNumber" required/>
      </span>
    </div>

    <div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
       <label> Account Number  </label>
            <input type="text" class="form-control" size="17" data-stripe="accountNumber" required/>
    	</span>
    </div>
	</div>
      <div class="modal-footer" style="margin-top:0px;">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->     
          <button type="submit" class="btn btn-primary" load-text>Link Account</button>
        
      </div>
    </div>
  </div>
</div>

</form>
</div>
</div> 
