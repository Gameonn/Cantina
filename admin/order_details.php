<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php 
$oid=$_REQUEST['order_id'];
  $sql="SELECT `order`.*,TRUNCATE((`order`.order_amount),2) as order_amount,staff_order.status,staff_order.*,`order`.created_on as oc,DATE_FORMAT(`order`.created_on,'%d-%m-%Y') as open_time, `order`.id as oid,venue.id as vid,venue.*,venue.mobile as v_mobile,(select pictures.url from pictures where pictures.venue_id=venue.id) as url, users.*,
	CASE 
                  WHEN DATEDIFF(NOW(),`order`.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),`order`.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),`order`.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),`order`.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),`order`.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),`order`.created_on)) ,' s ago')
                END as time_elapsed
 FROM `order` join venue on venue.id=order.venue_id join users on users.id=`order`.user_id left join staff_order on staff_order.order_id=`order`.id where `order`.id=:order_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue('order_id',$oid);
  try{$sth->execute();}catch(Exception $e){}
  $res=$sth->fetchAll();
  $obj= json_decode($res[0]['bill_file'],true);
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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type="text/css">
      <body style="height: initial;">
        <div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Left side column. contains the logo and sidebar -->
          <?php require_once "../php_include/admin_leftmenu.php"; ?>
          <!-- right-side -->
           <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Receipt for Order
                        <small>#P<?php echo $oid; ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li><a href="orders.php"><i class="fa fa-tasks"></i>Orders</a></li>
                        <li class="active"><i class="fa fa-list-alt"></i> Order Details</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header" style="font-family: 'Kaushan Script', cursive;">
                              <!--  <i class="fa fa-globe"></i>Gambay -->
                              <img src="../uploads/4575Logo.png"> 
                               <!--  <small class="pull-right">Date: <?php echo $res[0]['open_time']; ?></small> -->
                            </h2>
                        </div><!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Venue 
                            <address>
                                <strong><?php echo $res[0]['venue_name']; ?></strong><br>
                                <?php echo $res[0]['address']; ?> <?php echo $res[0]['city']; ?><br>
                                <b>Phone:</b> <?php echo $res[0]['v_mobile']; ?><br/>
                                <b>Email:</b> <?php echo $res[0]['contact_email']; ?> <br/>
                                
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            User 
                            <address>
                                <strong><?php echo $res[0]['username']; ?></strong><br>
                                <?php  if($res[0]['dob']!='0000-00-00') 
                               echo  '<i class="fa fa-birthday-cake"></i>'. $res[0]["dob"].'<br>'; ?>
                                 
                                <b>Phone:</b> <?php echo $res[0]['mobile']; ?><br/>
                                <b>Email:</b> <?php echo $res[0]['email']; ?>
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                          <!--  <b>Invoice #<?php echo $oid; ?></b><br/>
                            <br/>
                            <b>Order ID:</b> P#<?php echo $oid; ?><br/> 
                            <b>Staus:</b> <?php if($res[0]['status']==1) echo 'Open'; elseif($res[0]['status']==2) echo 'Ready'; elseif($res[0]['status']==3) echo 'Closed'; elseif($res[0]['status']==4) echo 'Void';?> -->  
                               
                            <?php if($res[0]['status']==1){
                            echo 'Order Time: '. $res[0]['oc'].'<br>'; 
                            }
                            elseif($res[0]['status']==2){
                            echo 'Order Time: '. $res[0]['oc'].'<br>';
                            echo 'Ready Time: '. $res[0]['ready_time'];
                            }
                            elseif($res[0]['status']==3){
                            echo 'Order Time: '. $res[0]['oc'].'<br>';
                            echo 'Ready Time: '. $res[0]['ready_time'].'<br>';
                            echo 'Closed Time: '. $res[0]['closed_time'];
                            }
                            elseif($res[0]['status']==4){
                            echo 'Order Time: '. $res[0]['oc'].'<br>';
                            echo 'Void Time: '. $res[0]['void_time'];
                            }
                            ?>
                                  
                            <br/>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Qty</th>
                                        <th>Product</th>
                                        <th>Serial #</th>
                                        <th>Serving Type</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php    if(is_array($obj)){	
			foreach($obj as $k=>$v){
			
			
			if(is_array($v)){
			foreach($v as $key1=>$val){ 
			$px=$val['item_price'];
			$prx+=$px;
			?>
                                    <tr>
                                        <td><?php echo $val['quantity']; ?></td>
                                        <td><?php echo $val['item_name']; ?></td>
                                        <td><?php echo $val['item_id']; ?></td>
                                        <td><?php echo $val['serving_name']; ?></td>
                                        <td><?php echo $val['item_price']; ?></td>
                                    </tr>
                                   <?php  }}	
					}} ?> 
                                </tbody>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <div class="row">
                        
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
									<th> Subtotal:</th>
									<td><?php  echo $prx; ?></td>
									 </tr>
                                     <tr>
                                        <th>Tax: </th>
                                        <td><?php  $p=$obj['total_tax']?$obj['total_tax']:0;
                                        echo $p;
                                         ?></td>
                                    </tr>
                                     <tr>
                                        <th>Coupon: </th>
                                        <td><?php  $p=$obj['coupon_code']?$obj['coupon_code']:0;
                                        echo $p;
                                         ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tip: </th>
                                        <td><?php  $k=$obj['tip']?$obj['tip']:0;
                                        echo $k;
                                         ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total: </th>
                                        <td><?php echo $res[0]['order_amount']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print" style="text-align:center;">
                        <div class="col-xs-12">
                            <button class="btn btn-primary" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                            
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

          </body>
          </html>