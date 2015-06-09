<!-- Add Sub Category-->
<?php
$sql="SELECT id from manager where token=:token";
$sth=$conn->prepare($sql);
$sth->bindValue("token", $key);
try{$sth->execute();}catch(Exception $e){}
$mgid=$sth->fetchAll();
$mid=$mgid[0]['id'];

$sql="SELECT id from manager_venue where manager_id=:manager_id";
$sth=$conn->prepare($sql);
$sth->bindValue("manager_id", $mid);
try{$sth->execute();}catch(Exception $e){}
$venueid=$sth->fetchAll();
$vid=$venueid[0]['id'];
?>
<div class="modal fade" id="add_subcategories" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Sub Category</h4>
      </div>
      <div class="modal-body">
    <form action="functions.php" method="post" enctype="multipart/form-data">
     
      <div class="row form-group">
      <div class="col-md-12 input_fields_wrap" style="text-align:center;">
        
       <div class="col-md-10">   <input type="text" name="subcategory_name[]" class="form-control" placeholder="SubCategory Name" required/></div>
       <div class="col-md-2">   <button class="add_subfield_button_1"><i class="fa fa-plus"> </i></button></div>
            <input type="hidden" name="event" value="add-subcategory">
              <input type="hidden" name="venue_id" value="<?php echo $vid; ?>">
              <input type="hidden" id="subcatid" name="parent_id" value=0>
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
    var add_button      = $(".add_subfield_button_1"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="serve"><div class="col-md-10"> <input type="text" name="subcategory_name[]" class="form-control" placeholder="SubCategory Name" required/></div><div class="col-md-2"><a href="#" class="remove_field"><button><i class="fa fa-times-circle-o"> </i></button></a></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
</script>