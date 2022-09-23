<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>
<?php require_once "../AntiXSS.php"; ?>

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
  
  $sql="SELECT * FROM `tax`";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){ }
  $taxes=$sth->fetchAll();
  
?>
<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Tax</title>
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
                       Taxes
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                         <li><a href="menu_items.php"><i class="fa fa-suitcase"></i> Menu Items</a></li>
                         <li><a href="#"><i class="fa fa-tags"></i> Taxes</a></li>
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
        <div class='col-md-4 well'  >
            <div class='parent' parent-id='0' >Taxes</div>
            <div class='input-group'>
            <span class='input-group-addon is-item' level='1' is-item='no' ></span>
              <input type='text' class='form-control'>
              <span class='input-group-btn'>
                <button class='add-tax btn btn-default' level='1' vid='<?php echo $vid; ?>' type='button'>Go!</button>
              </span>
            </div>
            <div class='items-in'>
                <ul class='nav nav-pills nav-stacked tax_field_append'>
                <?php if(count($taxes)){ ?>
               <?php
                $r=200;
                $s=400;
                foreach($taxes as $tax){ ?>
                <li class="field" level="1" tax_id="<?php echo $tax['id']; ?>" ><a href="#"><span class="field-name"><?php echo $tax['tax_name']; ?></span>
                
                </a></li>
                 <?php
                  $r++;
                  $s++;
                  } ?>
                </ul>
                <?php } ?>
            </div>
        </div>
      
      <div class="col-md-1"></div>
      <div class="col-md-6 well" id="append_cat_text"> Menu categories would be shown here based on taxes </div>  
 
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
<!-- <script src="../assets/js/my.js" type="text/javascript"></script> -->
<!-- DATA TABES SCRIPT -->
<script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
   <script src="../assets/js/jquery.custom-scrollbar.js" type="text/javascript"></script>
 
<script>

var levelCount=1;
    $('.field').unbind().click(function(){

    var fieldName= $(this).find('.field-name').text();
    
    var taxId= $(this).attr('tax_id');
   
    var level= parseInt($(this).attr('level'));
    var nextLevel = level + 1;
    var vid= $(this).parents('.main-con').attr('vid');
    var fields= [taxId];
    
    var levelSelect= '[level=' + level + ']';

    // Add active class to current field only
    $(' .field').removeClass('active');
    $(this).addClass('active');

    var isNext= $('#level-' + nextLevel);
    var event1='get_category_tax'
       $.post("functions.php",
        
    {
    event: event1,
    venue_id: vid,
    tax_id: taxId
    },
    
    function(data){

    console.log(data);
$('#append_cat_text').empty();
      $.each(data, function(index,value) {
  value.percentage= value.percentage?value.percentage:0;
       // alert(value.name); // will alert each value
        var field= "<div class='field col-md-12 well' level='2' tax_id='"+ taxId +"'  > <input type='text' value='" + value.name + "' id='" + value.id + "' name='categoryname' class='field-name col-md-6' readonly/> <input type='text' value='" + value.percentage + "' name='percentage' class='field-name col-md-2' />% </div>";
      
      //<div name='menucategory_id[]' class='field-name col-md-6'>" + value.name + "</div>
      //if(value.percentage==null) 0 else
    $('#append_cat_text').append(field).fadeIn(1000);
    });
    var button="<div style='text-align: center;'><button class='add-category-tax btn btn-primary' level='1' vid='<?php echo $vid; ?>' type='button'>Submit</button></div>";
  $('#append_cat_text').append(button).fadeIn(1000);
    
   
    },"json"
    );
 
    });
  $('#append_cat_text').on('click','.add-category-tax',function(){
  // $(".add-category-tax").unbind().click(function(){
  
    var vid= $(this).attr('vid');
    //var mid=$(this).parent().attr('menucategory_id');
    var tax_id=$(this).parent().parent().find('.field').attr('tax_id');

    var percent = new Array();

$("input:text[name=percentage]").each(function(){
    percent.push($(this).val());
});

    var category_ids = new Array();

$("input:text[name=categoryname]").each(function(){
    category_ids.push($(this).attr('id'));
});

    var event='add-category-tax';

    $.post("functions.php",
    {
    event: event,
    vid: vid,
    percentage: percent,
    menu_id: category_ids,
    tax_id: tax_id
    },
    
    function(data){

    console.log(data);

    $('#append_cat_text').empty();
    $('#append_cat_text').append("<div class='text-green'> The tax values saved successfully </div>");
    }
    );
    //});
    });

    //saving taxes
    $(".add-tax").unbind().click(function(){
    var vid= $(this).attr('vid');
    var value= $("[type=text]").val();
    value = value.replace(/(<([^>]+)>)/ig,"");
    
    var event='add-tax';
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
    name: value
    },
    
    function(data){

    console.log(data);

    var field= "<li class='field' level='1' tax_id=" + data +"  ><a href='#'><span class='field-name'>" + value + "</span></a></li>";
    $('.tax_field_append').append(field).fadeIn(1000);
   
    }
    );
    });
    
    
    function addLevel(level,parent, taxId){
    
    var newLevel= "<div class='col-md-1'></div><div class='col-md-6 well ' id='level-" + level + "'>Level "+ level +"<div class='parent' tax-id='" + taxId + "' tax_id='" + taxId + "' >"+ parent +"</div><div class='items-in'><ul class='nav nav-pills nav-stacked'></ul></div></div>";
    if(level<=2)
    $('.main-con').append(newLevel).fadeIn(1000);
    levelCount++;
    }
    
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