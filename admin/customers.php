<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>

<?php

  
$sql="select count(*) as user_count from (SELECT users.* FROM `order` join users on users.id=`order`.user_id and users.is_deleted=0 group by username) as temp";
$sth=$conn->prepare($sql);
try{$sth->execute();}catch(Exception $e){}
$mer=$sth->fetchAll();
$user_count=$mer[0]['user_count'];

$sql="SELECT users.*,users.id as uid,favorites.id as fid,venue.venue_name FROM `order` join users on users.id=`order`.user_id and users.is_deleted=0 join venue on venue.id=`order`.`venue_id` left join favorites on favorites.user_id=users.id and favorites.venue_id=`order`.venue_id group by username";
$sth=$conn->prepare($sql);
try{$sth->execute();}catch(Exception $e){}
$data=$sth->fetchAll();

$sql="SELECT * FROM (
		SELECT users . * , users.id AS uid, favorites.id AS fid, venue.venue_name, DATE_FORMAT(  `order`.created_on,  '%Y-%m-%d' ) AS dd
		FROM  `order` 
		JOIN users ON users.id =  `order`.user_id
		AND users.is_deleted =0
		JOIN venue ON venue.id =  `order`.`venue_id` 
		LEFT JOIN favorites ON favorites.user_id = users.id
		AND favorites.venue_id =  `order`.venue_id
		ORDER BY  `order`.created_on DESC
		) AS temp
		GROUP BY temp.username
		";
$sth=$conn->prepare($sql);
try{$sth->execute();}catch(Exception $e){}
$data1=$sth->fetchAll();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Customers</title>
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
                Customers
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-user-md"></i> Customers</a></li>
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
              <?php if($user_count){ ?>
                            <div class="box">
                                 <div class="box-body table-responsive no-padding">
                                    <table id="example2" class="table table-bordered table-hover">
                                   
                                        <tr>
                                            <th> </th>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>DOB</th>
                                            <th>Zipcode</th>
                                            <th>Mobile Number</th>
                                              <th> Last Venue Visited</th>
                                              <th> Date last venue visited</th>
											  <th> </th>
                                              <th> </th>
                                            <!--<th> Favorite</th> -->
                                        </tr>
                                        <?php  $r=1;
                                        foreach($data1 as $row){ 
                                        if($row['dob']=='0000-00-00')
                                        $d='N/A';
                                        else
                                        $d=$row['dob'];
                                        
                                        ?> 
                                        <tr>
                                            <td ><img src="<?php echo BASE_PATH; ?>/uploads/<?php echo $row['image']; ?>" style="width:50px;"></td>
                                            <td ><?php echo $r; ?></td>
                                            <td style='text-transform:capitalize;'><a href="orders.php?uid=<?php echo $row['uid']; ?>"><?php echo $row['username']; ?></a></td>
                                            <td><?php echo $row['email']; ?></td>
                                            <td><?php echo $d; ?></td>
                                            <td><?php echo $row['zipcode'];?></td>
                                            <td><?php echo $row['mobile']; ?></td>
                                             <td><?php echo  $row['venue_name'];?></td>
                                             <td><?php echo  $row['dd'];?></td>
											<td><div class="btn btn-danger btn-xs" type="submit"><a href="#myModal2" class="evtdltsnd" 
											data-vid="<?php echo $row['uid'];?>" data-toggle="modal" data-target="#myModal2"  style="color:white" >
											<i class="fa fa-trash-o "></i></a></div>
										  </td>
                                             <td><a href="orders.php?uid=<?php echo $row['uid']; ?>" class="btn btn-primary btn-sm"> Orders </a></td>
                                       <!--   <td>  <?php if($row['fid']) echo '<span class="label label-success"><i class="fa fa-check"></i></span>'; else echo '<span class="label label-danger"><i class="fa fa-remove"></i></span>'; ?></td> -->
                                        </tr>
                                        <?php
                                        $r++;
                                         } ?>
                                     </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <?php }else{
                            
	                   echo '<div style=" border: 1px solid #D5CCCC;font-size: 40px;text-align: center; height: 200px;">
	  	<div class="mac" style="position: relative;  top: 50%; transform: translateY(-50%); text-transform: capitalize;">        No Customers so far </div>     
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
			
			 <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1em;">
            <div class="modal-header" style="background-color:#dd4b39; border-top-left-radius: 1em;
                        border-top-right-radius: 1em;">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel" style="color:white;">Do You Really Want To Delete The User ?</h4>
            </div>
			<form action="eventHandler.php" method="post"> 
        <div class="modal-body" >
		<h4>
		  Deleting it will remove all related data
		</h4>
		<input type="hidden" name="event" value="delete-customer">
		<input type="hidden" name="user_id" id="vid" value=0>
               <div id="inside2" style="text-align:right;">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Save Changes</button>    
               </div>
        </div>
		</form>
        </div>
    </div> 
</div>
          </body>
          </html>

   <script>
   $(".evtdltsnd").click(function(){
  var subcatid= $(this).data('vid');
    $(".modal-body #vid").val(subcatid);
});
 
   </script>