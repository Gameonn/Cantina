<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>

<?php 

  $sql="SELECT * from venuetype";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){}
  $venuetypes=$sth->fetchAll();

  $sql="SELECT id from manager where token=:token and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("token", $key);
  try{$sth->execute();}catch(Exception $e){}
  $mgid=$sth->fetchAll();
  $mid=$mgid[0]['id'];
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/jquery-filestyle.css" rel="stylesheet" type="text/css" />
  <body>
    <div class="wrapper row-offcanvas row-offcanvas-left">
      <!-- Left side column. contains the logo and sidebar -->
      <?php require_once "../php_include/manager_leftmenu.php"; ?>
      <!-- right-side -->
      <aside class="right-side">                
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Create Venue
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Create Venue</a></li>
            
          </ol>
        </section>   

        <!-- Main content -->
        <section class="content" style="text-align:center;">
            <?php //error div
            if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
            <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?php echo $_REQUEST['msg']; ?>
            </div>
            <?php } // --./ error -- ?>
            
            
            <form action="functions.php" id="create_venue_form" method="post" enctype="multipart/form-data">
              <div class="body bg-gray">
                     
                    <div class="col-md-7">
                      <div class="row form-group">
                        <div class="col-md-6">
                        <label> Venue Name </label><br>
                          <input type="text" name="venuename" class="form-control" placeholder="Venue Name" required/>
                        </div>  
                        <div class="col-md-6">
                        <label> City </label><br>
                          <input type="text" id="city" name="city" class="form-control" placeholder="City" required/>
                        </div>
                      </div>      
                      <div class="form-group">
                      <label> Venue Address </label><br>
                        <input type="text" name="address" id="address" class="form-control v-address" placeholder="Address" required/>
                        <div class="push-down"></div>
                      </div>
                      
                      <div class="row form-group">
                        <div class="col-md-6">
                        <label> State </label><br>
                         <input type="text" name="state" class="form-control" placeholder="State" required/>
                       </div>  
                       <div class="col-md-6">
                       <label> Zipcode </label><br>
                        <input type="text" name="zipcode" class="form-control" placeholder="Zipcode" required/>
                      </div>
                    </div> 
                    
                     <div class="row form-group">
                        <div class="col-md-6">
                        <label> Contact Email </label><br>
                         <input type="email" name="contact_email" class="form-control" placeholder="Contact Email" required/>
                       </div>  
                       <div class="col-md-6">
                       <label> Website Url</label><br>
                        <input type="url" name="website" class="form-control" placeholder="Website URL" required/>
                      </div>
                    </div> 
                    
                    <div class="row form-group">
                      <div class="col-md-6">
                      <label> Paypal Email </label><br>
                        <input type="email" name="paypal" class="form-control" placeholder="Paypal Email" />
                      </div>  
                      <div class="col-md-6">
                      <label> Venue Cusines </label><br>
                        <select class="form-control" name='type'>
                          <?php foreach($venuetypes as $venuetype){ ?>
                          <option value="<?php echo $venuetype['id']; ?>"><?php echo $venuetype['type']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div> 
                    <div class="row form-group">
                      <div class="col-md-6">
                      <label> Fax Number </label><br>
                        <input type="text" name="fax" class="form-control" placeholder="Fax Number" />
                      </div>  
                      <div class="col-md-6">
                      <label> Contact Number </label><br>
                       <!--  <input type="text" name="contact" class="form-control"  placeholder="Contact Number" data-inputmask='"mask": "(999) 999-9999"' data-mask required/> -->
					   <input type="text" class="form-control" name="contact" data-inputmask='"mask": "(999) 999-9999"' data-mask required/>
                      </div>
                    </div>
                     <div class="row form-group">
                      <div class="col-md-6">
                      <label> Square Foot </label><br>
                        <input type="text" name="square_foot" class="form-control" placeholder="Square Foot" />
                      </div>  
                      <div class="col-md-6">
                      <label> No of Awards </label><br>
                        <input type="number" name="awards" class="form-control"  placeholder="Awards" />
                      </div>
                    </div>
                     <div class="row form-group">
                      <div class="col-md-6">
                      <label> No of Tables </label><br>
                        <input type="number" name="tables" class="form-control" placeholder="Number of Tables" />
                      </div>  
                      <div class="col-md-6">
                      <label> No of Seats </label><br>
                        <input type="number" name="seats" class="form-control"  placeholder="Number of Seats" />
                      </div>
                    </div>
                    
                    <div class="form-group">
                    <label> Parking Information </label><br>
                      <input type="text" name="parking" class="form-control" placeholder="Parking Information" />
                    </div> 
                    
                    <div class="row form-group">
                      
                      <div class="col-md-4">
                        <label> Days</label><br>
                        <select id="chktime" multiple="multiple" name="days[]">
                         <option value="1">Monday</option>
                         <option value="2">Tuesday</option>
                         <option value="3">Wednesday</option>
                         <option value="4">Thursday</option>
                         <option value="5">Friday</option>
                         <option value="6">Saturday</option>
                         <option value="7">Sunday</option>
                       </select>
                         <!-- <input type="button" id="btnget" value="Get Selected Values" /> -->
                     </div>
                  
                     <div class="col-md-4">
                      <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                            <label>Open Time:</label>
                                            <div class="input-group">
                                                <input type="text" name="start_time" class="form-control timepicker"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div><!-- /.input group -->
                                        </div><!-- /.form group -->
                                    </div>
                  </div>
                    
                    
                    <div class="col-md-4">
                      <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                            <label>Close Time:</label>
                                            <div class="input-group">
                                                <input type="text" name="end_time" class="form-control timepicker"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div><!-- /.input group -->
                                        </div><!-- /.form group -->
                                    </div>
                    	</div>                              	
                 	 </div>
                  
                  <div class="form-group">
                  <label> Other Information </label>
                    <input type="text" name="description" class="form-control" placeholder="Other Information" />
                  </div>
                </div>
                <div class="col-md-5 image-upload">
                  <img src="../uploads/steam_workshop_default_image.png" class="def-image" style="width: 100%;height: 200px; padding: 3px;border: 1px solid rgb(213, 206, 206);
                  border-radius: 4px;">
                    <input type="file" name="image" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
                </div>
              </div>
              <div class="footer" style="width: 20%;margin-left: auto;margin-right: auto;">                                                               
                <button type="submit" class="btn bg-olive btn-block" id="form_submit" >Submit</button>
                <a class="btn bg-red btn-block" href="dashboard.php"> Cancel</a>
              </div>
              <!-- hidden -->
              <input type="hidden" name="token" value="<?php echo $key; ?>">
              <input type="hidden" name="mgid" value="<?php echo $mid; ?>">
              <input type="hidden" name="event" value="create-venue">
              <input type="hidden" name="redirect" value="dashboard.php">

              
            </form>
            <!-- <input type="button" id="btnget" value="Get Selected Values" /> -->

          </section><!-- /.content -->
        </aside><!-- /.right-side -->
      </div><!-- ./wrapper -->

      <!-- add new calendar event modal -->


      <!-- jQuery 2.0.2 -->
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
      <!-- jQuery UI 1.10.3 -->
      <script src="../assets/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
      <!-- Bootstrap -->
      <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
       <script src="../assets/js/plugins/timepicker/bootstrap-timepicker.min.js" type="text/javascript"></script>
      <!-- Morris.js charts -->
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="../assets/js/plugins/morris/morris.min.js" type="text/javascript"></script>
      <!-- jvectormap -->
      <script src="../assets/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
      <script src="../assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
      <!-- Bootstrap WYSIHTML5 -->
      <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>
      
      <!-- iCheck -->
      <script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

      <!-- AdminLTE App -->
      <script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script>
      
      <!-- DATA TABES SCRIPT -->
      <script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
      <script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
      <script src="../assets/js/bootstrap-multiselect.js" type="text/javascript"></script>
	          <!-- InputMask -->
        <script src="../assets/js/jquery.inputmask.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.extensions.js" type="text/javascript"></script>
 	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
 	<script src="../assets/js/jquery.validate.js" type="text/javascript"></script>
 <script>
$("[data-mask]").inputmask();
$("#create_venue_form").validate();

$("#form_submit").click(function(){
var city=$('#city').val();
var address=$('#address').val();
var address=address+',' + city;
//alert(address);
            var geocoder =  new google.maps.Geocoder();
    geocoder.geocode( { 'address': $('#city').val()}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
          $('#create_venue_form').submit();
           // $('.push-down').text("location : " + results[0].geometry.location.lat() + " " +results[0].geometry.location.lng()); 
          } else {
           $('.push-down').empty();
            $('.push-down').append("<p style='color:red;'>Incomplete/Wrong Address  </p>");
          }
        });
});


