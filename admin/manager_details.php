<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php

  $sql="SELECT manager.*,venue.* from manager join manager_venue on manager_venue.manager_id=manager.id join venue on venue.id=manager_venue.venue_id where manager.is_deleted=0";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){}
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
                Managers <a href="manager_login.php" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="Manager Login"><i class="fa fa-sign-in"></i></a>
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
              <div class="container col-xs-12">
              
              <div class="col-xs-12">
                            <div class="box">
                                 <div class="box-body table-responsive no-padding">
                                    <table id="example2" class="table table-bordered table-hover">
                                   
                                        <tr>
                                            <th> </th> 
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Password</th>
                                            <th>Venue Name</th>
                                            <th>Manager Contact Number</th>
                                            <th>Venue Contact Number</th>
                                           
                                        </tr>
                                        <?php  $r=1;
                                        foreach($data as $row){ ?> 
                                        <tr>
                                            <td><img src="<?php echo BASE_PATH; ?>/uploads/<?php echo $row['pic']; ?>" style="width:50px;" ></td>
                                            <td><?php echo $r; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['email'];?></td>
                                            <td><?php echo $row['password']; ?></td>
                                            <td><?php echo $row['venue_name']; ?></td>
                                            <td><?php echo $row['mobile_number']; ?></td>
                                            <td><?php echo $row['mobile']; ?></td>
                                            
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
$('[data-toggle="tooltip"]').tooltip()
</script>
          </body>
          </html>