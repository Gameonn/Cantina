<!-- Edit venue Category-->
<div class="modal fade" id="add_categories" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Venue Category</h4>
      </div>
      <div class="modal-body">
    <form action="functions.php" method="post" enctype="multipart/form-data">
<div class="box-body">
      <div class="col-md-12">
              <h3>Create Venue</h3>
              </div>
      <div class="col-md-7">
      <div class="row form-group">
      <div class="col-md-6">
          <input type="text" name="category_name" class="form-control" placeholder="Category Name" required/>
      </div>  
      <div class="col-md-6">
      <input type="text" name="tax_name" class="form-control" placeholder="Tax Name" required/>
      </div>
      </div>      
         <div class="form-group">
              <input type="number" name="percentage" class="form-control" placeholder="Percentage" required/>
         </div>
    
    <div class="row form-group">
      <div class="col-md-12">
              <input type="text" name="tax_desc" class="form-control" placeholder="Tax Description" required/>
      </div>  
      </div> 
      <div class="row form-group">
      <div class="col-md-12">
                        <input type="text" name="serving_type" class="form-control" placeholder="Serving Type" />
          </div>  
     
      </div> 
                </div>
                <div class="col-md-5 image-upload">
                <img src="../uploads/steam_workshop_default_image.png" class="def-image" style="width: 100%;height: 200px; padding: 3px;border: 1px solid rgb(213, 206, 206);
    border-radius: 4px;">
                <input type="file" name="image" style="display: inline; padding-top:15px;">
                </div>
</div> 
<div class="box-footer">
<button type="submit" class="btn btn-primary">Submit</button>
</div>
</form>
      </div>

    </div>
  </div>
</div>