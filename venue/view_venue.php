<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php";
require_once "../php_include/db_connection.php";
require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php'); 
?>

<?php
    $obj = new GeneralFunctions; 
    $sql="SELECT id from manager where token=:token and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("token",$key);
    try{$sth->execute();}catch(Exception $e){}
    $mgid=$sth->fetchAll();
    $mid=$mgid[0]['id'];

    $sql="SELECT venue_id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("manager_id",$mid);
    try{$sth->execute();}catch(Exception $e){}
    $r=$sth->fetchAll();
    $vid=$r[0]['venue_id'];

    $sql="SELECT * from staff where venue_id=:venue_id ";
    $sth=$conn->prepare($sql);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();}catch(Exception $e){}
    $staff=$sth->fetchAll();

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
	
	$sql="SELECT * from stripe_connect where venue_id=:vid";
    $sth=$conn->prepare($sql);
    $sth->bindValue("vid",$vid);
    try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
    $s_res=$sth->fetchAll();
    $acc_id=$s_res[0]['stripe_id'];
	$is_linked=$s_res[0]['is_linked'];

	  $stripe = array(
    'secret_key'      => 'sk_test_gRNo5PZ1TVjxRv9crUhw92lu',
    'publishable_key' => 'pk_test_lsE6QUL6t2mDTJ64S9EXL5Nh'
    );
 \Stripe\Stripe::setApiKey($stripe['secret_key']);
	
   if($_POST){
   \Stripe\Stripe::setApiKey($stripe['secret_key']);
  
    if($acc_id){
	$account = \Stripe\Account::retrieve($acc_id);
	$account->bank_account = $_POST['stripeToken'];
	$account->save();
   if($account){
   $sth=$conn->prepare('insert into payment values(DEFAULT,:json)');
  $sth->bindValue('json',$account);
  try{$sth->execute();}
  catch(Exception $e){
  echo $e->getMessage();
  }
   
  $sth=$conn->prepare('update stripe_connect set is_linked=1 where venue_id=:vid');
  $sth->bindValue('vid',$vid);
  try{$sth->execute();}
  catch(Exception $e){}
  }}
   } 
