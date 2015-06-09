<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; 
require_once('../stripe_lib/stripe/init.php');
require_once('../stripe_lib/stripe/lib/Stripe.php');
?>

<?php
  $sql="SELECT venue.*,venue.id as vid,(select stripe_connect.stripe_id from stripe_connect where stripe_connect.venue_id=venue.id) as stripe_link,(select gambay_charge.charge from gambay_charge where gambay_charge.venue_id=venue.id) as gambay_fee,manager_venue.*,manager.*,manager.username as mn_username,manager.mobile_number as mn_mobile,(select count(`order`.id) from `order` where `order`.`venue_id`=venue.id) as order_count
  FROM `venue` join manager_venue on manager_venue.venue_id=venue.id and manager_venue.is_deleted=0 and manager_venue.is_live=1 join manager on manager.id=manager_venue.manager_id and manager.is_deleted=0 where venue.is_live=1 and venue.is_deleted=0  order by `venue`.venue_name ASC";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}
  catch(Exception $e){}
  $data=$sth->fetchAll();
   ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Venues</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Category Block Design -->
    <link rel="stylesheet" href="../assets/css/my.css" type="text/css" />  
  </head>
  <style>
    .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
      text-align: center; vertical-align: middle; }
	  .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
  padding: 2px;}
      .fa {
        line-height: 1.5;}
      </style>
      <body style="height: initial;">
        <div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Left side column. contains the logo and sidebar -->
          <?php require_once "../php_include/admin_leftmenu.php"; ?>
          <!-- right-side -->
          <aside class="right-side">                
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <h1>
                Venues
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-building-o"></i> Venues</a></li>
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
             
<div class="container col-xs-12" style="  max-width: 1400px;  overflow-x: auto;">
              
              <div class="col-xs-12">
                            <div class="">
                                 <div class="box-body table-responsive no-padding">
                                    <table id="example2" class="table table-bordered table-hover">
                                   
                                        <tr>
                                            <th>ID</th>
                                            <th>Venue Name</th>
                                            <th>Venue Address</th>
                                            <th>Venue Contact Email</th>
                                            <th>Manager Name</th>
                                            <th>Manager Email</th>
                                            <th>Manager Contact Number</th>
                                            <th>Venue Contact Number</th>
											<th>Gambay Fee <br>(In Cents)</th>
											<th> Update</th>
											<th>Stripe Link </th>
                                            <th> </th>
                                        </tr>
                                        <?php  $r=1;
                                        foreach($data as $row){ ?> 
                                        <tr>
                                            <td><?php echo $r; ?></td>
                                            <td><a href="venue_profile.php?venue_id=<?php echo $row['vid']; ?> "><?php echo $row['venue_name']; ?></a></td>
                                            <td><?php echo $row['address'].', '. $row['city']; ?></td>
                                            <td><?php echo $row['contact_email'];?></td>
                                            <td><?php echo $row['mn_username']; ?></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $row['mn_mobile']; ?></td>
                                            <td><?php echo $row['mobile']; ?></td>
											<td>
											 <?php if(!$row['gambay_fee']){
												echo '-'; } 
												else{ echo $row['gambay_fee'];} ?>
											 </td>
											 <td>
											 <?php if(!$row['gambay_fee']){  ?>
											 <a class="btn btn-warning btn-sm modalBtn-X" data-toggle="modal" data-vid="<?php echo $row['vid']; ?>"   data-vname="<?php echo $row['venue_name']; ?>" data-target="#gambay_fee">
												Set<br> Gambay<br> Fee
											</a>
											 <?php } else{  ?>
											<a class="btn btn-warning btn-sm modalBtn-X" data-toggle="modal" data-vid="<?php echo $row['vid']; ?>"   data-vname="<?php echo $row['venue_name']; ?>" data-target="#gambay_fee">
												Update<br> Gambay<br> Fee
											</a>
											<?php } ?>
											 </td>
											   <td>
											 <?php if(!$row['stripe_link']){  ?>
											 <a class="btn btn-primary btn-sm modalBtn" data-toggle="modal" data-vid="<?php echo $row['vid']; ?>" data-target="#stripe_account">
												Add<br> Stripe<br> Account
											</a>
											 <?php } else{ ?>
											  <a class="btn btn-danger btn-sm modalBtn" data-toggle="modal" data-vid="<?php echo $row['vid']; ?>" data-target="#stripe_account">
												Update <br> Stripe<br> Account
											</a>
											 <?php } ?>
											 </td>
                                             <td>
											 <?php if($row['order_count']){  ?>
											 <a href="orders.php?vid=<?php echo $row['vid']; ?>" class="btn btn-primary btn-xs"> Orders </a>
											 <?php } else{ echo "No Orders";} ?>
											 </td>
                                        </tr>
                                        <?php
                                        $r++;
                                         } ?>
                                     </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
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

<!-- AdminLTE App -->
<script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script>
<!-- Category Block effect -->
<script src="../assets/js/my.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
   <script src="../assets/js/jquery.custom-scrollbar.js" type="text/javascript"></script>

<?php require_once "stripe_form.php"; ?>
<?php require_once "gambay_fee_form.php"; ?>
   <script>
   $(".modalBtn").click(function(){
  var vid= $(this).data('vid');
  
    $(".modal-body #vid").val(vid);
});
 
   </script>  
   
   <script>
      $(".modalBtn-X").click(function(){
  var vid1= $(this).data('vid');
    var vname= $(this).data('vname');
    $(".modal-body #vid").val(vid1);
	 $(".modal-body #vname").val(vname);
});
   </script>

   </body>
          </html>