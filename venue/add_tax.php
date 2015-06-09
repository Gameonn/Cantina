<!-- Add Tax-->
<?php

?>
<div class="modal fade" id="add_tax" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Tax</h4>
      </div>
      <div class="modal-body">
    <form action="functions.php" method="post" enctype="multipart/form-data">
       <div class="row form-group">
      <div class="col-md-12">
      <input type="text" name="tax_name" class="form-control" placeholder="Tax Name" required/>
      </div>
      </div>      
         <div class="form-group">
              <input type="number" name="percentage" class="form-control" placeholder="Percentage" required/>
         </div>
    
    <div class="row form-group">
      <div class="col-md-12">
              <input type="text" name="tax_desc" class="form-control" placeholder="Tax Description" required/>
              <input type="hidden" name="event" value="add-tax">
              <input type='hidden' name="menucategory_id" id="catid" value="">
      </div>  
      </div> 
     
      
              <div class="footer">
<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_subfield_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div> <input type="text" name="subcategory_name[]" class="form-control" placeholder="SubCategory Name" required/><a href="#" class="remove_field"><i class="fa fa-times-circle"> </i></a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>