?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Gambay| Dashboard</title>
      <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
      <link href="../assets/css/flexslider.css" rel="stylesheet" type="text/css" />

    </head>
    <style>
      .modal-title {text-align: center!important;}
      .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
        text-align: center; vertical-align: middle; }

        .venue-div{display: block; padding: 9.5px;margin: 0 0 10px;font-size: 18px;line-height: 1.428571429;color: #333;word-break: break-all;word-wrap: break-word;
          background-color: #f5f5f5;border: 1px solid #ccc;border-radius: 4px;  }
	.nav-tabs-custom {margin-bottom: 20px;background: transparent;box-shadow: none;}
	.nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus {
	color: #fff!important;background-color: #428bca!important;border-radius: 4px;}
          .v-details{display: inline-block; width: 100%; background: rgba(218, 218, 218, 0.53);}
        </style>
        <body style="height:initial;">
          <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <?php require_once "../php_include/manager_leftmenu.php"; ?>
            <!-- right-side -->
            <aside class="right-side">                
              <!-- Content Header (Page header) -->
              <section class="content-header" style="display: -webkit-box;">
                <h1>
                 <?php echo $res[0]['venue_name']; ?>
               </h1>
               <div class="edit-venue" style="font-size: x-large;font-family: 'open sans';font-weight: 100;margin-left: 10px;">
                <a href="edit_venue.php" class="venueedit fa fa-pencil-square-o" style='text-decoration: none;' ></a>
              </div> 
			  <?php if(!$is_linked){ ?>
			   <a class="btn btn-primary btn-sm modalBtn" style="margin-left: 10px;" data-toggle="modal"  data-target="#link_account">
				Link Your Account
			   </a>
			   <?php } else{ ?>
			   <a class="btn btn-warning btn-sm" style="margin-left: 10px;cursor:auto;">Account Linked</a>
			    <a class="btn btn-primary btn-sm modalBtn" style="margin-left: 10px;" data-toggle="modal"  data-target="#send_email">
				Send Email
			   </a>
			   <?php } ?>
			  
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
                <li><a href="#"><i class="fa fa-foursquare"></i>My Venue</a></li>
              </ol>
            </section>

            <!-- Main content -->
            <section class="content" style="text-align:center;">

             <div class="nav-tabs-custom">
              <ul class="nav nav-pills">
                <li role="presentation" class="active"><a href="#tab_1" data-toggle="tab">Profile</a></li>
                <li role="presentation"><a href="#tab_2" data-toggle="tab">Manage Staff</a></li>
              </ul>
              <div class="tab-content" style='padding: 0px;'>
                <div class="tab-pane active" id="tab_1">
                 <div class="row col-xs-12" style='margin-top:10px;'>

                  <div class="col-md-4">
                    <img src="<?php echo $obj->getImagePath($picture[0]['url']) ?>" style="width:100%; height:auto;" >
                  </div>
                  <div class="col-md-8 venue-div">
                    <div class="v-details">
                      <div class="col-md-5">
                        Venue Address
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['address'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        City
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['city'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Zipcode
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['zipcode'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Mobile Number
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['mobile'];?>
                      </div>
                    </div>
                    
                     <div class="v-details">
                      <div class="col-md-5">
                        Contact Email
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['contact_email'];?>
                      </div>
                    </div>

 			<div class="v-details">
                      <div class="col-md-5">
                        Website Url
                      </div>

                      <div class="col-md-7">
                        <?php echo $res[0]['website'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Venue Type
                      </div>

                      <div class="col-md-7">
                        <?php echo $vtype[0]['type'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Store Open Days
                      </div>

                      <div class="col-md-7">
                        <?php 
                        foreach($d as $key=>$value){
                          if($d[$key])
                            echo $m[$key]." ";
                        }
                        ?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Open Time
                      </div>

                      <div class="col-md-7">
                        <?php echo $hop[0]['start_time'];?>
                      </div>
                    </div>

                    <div class="v-details">
                      <div class="col-md-5">
                        Close Time
                      </div>

                      <div class="col-md-7">
                        <?php echo $hop[0]['end_time'];?>
                      </div>
                    </div>

                  </div>

                </div> 
              </div><!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <div class="box" style="margin-top: 10px;">
                                <div class="box-header">
                                    <h3 class="box-title">Staff Details</h3>
                                  
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Status</th>
                                            <th> </th>
                                        </tr>
                                          
                                          <?php foreach($staff as $row){ ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['mobile']; ?></td>
                                             <td>  <?php if($row['is_live']) echo '<span class="label label-success"><i class="fa fa-check"></i></span>'; else echo '<span class="label label-danger"><i class="fa fa-remove"></i></span>'; ?></td>
                                            <td class='staff'>
                                            <a class="staffedit fa fa-pencil-square-o" vid="<?php echo $vid; ?>" staff_id="<?php echo $row['id']; ?>" data-toggle="modal" data-target="#editstaff" data-remote="true" data-no-turbolink ='true' style='text-decoration:none;' ></a>
              <a class="staffdelete fa fa-remove" vid="<?php echo $vid; ?>" staff_id="<?php echo $row['id']; ?>" style='text-decoration:none;'></a>
                                        
                                            </td>
                                         
                                        </tr>
                                            <?php } ?>
                                    
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            
            </div><!-- /.tab-pane -->
          </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->  


      </section><!-- /.content -->
    </aside><!-- /.right-side -->
  </div><!-- ./wrapper -->




  <!-- jQuery 2.0.2 -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
  <!-- jQuery UI 1.10.3 -->
  <script src="../assets/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
  <!-- Bootstrap -->
  <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
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
  <script src="../assets/js/jquery.flexslider.js" type="text/javascript"></script>

</body>
</html>
<script>
     $('.staff').on('click','.staffedit',function(){
  
    var vid= $(this).attr('vid');
    var staffid=$(this).attr('staff_id');
    var event='get-staff';
//alert(vid);
    $.post("functions.php",
    {
    event: event,
    vid: vid,
    staff_id: staffid
    },
    
    function(data){

    console.log(data);
$('#staff-editing1').empty();
     $.each(data, function(index,v) {
   
   // alert(value.name); // will alert each value
        var field= '<form action="functions.php" method="post" enctype="multipart/form-data"><div class="body "><div class="form-group"><label> Username </label><br><input type="text" name="username" class="form-control" value="'+ v.username +'"  readonly /></div><div class="form-group"><label> Email </label><br><input type="email" name="email" class="form-control"  value="'+ v.email +'" readonly /></div><div class="form-group"><label> Mobile Number </label><br> <input name="mobile" class="form-control" value="'+v.mobile+'"><input type="hidden" name="event" value="edit-staff"><input type="hidden" name="venue_id" value="'+vid+'"><input type="hidden" name="staff_id" value="'+v.id+'"></div></div><div class="footer" > <input type="submit" class="btn bg-olive btn-block" name="Submit"><a href="view_venue.php" class="btn bg-red btn-block" >Cancel</a></div></form>';

    $('#staff-editing1').append(field).fadeIn(1000);
    });
    
    },"json"
    );
   
    });
   
     $('.staff').on('click','.staffdelete',function(){
 
    var vid= $(this).attr('vid');
    var staffid=$(this).attr('staff_id');
    var event='delete-staff';

    $.post("functions.php",
    {
    event: event,
    vid: vid,
    staff_id: staffid
    },
    
    function(data){

    console.log(data);
    location.reload();
	//$('.all_coupons').empty();
	//$('.all_coupons').append('all_coupons.php').html();
   }
    );
   
    });   
     
   </script>
            <div class="modal fade" id="editstaff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="myModalLabel">Edit Staff Details</h4>
            </div>
            <div class="modal-body">
             <div class="" id="staff-editing1">

             </div>
           </div>
         </div>
       </div>
     </div>
		 
   <?php require_once "bank_form.php"; ?> 
   
   <?php require_once "email_admin.php"; ?> 