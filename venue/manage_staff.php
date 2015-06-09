<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

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

?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Gambay| Venue Owner</title>
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
        <body>
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
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
                <li><a href="view_venue.php"><i class="fa fa-foursquare"></i>My Venue</a></li>
                <li><a href="#"><i class="fa fa-suitcase"></i>Staff Management</a></li>
              </ol>
            </section>

            <!-- Main content -->
            <section class="content" style="text-align:center;">

       <?php if(count($staff)){ ?>
     
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
                            
        		<?php }else{
                            
	                   echo '<div style=" border: 1px solid #D5CCCC;font-size: 40px;text-align: center; height: 200px;">
	  	<div class="mac" style="position: relative;  top: 50%; transform: translateY(-50%); text-transform: capitalize;">        No Staff added so far </div>     
		  </div>';
		  } ?>
 


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
        var field= '<form action="functions.php" method="post" enctype="multipart/form-data"><div class="body "><div class="form-group"><label> Username </label><br><input type="text" name="username" class="form-control" value="'+ v.username +'"  readonly /></div><div class="form-group"><label> Email </label><br><input type="email" name="email" class="form-control"  value="'+ v.email +'" readonly /></div><div class="form-group"><label> Mobile Number </label><br> <input name="mobile" class="form-control" value="'+v.mobile+'"><input type="hidden" name="event" value="edit-staff"><input type="hidden" name="venue_id" value="'+vid+'"><input type="hidden" name="staff_id" value="'+v.id+'"></div></div><div class="footer" > <input type="submit" class="btn bg-olive btn-block" name="Submit"><a href="manage_staff.php" class="btn bg-red btn-block" >Cancel</a></div></form>';

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