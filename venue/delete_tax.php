  <!-- Delete Tax -->
  <?php session_start();?>
  <?php require_once "../php_include/db_connection.php"; ?>
  <?php require_once "../php_include/manager_header.php"; ?>
  <?php require_once "../GeneralFunctions.php"; ?>
  
  <?php
  $menuid=$_REQUEST['menu_id'];
  
  function check_tax($var){
    global $conn;
    $menuid=$_REQUEST['menu_id'];
    $sql="SELECT * FROM `tax` where id IN (select tax_id from category_tax where menucategory_id=:menucategory_id and is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    try{$sth->execute();}catch(Exception $e){ }
    $taxes=$sth->fetchAll();
    return $taxes;
    
  }


  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Taxes</title>
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
  <style>
    .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
      text-align: center; vertical-align: middle; }
    </style>
    <body>
      <div class="wrapper row-offcanvas row-offcanvas-left">
        <!-- Left side column. contains the logo and sidebar -->
        <?php require_once "../php_include/manager_leftmenu.php"; ?>
        <!-- right-side -->
        <aside class="right-side">                
          <!-- Content Header (Page header) -->
          
          <?php $a=check_tax($menuid); ?>
           <section class="content-header">
                    <h1>
                        Delete Tax
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                         <li><a href="menu_items.php"><i class="fa fa-suitcase"></i> Menu Items</a></li>
                          <li><a href="#"><i class="fa fa-eraser"></i> Delete Tax</a></li>
                    </ol>
                </section>
          
          <!-- Main content -->
          <section class="content" style="text-align:center;">
           <?php  if(count($a)){?>
            <form action="functions.php" method="post" enctype="multipart/form-data">
             <div class=" form-group">
              <?php foreach($a as $row){ ?>
              
              <div class="col-md-1" style="margin-top: 8px;">
                <input type="checkbox" name="tax_id[]" value="<?php echo $row['id']; ?>" class="flat-red" />
              </div>
              <div class="col-md-8">
                <input  name="tax_name" class="form-control" value="<?php echo $row['tax_name']; ?>" readonly/>
              </div>
              
              <div class="col-md-3">
                <input  name="percentage" class="form-control" value="<?php echo $row['percentage'].'%'; ?>" readonly/>
              </div>
              <?php } ?>
              
            </div>      
            <div class="row form-group">
              <div class="col-md-12">
               
                <input type="hidden" name="event" value="delete-tax">
                <input type='hidden' name="menucategory_id" value="<?php echo $menuid; ?>">
              </div>  
            </div> 
            
            <div class="footer">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>

          </form>

	<?php } else{?>
	<div style="  height: 200px;width: 200px; background: #eee; display: inline-block; font-size: xx-large; text-transform: uppercase; padding: 20px;">
	No Tax added so far
	</div>
	<?php }?>
          


          
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
  </body>
  </html>
  <script>
              //Flat red color scheme for iCheck
              $(document).ready(function() {
                $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                  checkboxClass: 'icheckbox_flat-red',
                  radioClass: 'iradio_flat-red'
                });
              });
            </script>
            
            <script>
             $(".modalBtn").click(function(){
              var subcatid= $(this).data('subcatid');
              $(".modal-body #subcatid").val(subcatid);
            });
             
           </script>