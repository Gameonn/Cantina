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
  $rx=$sth->fetchAll();
  $vid=$rx[0]['venue_id'];
  

  $sql="SELECT `order`.id as oid,`order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.*,staff_order.created_on as st_created_on,staff.*,staff.username as st_user,staff.email as st_email,staff.mobile as st_mobile,users.*,users.username as u_username,users.email as u_email
  FROM `order` join `staff_order` on staff_order.order_id=`order`.id and staff_order.venue_id=`order`.venue_id join staff on staff.id=staff_order.staff_id join users on users.id=`order`.user_id where `order`.venue_id=:venue_id order by `order`.created_on DESC ";
  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){}
  $data=$sth->fetchAll();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Orders</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Category Block Design -->
    <link rel="stylesheet" href="../assets/css/my.css" type="text/css" />  
  </head>
  <style>
    .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
      text-align: center; vertical-align: middle; }
      .fa {
        line-height: 1.5;}
      </style>
      <body style="overflow-y: hidden;">
        <div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Left side column. contains the logo and sidebar -->
          <?php require_once "../php_include/manager_leftmenu.php"; ?>
          <!-- right-side -->
          <aside class="right-side">                
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <h1>
                Orders
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-gavel"></i> Orders</a></li>
              </ol>
            </section>

            <!-- Main content -->
            <section class="content">
              <?php //error div
              if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
              <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $_REQUEST['msg']; ?>
              </div>
              <?php } // --./ error -- ?>
              <div class="container">
              
              <div class="col-xs-12">
              <?php if(count($data)){ ?>
                            <div class="box">
                                 <div class="box-body table-responsive no-padding">
                                    <table id="example2" class="table table-bordered table-hover">
                                   
                                        <tr>
                                            <th>ID</th>
											<th>User Name </th>
											<th>User Email </th>
                                            <th>Staff username</th>
                                            <th>Staff Mobile</th>
                                            <th>Order Amount</th>
                                            <th>Delivery Type</th>
                                            <th>Status</th>
											<th> </th>
                                        </tr>
                                        <?php  $r=1;
                                        foreach($data as $row){ ?> 
                                        <tr>
                                  <td><?php echo $r; ?></td>
								   <td><?php echo $row['u_username']; ?></td>
								     <td><?php echo $row['u_email']; ?></td>
                                  <td><?php echo $row['st_user']; ?></td>
                                  <td><?php echo $row['st_mobile']; ?></td>
                                  <td><?php echo $row['order_amount'];?></td>
                                  <td><?php echo $row['delivery_type']; ?></td>
                                  <td><?php if($row['status']==1) echo 'Open'; elseif($row['status']==2) echo 'Ready'; elseif($row['status']==3) echo 'Closed'; else echo 'Void';  ?></td>
								    <td><a href="order_details.php?order_id=<?php echo $row['oid']; ?>" class="btn btn-primary btn-sm"> Order<br> Details </a></td>
                                        </tr>
                                        <?php
                                        $r++;
                                         } ?>
                                     </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <?php }else{
                            
	                   echo '<div style=" border: 1px solid #D5CCCC;font-size: 40px;text-align: center; height: 200px;">
	  	<div class="mac" style="position: relative;  top: 50%; transform: translateY(-50%); text-transform: capitalize;">        No Orders so far </div>     
		  </div>';
		  } ?>
                        </div>
                </div>

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
<!-- Category Block effect -->
<script src="../assets/js/my.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
   <script src="../assets/js/jquery.custom-scrollbar.js" type="text/javascript"></script>
<script>
    $(function() {
    
                $('#example2').dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
               });
          
              //Flat red color scheme for iCheck
              $(document).ready(function() {
                $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                  checkboxClass: 'icheckbox_flat-red',
                  radioClass: 'iradio_flat-red'
                });

              });
            </script>
          </body>
          </html>