$(document).ready(function () {
		
		$("#create_venue_form").validate();
		
			$('#form_submit').on('click', function()
			{ 
			var city=$('#city').val();
		var address=$('#address').val();
		var address=address+',' + city;
			$('.v-address').rules('add', {  
           		 required: true,
			}); 
			//alert("Please select item name");
	            var geocoder =  new google.maps.Geocoder();
    geocoder.geocode({ 'address': address}, function(results, status) {		
				 if (status == google.maps.GeocoderStatus.OK) {
				//alert("Div1 exists");
				}
				else{
				$('.push-down').empty();
            $('.push-down').append("<p style='color:red;'>Incomplete/Wrong Address  </p>");
				}
				});
				
			$('#add_item_form').submit();
			}); 	
	});

  /*  $('.v-address').unbind().change(function(){

    var address= $(this).val();
if(address){
    var event1='get_lat_long'
       $.post("functions.php",
        
    {
    event: event1,
	address: address
    },
    
    function(data){

    console.log(data);
    if(data==0)
	alert('Please Enter correct address');
    }
    );
 }
    });*/
    
</script> 
      <script type="text/javascript">
       $(document).ready(function() {
        $('#chktime').multiselect({
  //includeSelectAllOption: true
	});
  //$('#btnget').click(function() {
  //alert($('#chktime').val());
  //})
 $(".timepicker").timepicker({
           showInputs: false,
           pick12HourFormat: false,
            timeFormat: 'HH:mm',
        altTimeFormat: "HH:mm",
        pickerTimeFormat: "HH:mm" 
          // pickTime: true
                });
                $('input.timepicker').timepicker({
        timeFormat: 'HH:mm:ss',
        pick12HourFormat: false ,
         timeFormat: 'HH:mm',
        altTimeFormat: "HH:mm",
        pickerTimeFormat: "HH:mm"
        });
             
 // $('#cb1,#cb2,#cb3,#cb4,#cb5,#cb6,#cb7').click(function(){
   //     $(this).val(this.checked ? 1 : 0);
   // });
     });
 //    $(document).ready(function(){
  
//});
     </script>
 <script>
 $(document).ready(function(){
 //$(":file").filestyle({buttonText: "Find file"});
 //$(":file").filestyle({badge: false});
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

                 this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100 %').height(200);
               }
               reader.readAsDataURL(files[0]);
             });

    </script> 
  </body>
  </html>
  