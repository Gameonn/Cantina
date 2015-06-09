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
  $r=$sth->fetchAll();
  $vid=$r[0]['venue_id'];

  $sql="SELECT * from servings where venue_id=:venue_id and is_live=1 and is_deleted=0 order by id DESC";
  $sth=$conn->prepare($sql);
  $sth->bindValue('venue_id',$vid);
  try{$sth->execute();}catch(Exception $e){ }

  $servings=$sth->fetchAll();

  $sql="SELECT * FROM `pricing_names` where venue_id=:venue_id and is_live=1 and is_deleted=0 order by id DESC";
  $sth=$conn->prepare($sql);
  $sth->bindValue('venue_id',$vid);
  try{$sth->execute();}catch(Exception $e){ }

  $pricing_names=$sth->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Gambay| Pricing Category</title>
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
<body>
<div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Left side column. contains the logo and sidebar -->
<?php require_once "../php_include/manager_leftmenu.php"; ?>
          <!-- right-side -->
          <aside class="right-side">                
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <h1>
                Pricing Categories
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="menu_items.php"><i class="fa fa-suitcase"></i> Menu Items</a></li>
                <li><a href="#"><i class="fa fa-tags"></i> Pricing Categories</a></li>
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


		
                 <div class="col-md-6 well level-main" style='height:auto;'>
                   Pricing Categories
                   <div class="input-group "><span class="input-group-addon is-item" level="2" is-item="no"></span>
                    <input type="text" class="form-control"><span class="input-group-btn"><button class="add-pname btn btn-default" level="2" vid='<?php echo $vid; ?>' type="button">Go!</button></span></div>
                    <div class="items-in">
                      <ul class="nav nav-pills nav-stacked pric_field_append">
                        <?php if(count($pricing_names)){ ?>
                        <?php
                        $r=200;
                        $s=400;

                        foreach($pricing_names as $pricing_name){ ?>
                        <li class="field1 pric" level='2' price_id='<?php echo $pricing_name["id"]; ?>' ><a href="#"><span class="field-name"><?php echo $pricing_name['name']; ?></span>
                         <span class="close">x</span> 
                       </a></li>
                       <?php
                       $r++;
                       $s++;
                     } ?>


                   </li>  
                 </ul>
                 <?php } ?>
               </div></div>   




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
     <!-- <script src="../assets/js/my.js" type="text/javascript"></script> -->
     <!-- AdminLTE App -->
     <script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script>
     <!-- Category Block effect -->

     <!-- DATA TABES SCRIPT -->
     <script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
     <script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
     <!-- <script src="../assets/js/jquery.custom-scrollbar.js" type="text/javascript"></script> -->
     <script>
       function myfunction()
       {
        $('.field1').unbind().click(function(){  

         var level= parseInt($(this).attr('level'));
           //alert(level);
           var levelSelect= '[level=' + level + ']';
          // Add active class to current field only
          $(' .field1').removeClass('active');
          $(this).addClass('active');


        });
        
        $(".pric .close").unbind().click(function(){

          var prid= $(this).parents('.pric').attr('price_id');
          var fselect='.main-con [price_id=' + prid + ']';

          var event='remove-pricing-name';

          $.post("functions.php",
          {
            event: event,
            pricing_name_id: prid
          },

          function(data){

            var option=data;

            console.log(data);

      //$('body').append(catid);
      if(option==1)
        $(fselect).remove();
      else
        alert("Pricing Category Linked with some pricing");
      myfunction();
    } 
    );

        });
        
      //saving pricing names    
      $(".add-pname").unbind().click(function(){

        var value= $(this).parent().parent().find("[type=text]").val();
        value = value.replace(/(<([^>]+)>)/ig,"");
        var vid=$(this).attr('vid');
        var event='add-pricing-name';
        if(value== ""){
          alert('name can\'t be empty');  
        }
        else{
      //Empty the value
      $("[type=text]").val('');

    } 

    $.post("functions.php",
    {
      event: event,
      name: value,
      vid: vid
      
    },

    function(data){

      //alert(data);
      console.log(data);
      var field= "<li class='field1 pric' level='2' price_id='"+ data +"'><a href='#'><span class='field-name'>" + value + "</span><span class='close'>x</span></a></li> ";
      $('.pric_field_append').append(field).fadeIn(1000);
      myfunction();
    }  
    );

  });

    }
    myfunction();
  </script>

  <script>
              //Flat red color scheme for iCheck
              $(document).ready(function() {
              //$(".demo").customScrollbar();
              $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-red',
                radioClass: 'iradio_flat-red'
              });

            });
            </script>

          </body>
          </html>