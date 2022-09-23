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
   <form method="POST" id="email-form" action="functions.php"> 	
<!-- Modal -->
<div class="modal fade" id="send_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: #f0f0f2;border-radius: 6px;">
        <a class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img src="../uploads/close-gbN3V5bjbhRxDClwGYFQ.png"> </span></a>
        <h3 class="modal-title" id="myModalLabel" style="text-align:center;"> Send Update Stripe Account Email </h3>
      </div>
      <div class="modal-body">
     
    <div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
      <label> Subject</label>
            <select name="subject" class="form-control">
                <option value="UAE">Update Stripe Account</option>
            </select>
      </span>
    </div>

    <div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Message </label><br>
        <textarea name="body_msg" class="form-control" rows="6" placeholder="Enter your message here with your details">		   
		   </textarea>
      </span>
    </div>

	</div>
	<input type="hidden" value="mail_send" name="event">
	<input type="hidden" value="<?php echo $vid; ?>" name="venue_id">
      <div class="modal-footer" style="margin-top:0px;">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->     
          <button type="submit" class="btn btn-primary" load-text>Send Email</button>
        
      </div>
    </div>
  </div>
</div>

</form>
</div>
</div> 
