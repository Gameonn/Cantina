<!-- Add New Category-->
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
<div class="modal fade" id="add_categories" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add New Category</h4>
      </div>
      <div class="modal-body">
    <form action="functions.php" method="post" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-7">
      <div class="row form-group">
      <div class="col-md-12">
          <input type="text" name="category_name" class="form-control" placeholder="Category Name" required/>
      </div> 
      </div>
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
              <input type="hidden" name="event" value="add-category">
              <input type="hidden" name="venue_id" value="<?php echo $vid; ?>">
      </div>  
      </div> 
      <div class="row form-group">
      <label style="display: block; text-align: center;"> Servings</label>
      <div class="col-md-12 input_fields_wrap" style="text-align:center;">
    
                 <div class="col-md-10">  <input type="text" name="serving_type[]" class="form-control" placeholder="Serving Type" /></div>
                 <div class="col-md-2">   <button class="add_field_button"><i class="fa fa-plus"> </i></button></div>
          </div>  

      </div> 
                </div>
                <div class="col-md-5 image-upload">
                <img src="../uploads/steam_workshop_default_image.png" class="def-image" style="width: 100%;height: 200px; padding: 3px;border: 1px solid rgb(213, 206, 206);
    border-radius: 4px;">
                <input type="file" name="image" style="display: inline; padding-top:15px;">
                </div>
</div>
<div class="footer" style="text-align:center;">
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
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="serve"><div class="col-md-10"><input type="text" name="serving_type[]" class="form-control" placeholder="Serving Type" /></div><div class="col-md-2"><a href="#" class="remove_field"><button><i class="fa fa-times-circle-o"></i></button></a></div></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault();
     // alert($(this).parent('.serve'));
         $(this).parents('.serve').remove(); x--;
    })
});
</script>
 <script type="text/javascript">

            $('.image-upload').on("change","input[type='file']",function () {
              // alert('hey');
              var files = this.files;
              var reader = new FileReader();
              name=this.value;
              var this_input=$(this);
              reader.onload = function (e) {

               this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100%').height(200);
             }
             reader.readAsDataURL(files[0]);
           });

          </script>