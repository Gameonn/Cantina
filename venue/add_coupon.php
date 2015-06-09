<!-- Add New Coupon-->

<div class="modal fade" id="add_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add New Coupon</h4>
      </div>
      <div class="modal-body">
    <form action="functions.php" method="post" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-7">
      <div class="col-md-12 form-group" style="padding: 0px;">
         <label> Coupon Name </label><br>
          <textarea name="coupon_name" class="form-control" placeholder="Coupon Name" required> </textarea>
      </div>
      <div class="row form-group">
      <div class="col-md-6">
       <label> Limit </label><br>
      <input type="number" name="limit" class="form-control" placeholder="Limit" />
      </div>
      <div class="col-md-6">
        <label> Status </label><br>
     <input name='status' class='status switch-radio2' checked data-radio-all-off="true" type="radio" > 
      </div>
      </div>  
              <div class="col-md-12 form-group" style="padding: 0px;">
         <label> Expiry Date </label><br>
      <input type="text" id="expiry" name="expiry" class="form-control" placeholder="Expiry Date" required/>
      </div>
       
     <div class="col-md-12 form-group" style="padding: 0px;" id="coupon_val">
     <label> Coupon Value </label><br>
     <div style="display: -webkit-box;font-size: 20px;font-weight: bolder;">
      <input type="number" name="value" class="form-control coupon_value" placeholder="Value" />
      <span class="input-group-addon" style="display: inline;padding: 8px 12px 9px 12px;">$</span>
      </div>
      </div>
      
        <div class="col-md-12 or_div" style="text-align: center;font-size: 18px;">
        OR
        </div>
        
         <div class="col-md-12 form-group" style="padding: 0px;" id="coupon_percent">
        <label> Coupon Percentage</label><br>
        <div style="display: -webkit-box;font-size: 20px;font-weight: bolder;">
    <input type="number" name="percentage" class="form-control coupon_percentage" placeholder="Percentage" />
    <span class="input-group-addon" style="display: inline; padding: 8px 12px 9px 12px;">%</span>
    </div>
      </div>

      <div class="col-md-12 form-group" style="padding: 0px;">
      <input type="hidden" name="event" value="add-coupon">
      <input type="hidden" name="venue_id" value="" id="vcouponid"> 
     
         </div>  

      </div>
                <div class="col-md-5 image-upload">
                <img src="../uploads/122-h_main-w.png" class="def-image" style="width: 100%;height: 200px; padding: 3px;border: 1px solid rgb(213, 206, 206);
    border-radius: 4px;">
	<input type="file" name="image" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
	 <p class="text-yellow"> Image should be in the ratio of (3:2) </p>
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
<script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script> 
<script type="text/javascript">
 $(document).ready(function() {
    $("[name='status']").bootstrapSwitch(); 
    $("#expiry").datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
    });               
});

</script>

<script type="text/javascript">
$('.coupon_value').change(function(){
var val=$(this).val();
if(val!=0){
$('#coupon_percent').fadeOut();
$('.or_div').fadeOut();
}
else{
$('#coupon_percent').fadeIn();
$('.or_div').fadeIn();
}
});

$('.coupon_percentage').change(function(){
var val=$(this).val();
if(val!=0){
$('#coupon_val').fadeOut();
$('.or_div').fadeOut();
}
else{
$('#coupon_val').fadeIn();
$('.or_div').fadeIn();
}
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