<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/admin_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php
  //error_reporting(1);
  $obj = new GeneralFunctions; 

  $sql="SELECT * FROM `venuetype`";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){ }
  $vtypes=$sth->fetchAll();
  
  $sql="Select * from venue";
  $sth=$conn->prepare($sql);
  try{$sth->execute();}catch(Exception $e){ }
  $venues=$sth->fetchAll();
  
?>
<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Cusines</title>
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
 <!-- Category Block Design -->
    <link rel="stylesheet" href="../assets/css/my.css" type="text/css" />
  </head>
  <style>
  .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
  text-align: center; vertical-align: middle; }
  .fa {
line-height: 1.5;}
	.venue_name{font-size: 17px;font-weight: 500;text-transform: capitalize;}
  </style>
  <body >
    <div class="wrapper row-offcanvas row-offcanvas-left">
      <!-- Left side column. contains the logo and sidebar -->
      <?php require_once "../php_include/admin_leftmenu.php"; ?>
      <!-- right-side -->
      <aside class="right-side">                
        <!-- Content Header (Page header) -->
       <section class="content-header">
                    <h1>
                      Gambay Cusines
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                         <li><a href="#"><i class="fa fa-cutlery"></i> Gambay Cusines</a></li>
                    </ol>
                </section>

        <!-- Main content -->
        <section class="content">
       
<div class="container">
    <div class="row main-con" style="width:95%" vid='<?php echo $vid; ?>'>
        <div class='col-md-4 well'  >
            <div class='parent' parent-id='0' >Cusines</div>
            <div class='input-group'>
            <span class='input-group-addon is-item' level='1' is-item='no' ></span>
              <input type='text' class='form-control'>
              <span class='input-group-btn'>
                <button class='add-vtype btn btn-default' level='1' type='button'>Go!</button>
              </span>
            </div>
            <div class='items-in'>
                <ul class='nav nav-pills nav-stacked vtype_field_append'>
                <?php if(count($vtypes)){ ?>
               <?php
                foreach($vtypes as $vtype){ ?>
                <li class="field" level="1" vtype_id="<?php echo $vtype['id']; ?>" ><a href="#"><span class="field-name"><?php echo $vtype['type']; ?></span>
                <span class="close">x</span>
                </a></li>
                 <?php  } ?>
                </ul>
                <?php } ?>
            </div>
        </div>
      
     <div class="col-md-1"></div>
      <div class="col-md-6 well" id="append_cat_text"> Venues would be listed here based on cusines</div> 
 
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
function myfunction(){
    $('.field').unbind().click(function(){
    var vtypeId= $(this).attr('vtype_id');
     // Add active class to current field only
    $(' .field').removeClass('active');
    $(this).addClass('active');
   //alert(vtypeId);
    var event1='get-venues'
       $.post("eventHandler.php",  
    {
    event: event1,
    vtype_id: vtypeId
    },
    
    function(data){
    console.log(data);
    //alert(data);
    $('#append_cat_text').empty();
    if(data==0)
    $('#append_cat_text').append("No venues based on this cuisine ");
	else{
        $.each(data, function(index,value) {
      //alert(value.venue_name); // will alert each value
        var field= "<div class='field col-md-12 well' level='2' vtype_id='"+ vtypeId +"'  > <div class='venue_name'>"+value.venue_name+" </div> </div>";

    $('#append_cat_text').append(field).fadeIn(1000);
    });
    myfunction();
    }
myfunction();
    },"json"
    );
 
    });
    
       $(".field .close").unbind().click(function(){

    var cusine= $(this).parents('.field').attr('vtype_id');
    var fselect='.main-con [vtype_id=' + cusine + ']';
  //alert(cusine);
   var event='remove-cusine';
    
    $.post("eventHandler.php",
    {
    event: event,
    vtype_id: cusine
    },
    
    function(data){
    
    var option=data;
    
    console.log(data);

    if(option=='3')
    $(fselect).remove();
    else
    alert("Cuisine Linked with some venue");
    
    
     myfunction();
    } 
    );
    });
}
myfunction();
    //saving cusines
    $(".add-vtype").unbind().click(function(){
    var value= $("[type=text]").val();
    value = value.replace(/(<([^>]+)>)/ig,"");
    var event='add-vtype';
    if(value== ""){
    alert('name can\'t be empty');  
    }
    else{
    //Empty the value
    $("[type=text]").val('');
    } 

    $.post("eventHandler.php",
    {
    event: event,
    name: value
    },
    
    function(data){

    console.log(data);

    var field= "<li class='field' level='1' vtype_id=" + data +"  ><a href='#'><span class='field-name'>" + value + "</span><span class='close'>x</span></a></li>";
    $('.vtype_field_append').append(field).fadeIn(1000);
   myfunction();
    }
    );
    });
      
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