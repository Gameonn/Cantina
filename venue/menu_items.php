<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php
    //error_reporting(1);
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

  //level1 main-categories
  $sql="SELECT * from menucategory where menucategory.venue_id=:vid and is_live=1 and is_deleted=0 order by id DESC";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $menus=$sth->fetchAll();

  //level2 subcategories
  $sql="SELECT * from subcategory where subcategory.venue_id=:vid and subcategory.parent_id=0 and is_live=1 and is_deleted=0 order by id DESC";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $submenus=$sth->fetchAll();

  //level3 subcategories
  $sql="SELECT * from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and parent_id=0 and is_live=1 and is_deleted=0)";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $levelmenus=$sth->fetchAll();

    //level4 subcategories
  $sql="SELECT * from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and parent_id=0 and is_live=1 and is_deleted=0))";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $downmenus=$sth->fetchAll();

      //level5 subcategories
  $sql="SELECT * from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (select id from subcategory where parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and parent_id=0 and is_live=1 and is_deleted=0)))";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $maxmenus=$sth->fetchAll();

      //level6 subcategories
  $sql="SELECT * from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (select id from subcategory where parent_id IN (select id from subcategory where parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and is_live=1 and is_deleted=0 and subcategory.parent_id IN (SELECT id from subcategory where subcategory.venue_id=:vid and parent_id=0 and is_live=1 and is_deleted=0))))";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $dropmenus=$sth->fetchAll();


    //subcategories based on main categories
  function check_subcategory($var,$v2){
    global $conn;

    $sql="SELECT * from subcategory where menucategory_id=:menucategory_id and parent_id=:pid and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$var);
    $sth->bindValue("pid",$v2);
    try{$sth->execute();}catch(Exception $e){ }
    $submenus=$sth->fetchAll();
    return count($submenus);
  }

  function check_item($var,$v2){
    global $conn;
   $sql="SELECT * from item where menucategory_id=:menucategory_id and parent_id=:pid and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$var);
    $sth->bindValue("pid",$v2);
    try{$sth->execute();}catch(Exception $e){ }
    $items=$sth->fetchAll();
    return count($items);
  }

  
  $sql="SELECT menucategory.*,(select group_concat(tax.tax_name separator '<br>') from tax left join category_tax on category_tax.tax_id=tax.id where category_tax.menucategory_id=menucategory.id and category_tax.is_deleted=0) as tax_name,(select group_concat(servings.type separator ',') from servings) as serving from menucategory where menucategory.venue_id=:venue_id and menucategory.is_live=1 and menucategory.is_deleted=0";

  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
  $venuecategories=$sth->fetchAll();

  $sql="SELECT * from menucategory where venue_id=0 and is_live=1 and is_deleted=0";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){}
  $categories=$sth->fetchAll();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Menu Levels</title>
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
                Menu Levels
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-suitcase"></i> Menu Levels</a></li>
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
                <div class="row main-con" style="width:95%" vid='<?php echo $vid; ?>'>


                  <div class='col-md-3 well level-main' id='level-1' level='1' >
                    Level 1
                    <div class='parent' parent-id='0' >  </div>
                    <div class='input-group'>
                      <span class='input-group-addon is-item' level='1' is-item='no' ></span>
                      <input type='text' class='form-control'>
                      <span class='input-group-btn'>
                        <button class='add-cat btn btn-default' level='1' vid='<?php echo $vid; ?>' type='button'>Go!</button>
                      </span>
                    </div>
                    <div class='items-in'>
                      <ul class='nav nav-pills nav-stacked'>
                       <?php
                       $r=200;
                       $s=400;
                       foreach($menus as $menu){ ?>
                       <li class="field" category_id="<?php echo $menu['id']; ?>" level="1" has-item="<?php if(check_item($menu['id'],0)){ echo yes; } else{ echo no; } ?>" has-child="<?php if(check_subcategory($menu['id'],0)){ echo yes; } else{ echo no; } ?>" parent-id="<?php echo $menu['id']; ?>" field-id="<?php echo $r; ?>" is-item="no"><a href="#"><span class="field-name"><?php echo $menu['name']; ?></span>
                        <?php if(!check_subcategory($menu['id'],0) && (!check_item($menu['id'],0))){ 
                         echo "<span class='close'>x</span>";
                       } ?>
                     </a></li>
                     <?php
                     $r++;
                     $s++;
                   } ?>
                 </ul>
               </div>
             </div>

             <!-- level 2 subcategories -->
             <?php if(count($submenus)){ ?>
             <div class="col-md-3 well level-main" id="level-2" level="2">Level 2<div class="parent" parent-id=""></div>
             <div class="input-group"><span class="input-group-addon is-item" level="2" is-item="no"></span>
              <input type="text" class="form-control"><span class="input-group-btn"><button class="add-cat btn btn-default" level="2" type="button">Go!</button></span></div>
              <div class="items-in">
                <ul class="nav nav-pills nav-stacked">
                 <?php
                 $r=200;
                 $s=400;
                 foreach($submenus as $submenu){ ?>
                 <li class="field" subcategory_id="<?php echo $submenu['id']; ?>" category_id="<?php echo $submenu['menucategory_id']; ?>" level="2" has-item="<?php if(check_item($submenu['menucategory_id'],$submenu['id'])){ echo yes; } else{ echo no; } ?>" has-child="<?php if(check_subcategory($submenu['menucategory_id'],$submenu['id'])){ echo yes; } else{ echo no; } ?>" parent-id="<?php echo $submenu['menucategory_id']; ?>" field-id="<?php echo $s; ?>" is-item="no"><a href="#"><span class="field-name"><?php echo $submenu['name']; ?></span>
                  <?php if(!check_subcategory($submenu['menucategory_id'],$submenu['id']) && (!check_item($submenu['menucategory_id'],$submenu['id']))){  ?>
                  <span class="close">x</span>
                  <?php } ?>
                </a></li>
                <?php
                $r++;
                $s++;
              } ?>


            </li>  
          </ul></div></div>   
          <?php } ?>

            <!-- level 3 subcategories -->
          <?php if(count($levelmenus)){ ?> 
          <div class="col-md-3 well level-main" id="level-3" level="3">Level 3<div class="parent" cat-id="" subcat-id="" parent-id=""></div>
          <div class="input-group"><span class="input-group-addon is-item" level="3" is-item="no"></span>
            <input type="text" class="form-control"><span class="input-group-btn">
            <button class="add-cat btn btn-default" level="3" type="button">Go!</button></span></div>
            <div class="items-in"><ul class="nav nav-pills nav-stacked">
              <?php
              $t=600;
              
              foreach($levelmenus as $levelmenu){ ?>
              <li class="field" category_id="<?php echo $levelmenu['menucategory_id']; ?>" subcategory_id="<?php echo $levelmenu['id']; ?>" level="3" has-item="<?php if(check_item($levelmenu['menucategory_id'],$levelmenu['id'])){ echo yes; } else{ echo no; } ?>" 
                has-child="<?php if(check_subcategory($levelmenu['menucategory_id'],$levelmenu['id'])){ echo yes; } else{ echo no; } ?>" parent-id="<?php echo $levelmenu['parent_id']; ?>" field-id="<?php echo $t; ?>" is-item="no"><a href="#"><span class="field-name"><?php echo $levelmenu['name']; ?></span>
                <?php if(!check_subcategory($levelmenu['menucategory_id'],$levelmenu['id'])&&(!check_item($levelmenu['menucategory_id'],$levelmenu['id']))) { ?>
                <span class="close">x</span>
                <?php } ?>
              </a></li>
              <?php
              $t++;

            } ?>
          </ul></div></div>
          <?php } ?>

             <!-- level 4 subcategories -->
          <?php if(count($downmenus)){ ?> 
          <div class="col-md-3 well level-main" id="level-4" level="4">Level 4<div class="parent" cat-id="" subcat-id="" parent-id=""></div>
          <div class="input-group"><span class="input-group-addon is-item" level="4" is-item="no"></span>
           <input type="text" class="form-control"><span class="input-group-btn"><button class="add-cat btn btn-default" level="4" type="button">Go!</button></span></div><div class="items-in">
           <ul class="nav nav-pills nav-stacked">
            <?php
            $p=500;
            foreach($downmenus as $downmenu){ ?>
            <li class="field" category_id="<?php echo $downmenu['menucategory_id']; ?>" subcategory_id="<?php echo $downmenu['id']; ?>" level="4" has-item="<?php if(check_item($downmenu['menucategory_id'],$downmenu['id'])){ echo yes; } else{ echo no; } ?>"
              has-child="<?php if(check_subcategory($downmenu['menucategory_id'],$downmenu['id'])){ echo yes; } else{ echo no; } ?>" parent-id="<?php echo $downmenu['parent_id']; ?>" field-id="<?php echo $p; ?>" is-item="no"><a href="#"><span class="field-name"><?php echo $downmenu['name']; ?></span>
              <?php if(!check_subcategory($downmenu['menucategory_id'],$downmenu['id'])&&(!check_item($downmenu['menucategory_id'],$downmenu['id']))) { ?>
              <span class="close">x</span>
              <?php } ?>
            </a></li>
            <?php
            $p++;
          } ?>
        </ul></div></div>
        <?php } ?>

           <!-- level 5 subcategories -->
        <?php if(count($maxmenus)){ ?> 
        <div class="col-md-3 well level-main" id="level-5" level="5">Level 5<div class="parent" cat-id="" subcat-id="" parent-id=""></div>
        <div class="input-group"><span class="input-group-addon is-item" level="5" is-item="no"></span>
         <input type="text" class="form-control"><span class="input-group-btn"><button class="add-cat btn btn-default" level="5" type="button">Go!</button></span></div><div class="items-in">
         <ul class="nav nav-pills nav-stacked">
           <?php
           $kl=1100;
           foreach($maxmenus as $maxmenu){ ?>
           <li class="field" category_id="<?php echo $maxmenu['menucategory_id']; ?>" subcategory_id="<?php echo $maxmenu['id']; ?>" level="5" has-item="<?php if(check_item($maxmenu['menucategory_id'],$maxmenu['id'])){ echo yes; } else{ echo no; } ?>"
             has-child="<?php if(check_subcategory($maxmenu['menucategory_id'],$maxmenu['id'])){ echo yes; } else{ echo no; } ?>" parent-id="<?php echo $maxmenu['parent_id']; ?>" field-id='<?php echo $kl; ?>' is-item="no"><a href="#"><span class="field-name"><?php echo $maxmenu['name']; ?></span>
             <?php if(!check_subcategory($maxmenu['menucategory_id'],$maxmenu['id'])&&(!check_item($maxmenu['menucategory_id'],$maxmenu['id']))){  ?>   
             <span class="close">x</span>
             <?php } ?>
           </a></li>
           <?php
           $kl++;
         } ?>
       </ul></div></div>
       <?php } ?>

         <!-- level 6 subcategories -->
       <?php if(count($dropmenus)){ ?>
       <div class="col-md-3 well level-main" id="level-6" level="6">Level 6<div class="parent" cat-id="" subcat-id="" parent-id=""></div>
       <div class="input-group"><span class="input-group-addon is-item" level="6" is-item="no"></span>
         <input type="text" class="form-control"><span class="input-group-btn">
         <button class="add-cat btn btn-default" level="6" type="button">Go!</button></span></div>
         <div class="items-in">
           <ul class="nav nav-pills nav-stacked">
             <?php
             $k=1300;
             foreach($dropmenus as $dropmenu){ ?>
             <li class="field" category_id="<?php echo $dropmenu['menucategory_id']; ?>" subcategory_id="<?php echo $dropmenu['id']; ?>" level="6" has-child="no" parent-id="<?php echo $dropmenu['parent_id']; ?>" field-id="<?php echo $k; ?>" is-item="no"><a href="#"><span class="field-name"><?php echo $dropmenu['name']; ?></span>
              <?php if(!check_item($dropmenu['menucategory_id'],$dropmenu['id'])){  ?> 
             <span class="close">x</span>
             <?php } ?>
             </a></li>
             <?php
             $k++;
           } ?>
         </ul></div></div>
         <?php } ?>

       </div>
       <!-- <div class="row"><button class="btn btn-primary" id="submit-it">Submit</button></div> -->
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
   <script src="../assets/js/jquery.blockUI.js" type="text/javascript"></script>
<script>

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