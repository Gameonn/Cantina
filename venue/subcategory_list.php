<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>
	
<?php
   $menuid=$_REQUEST['menu_id'];
	
  function check_subcategory($var){
  global $conn;
  $menuid=$_REQUEST['menu_id'];
    $sql="SELECT * from subcategory where id=:id IN (select parent_id from subcategory where menucategory_id=:menucategory_id) and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("id",$var);
  $sth->bindValue("menucategory_id",$menuid);
  try{$sth->execute();}catch(Exception $e){ }

  $s_id=$sth->fetchAll();
  return count($s_id);
 
  }
  
    function check_item($var){
  global $conn;
  $menuid=$_REQUEST['menu_id'];
    $sql="SELECT * from item where parent_id=:parent_id IN (select parent_id from item where menucategory_id=:menucategory_id) and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("parent_id",$var);
  $sth->bindValue("menucategory_id",$menuid);
  try{$sth->execute();}catch(Exception $e){ }

  $s_id=$sth->fetchAll();
  return count($s_id);
 
  }
  
  $obj = new GeneralFunctions; 
  $sql="SELECT id from manager where token=:token and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("token",$key);
  try{$sth->execute();}catch(Exception $e){}
  $mgid=$sth->fetchAll();
  $mid=$mgid[0]['id'];

  $sql="SELECT id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("manager_id",$mid);
  try{$sth->execute();}catch(Exception $e){}
  $r=$sth->fetchAll();
  $vid=$r[0]['id'];

 	
  $sql="SELECT * from subcategory where menucategory_id=:menucategory_id and parent_id=0 and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  $sth->bindValue("menucategory_id",$menuid);
  try{$sth->execute();}catch(Exception $e){}
  $subcategories=$sth->fetchAll();

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
        <section class="content-header">
                    <h1>
                       Subcategories
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
                         <li><a href="menu_items.php"><i class="fa fa-suitcase"></i>Menu Items</a></li>
                          <li><a href="#"><i class="fa fa-tags"></i>Subcategories</a></li>
                    </ol>
                </section>

        <!-- Main content -->
        <section class="content" style="text-align:center;">
         <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Sub Categories</h3>
              <div class="box-tools">
              
              </div>
            </div> 
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-bordered">
                <tbody><tr>
                  <th> </th>
                  <th>ID</th>
                  <th>Name</th>
                  <th> </th>
                </tr>

                <?php foreach($subcategories as $subcategory){
                
                $sub=check_subcategory($subcategory['id']);
           	$item=check_item($subcategory['id']);
                ?>
                
                <tr>
                  <td><img src="<?php echo $obj->getImagePath($subcategory['pic']) ?>" style="width: 70px;height: 60px;"></td>
                  <td><?php echo $subcategory['id'];?></td>
                  <td><?php echo $subcategory['name'];?></td>
                  <td> <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_categories"><i class="fa fa-edit"> </i></button> 
                  <?php if(!$item){?>
                  <button type="button" class="btn btn-primary btn-sm modalBtn" data-subcatid="<?php echo $subcategory['id'];?>" data-toggle="modal" data-target="#add_subcategories">Add Subcategory</button>
                  <?php }?>
                  <?php if(!$sub){?>
                      <button type="button" class="btn btn-primary btn-sm" data-catid="<?php echo $subcategory['id'];?>" data-toggle="modal" data-target="#add_subcategories">Add Item</button>
                      <?php }?>
                     <?php if($sub){?> 
                  <a href="" class="btn btn-primary btn-sm" ><i class="fa fa-plus"> </i></button>
                     <?php }?> 
                  </td>
                </tr>
                <?php } ?>

              </tbody></table>
            </div> 
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

<!-- DATA TABES SCRIPT -->
<script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
</body>
</html>



   <?php require_once "add_subcategory.php"; ?>
   
   <script>
   $(".modalBtn").click(function(){
  var subcatid= $(this).data('subcatid');
    $(".modal-body #subcatid").val(subcatid);
});
 
   </script>