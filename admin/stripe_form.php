<div class="modal fade" id="stripe_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	 <form method="POST" action="eventHandler.php"> 
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"> Create Stripe Account </h3>
      </div>
      <div class="modal-body">
		<div class="form-group row">
		  <span class="col-sm-10 col-sm-offset-1">
		  <label> Country </label>
            <select data-stripe="country" name="country" class="form-control">
				<option value="US">United States</option>
                <option value="AU">Australia</option>
				<option value="AT">Austria</option>
				<option value="BE">Belgium</option>
				<option value="CA">Canada</option>
				<option value="DK">Denmark</option>
				<option value="FI">Finland</option>
				<option value="FR">France</option>
				<option value="DE">Germany</option>
				<option value="IT">Italy</option>
				<option value="LU">Luxembourg</option>
				<option value="NL">Netherlands</option>
				<option value="NO">Norway</option>
				<option value="ES">Spain</option>
				<option value="SE">Sweden</option>
				<option value="CH">Switzerland</option>
				<option value="GB">United Kingdom</option>
            </select>
      </span>
    </div>

    <div class="form-row row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Email  </label>
            <input type="email" class="form-control" placeholder="Email Address" name="email"/>
			<input type="hidden" name="venue_id" value=0 id="vid">
			<input type="hidden" name="event" value="add_stripe_account" >
      </span>
    </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Account</button>
        
      </div>
    </div>
	</form>
  </div>
</div>