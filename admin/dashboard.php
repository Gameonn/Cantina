<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>

<?php 
$sql="select count(*) as user_count from (SELECT users.* FROM `order` join users on users.id=`order`.user_id and users.is_deleted=0 group by username) as temp";
$sth=$conn->prepare($sql);
try{$sth->execute();}catch(Exception $e){}
$mer=$sth->fetchAll();

$customers=$mer[0]['user_count']?$mer[0]['user_count']:"No";

$sql="SELECT count(venue.id) as venue_count
  FROM `venue` join manager_venue on manager_venue.venue_id=venue.id and manager_venue.is_deleted=0 and manager_venue.is_live=1 join manager on manager.id=manager_venue.manager_id and manager.is_deleted=0 where venue.is_live=1 and venue.is_deleted=0  order by `venue`.venue_name ASC";
$sth=$conn->prepare($sql);
try{$sth->execute();}catch(Exception $e){}
$venue=$sth->fetchAll();

$ct=$venue[0]['venue_count']?$venue[0]['venue_count']:"No";
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
<body>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <?php require_once "../php_include/admin_leftmenu.php"; ?>
			<!-- right-side -->
            <aside class="right-side">                
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        
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
					
  <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                      <?php echo $ct; ?>
                                    </h3>
                                    <p>
                                        Venues
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="venues.php" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                     <?php echo $customers; ?>
                                    </h3>
                                    <p>
                                        Customers
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-help-buoy"></i>
                                </div>
                                <a href="customers.php" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                      
                     
                    </div><!-- /.row -->

				
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
		
    </body>
</html>
       