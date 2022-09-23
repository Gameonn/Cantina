<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php 
  $obj = new GeneralFunctions; 
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
  
  $sql="SELECT venue_id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("manager_id",$mid);
  try{$sth->execute();}catch(Exception $e){}
  $r=$sth->fetchAll();
  $vid=$r[0]['venue_id'];

    $sql="SELECT * from venue where id=:id and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("id",$vid);
  try{$sth->execute();}catch(Exception $e){}
  $res=$sth->fetchAll();
  $vtypeid=$res[0]['venuetype_id'];

    $sql="SELECT * from pictures where venue_id=:venue_id and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){}
  $picture=$sth->fetchAll();
 	
$sql="SELECT type from venuetype where id=:id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("id",$vtypeid);
  try{$sth->execute();}catch(Exception $e){}
  $vtype=$sth->fetchAll();
  
  $sql="SELECT * from hours_of_operation where venue_id=:id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("id",$vid);
  try{$sth->execute();}catch(Exception $e){}
  $hop=$sth->fetchAll();
  $days=$hop[0]['days'];
  $d=explode(" ",$days);
  $m=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
  	
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="../assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
  </head>
  <!--CSS DATA TABLES -->
  <link href="../assets/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
  <body style="height:initial;">
    <div class="wrapper row-offcanvas row-offcanvas-left">
      <!-- Left side column. contains the logo and sidebar -->
      <?php require_once "../php_include/manager_leftmenu.php"; ?>
      <!-- right-side -->
      <aside class="right-side">                
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Edit Venue
          </h1>
          <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#"><i class="fa fa-desktop"></i> Edit Venue</a></li>
            
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
            
            
            <form action="functions.php" method="post" enctype="multipart/form-data">
              <div class="body bg-gray">
                    <?php //error div
                    if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
                    <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <?php echo $_REQUEST['msg']; ?>
                    </div>
                    <?php } // --./ error -- ?>
                    

                    <div class="col-md-7">
                      <div class="row form-group">
                        <div class="col-md-6">
                       <label> Venue Name </label><br>
                          <input type="text" name="venuename" value="<?php echo $res[0]['venue_name']; ?>" class="form-control" placeholder="Venue Name" required/>
                        </div>  
                        <div class="col-md-6">
                        <label> City </label><br>
                          <input type="text" name="city" class="form-control" value="<?php echo $res[0]['city']; ?>"  placeholder="City" required/>
                        </div>
                      </div>      
                      <div class="form-group">
                      <label> Address </label><br>
                        <input type="text" name="address" class="form-control" value="<?php echo $res[0]['address']; ?>" placeholder="Address" required/>
                      </div>
                      
                      <div class="row form-group">
                        <div class="col-md-6">
                        <label> State </label><br>
                         <input type="text" name="state" class="form-control" value="<?php echo $res[0]['state']; ?>" placeholder="State" required/>
                       </div>  
                       <div class="col-md-6">
                       <label> Zipcode </label><br>
                        <input type="text" name="zipcode" class="form-control" placeholder="Zipcode"  value="<?php echo $res[0]['zipcode']; ?>" required/>
                      </div>
                    </div> 
                    
                    <div class="row form-group">
                        <div class="col-md-6">
                        <label> Contact Email </label><br>
                         <input type="text" name="contact_email" class="form-control" value="<?php echo $res[0]['contact_email']; ?>" placeholder="Contact Email" required/>
                       </div>  
                       <div class="col-md-6">
                       <label> Website </label><br>
                        <input type="text" name="website" class="form-control" placeholder="Website"  value="<?php echo $res[0]['website']; ?>" required/>
                      </div>
                    </div> 
                    
                    <div class="row form-group">
                      <div class="col-md-6">
                      <label> Paypal Email </label><br>
                        <input type="email" name="paypal" class="form-control"  value="<?php echo $res[0]['paypal_email']; ?>" placeholder="Paypal Email" />
                      </div>  
                      <div class="col-md-6">
                      <label> Venue Cusines </label><br>
                        <select class="form-control" id="cusines" name='type'>
                          <option value="<?php echo $res[0]['venuetype_id']; ?>" selected ><?php echo $vtype[0]['type']; ?></option>
                          <?php foreach($venuetypes as $venuetype){ ?>
                          <option value="<?php echo $venuetype['id']; ?>" ><?php echo $venuetype['type']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div> 
                    <div class="row form-group">
                      <div class="col-md-6">
                      <label> Fax Number </label><br>
                        <input type="text" name="fax" class="form-control" value="<?php echo $res[0]['fax_number']; ?>" data-inputmask='"mask": "(999) 999-9999"' data-mask />
                      </div>  
                      <div class="col-md-6">
                      <label> Contact Number </label><br>
		<input type="text" class="form-control" name="contact" value="<?php echo $res[0]['mobile']; ?>" data-inputmask='"mask": "(999) 999-9999"' data-mask required/>
                       <!--  <input type="text" name="contact" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="<?php echo $res[0]['mobile']; ?>" 		placeholder="Contact Number" required/> -->
                      </div>
                    </div>
                     <div class="row form-group">
                      <div class="col-md-6">
                      <label> Square Foot </label><br>
                        <input type="text" name="square_foot" class="form-control" value="<?php echo $res[0]['sq_footage']; ?>" placeholder="Square Foot" />
                      </div>  
                      <div class="col-md-6">
                      <label> No of Awards </label><br>
                        <input type="text" name="awards" class="form-control"  value="<?php echo $res[0]['awards']; ?>"  placeholder="Awards" />
                      </div>
                    </div>
                     <div class="row form-group">
                      <div class="col-md-6">
                      <label> No of Tables </label><br>
                        <input type="text" name="tables" class="form-control" value="<?php echo $res[0]['tables']; ?>" placeholder="Number of Tables" />
                      </div>  
                      <div class="col-md-6">
                      <label> No of Seats </label><br>
                        <input type="text" name="seats" class="form-control" value="<?php echo $res[0]['seats']; ?>" placeholder="Number of Seats" />
                      </div>
                    </div>
                    
                    <div class="form-group">
                    <label> Parking Information </label><br>
                   <input type="text" name="parking" class="form-control" value="<?php echo $res[0]['parking_information']; ?>" placeholder="Parking Information" />
                    </div> 
                    
                    <div class="row form-group">
                      
                      <div class="col-md-4">
                        <label> Days</label>
                      <!-- <input type='text' value="<?php foreach($d as $key=>$value){	if($d[$key])
	echo $m[$key];	}	?>"readonly> -->
	<br>
                        <select id="chktime" multiple="multiple" name="days[]">
						
                         <option value="1" <?php if($d[0]) echo 'selected'; ?> >Monday</option>
                         <option value="2" <?php if($d[1]) echo 'selected'; ?>>Tuesday</option>
                         <option value="3" <?php if($d[2]) echo 'selected'; ?>>Wednesday</option>
                         <option value="4" <?php if($d[3]) echo 'selected'; ?>>Thursday</option>
                         <option value="5" <?php if($d[4]) echo 'selected'; ?>>Friday</option>
                         <option value="6" <?php if($d[5]) echo 'selected'; ?>>Saturday</option>
                         <option value="7"<?php if($d[6]) echo 'selected'; ?>>Sunday</option>
						
                       </select>
                         <!-- <input type="button" id="btnget" value="Get Selected Values" /> -->
                     </div>
                  
                     <div class="col-md-4">
                      <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                            <label>Open Time:</label>
                                            <div class="input-group">
                                                <input type="text" name="start_time" value="<?php echo $hop[0]['start_time'];?>" class="form-control timepicker"/>
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
                                                <input type="text" name="end_time" value="<?php echo $hop[0]['end_time'];?>" class="form-control timepicker"/>
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
                  <img src="<?php echo $obj->getImagePath($picture[0]['url']) ?>" class="def-image" style="width: 100%;height: auto; padding: 3px;border: 1px solid rgb(213, 206, 206);
                  border-radius: 4px;">
                    <input type="file" name="image" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
					 <p class="text-yellow"> Image should be in the ratio of (3:2) </p>
                </div>
              </div>
              <div class="footer" style="width: 20%;margin-left: auto;margin-right: auto;">                                                               
                <button type="submit" class="btn bg-olive btn-block" onclick="return 0;">Submit</button>
                 <a class="btn bg-red btn-block" href="view_venue.php"> Cancel</a>
              </div>
              <!-- hidden -->
              <input type="hidden" name="token" value="<?php echo $key; ?>">
              <input type="hidden" name="mgid" value="<?php echo $mid; ?>">
              <input type="hidden" name="venue_id" value="<?php echo $vid; ?>">
              <input type="hidden" name="event" value="edit-venue">
              <input type="hidden" name="redirect" value="view_venue.php">

              
            </form>  
            
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
      <script src="../assets/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
      
      <!-- iCheck -->
      <script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

      <!-- AdminLTE App -->
      <script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script>
      
      <!-- DATA TABES SCRIPT -->
      <script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
      <script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
      <script src="../assets/js/bootstrap-multiselect.js" type="text/javascript"></script>
   <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>
   	          <!-- InputMask -->
        <script src="../assets/js/jquery.inputmask.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
        <script src="../assets/js/jquery.inputmask.extensions.js" type="text/javascript"></script>
      <script type="text/javascript">
       $(document).ready(function() {
	   $("[data-mask]").inputmask();
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

                 this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100 %').height('auto');
               }
               reader.readAsDataURL(files[0]);
             });

    </script> 
  </body>
  </html>
  