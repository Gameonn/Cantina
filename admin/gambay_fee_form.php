<div class="modal fade" id="gambay_fee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	 <form method="POST" action="eventHandler.php"> 
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"> Set Gambay Fee </h3>
      </div>
      <div class="modal-body">
		<div class="form-group row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Venue </label>
            <input type="text" class="form-control" id="vname" placeholder="Venue Name" value=0 name="venue_name"/>
		
      </span>
    </div>
	
    <div class="form-row row">
      <span class="col-sm-10 col-sm-offset-1">
      <label>  Charge Amount  </label>
            <input type="number" class="form-control" placeholder="Charge Amount In Cents" name="charge"/>
			<input type="hidden" name="venue_id" value=0 id="vid">
			<input type="hidden" name="event" value="set_charge" >
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