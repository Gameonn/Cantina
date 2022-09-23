<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>

<?php

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

    $sql="SELECT * from servings where venue_id=:venue_id and is_live=1 and is_deleted=0";
    $sth=$conn->prepare($sql);
    $sth->bindValue("venue_id",$vid);
    try{$sth->execute();}catch(Exception $e){ }
    $servings=$sth->fetchAll();

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

    $sql="SELECT * FROM `tax` where id IN (select tax_id from category_tax where menucategory_id=:menucategory_id and is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    try{$sth->execute();}catch(Exception $e){ }
    $taxes=$sth->fetchAll();

    $sql="SELECT * FROM  `pricing_names` ";
    $sth=$conn->prepare($sql);
    try{$sth->execute();}catch(Exception $e){ }
    $names=$sth->fetchAll();

?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Gambay| Add Item</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>  
        <link href="../assets/css/bootstrap-switch.css" rel="stylesheet">
    </head>

    <style>
      .modal-title {text-align: center!important;}
      .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
          text-align: center; vertical-align: middle; }
          .form-control{border-radius: 3px!important;}
		  
		.error{ .font-weight:light; font-size:12px; color:rgba(255,0,0,1); }
		//.row.switch_check {width:80%; }
 </style>
      <body style='height:initial;'>
        <div class="wrapper row-offcanvas row-offcanvas-left">
          <!-- Left side column. contains the logo and sidebar -->
          <?php require_once "../php_include/manager_leftmenu.php"; ?>
          <!-- right-side -->
          <aside class="right-side">                
            <!-- Content Header (Page header) -->
            <section class="content-header row" style="padding-left:15px;padding-right:15px;">
                <h1 class='col-md-3'>
                 Add Menu Items 
             </h1>
              <!-- <div class="menu-dashboard col-md-3" style="font-family: 'open sans';font-weight: 100;margin-top: 4px;">
                <a href="menu_dashboard.php" style='text-decoration: none;' > Menu Dashboard</a>
              </div> -->
             <ol class="breadcrumb col-md-3 col-md-offset-3">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
                <li><a href="menu_items.php"><i class="fa fa-shopping-cart"></i>Menu Levels</a></li>
                <li><a href="#"><i class="fa fa-tags"></i>Add Menu Items</a></li>
            </ol>
        </section>

        <section class="content add-item-content" style="text-align:center;">
        <form action="functions.php" method="post" enctype="multipart/form-data" id='add_item_form'>
        
                <?php //error div
                if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
                <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <?php echo $_REQUEST['msg']; ?>
              </div>
              <?php } // --./ error -- ?>

              <div class="col-md-12 form-group">
                <div class="col-md-2 level1">
                  <label> Category <br>(Level 0) </label><br>
                  <select id="level1" level="1" name="menu_id" class='form-control menus'>
                    <option value="">Select Level0 </option>
                    <?php foreach($menus as $menu) {?>
                    <option id="<?php echo $menu['id']; ?>" menu_id="<?php echo $menu['id']; ?>" value="<?php echo $menu['id']; ?>"><?php echo $menu['name']; ?></option>
                    <?php } ?>  
                </select>
            </div> 

            <div class="col-md-2 level2 ">
   
        </div>

        <div class="col-md-2 level3 ">
 
    </div>

    <div class="col-md-2 level4 ">

</div>

<div class="col-md-2 level5 ">

</div>

<div class="col-md-2 level6 ">

</div>
</div> 

<div class='col-md-12 form-group' style='text-align:left;'>
<div class='col-lg-8'>
<div class="row">
<div class="col-md-7">
<div class="item_container" style='display: inline-block;'>
  <div class="col-sm-6 item_field" style="padding-left:0px;margin-top: 10px;">
 <label > Item Name </label> <br>
 <input id='item_name' type='text' value='' placeholder='Item Name' class='form-control' required readonly >

 </div>  
    <div class="col-sm-6" style="padding-left:0px;margin-top: 10px;">
      <label> Servings</label><br>
      <select id="serving" name="serving_id" class="form-control">
        <?php foreach($servings as $serving) {?>
        <option value="<?php echo $serving['id']; ?>"><?php echo $serving['type']; ?></option>
        <?php } ?>  
    </select>
</div> 
</div>
<div class="row switch_check" style='margin-top: 15px;margin-bottom: 15px;'>
    <div class="col-sm-4" style='margin-top: 15px;'>
        <label>Show as Special </label><br>
        <!-- <input id="switch-animate" name='specials' class='specials' type="radio" checked > -->
          <input type="radio" name="specials" class='specials switch-radio2' data-radio-all-off="true" >
    </div>
    <div class="col-sm-4" style='margin-top: 15px;'>
     <label> Show on Menu </label><br>
     <input name='item_menu' class='item_menu switch-radio2' checked data-radio-all-off="true" type="radio" > 
 </div>
 <div class="col-sm-4" style='margin-top: 15px;'>
 <label > Regular Price </label> <br>
 <input type='number' step='0.05' name='reg_price' placeholder='Price In $' class='form-control reg_pric' required />
 </div>
 </div>

</div>
<div class="col-md-5" id="append_tax_text">
 <a class='item-tax btn btn-default btn-sm' style='padding-bottom: 0px;padding-top: 0px;'> Select Tax</a><br>
 <div class='tax' style='margin-top: 6px;display: inline-block;'>
 <div class='well'>
 Taxes would be displayed here based on main category</div>
 </div>
</div>
</div>
 </div>
   <div class="col-lg-4 image-upload" style='margin-top: 15px;'>
                  <img src="../uploads/item-default.jpg" class="def-image" style="width: 100%;height: 150px; padding: 3px;border: 1px solid rgb(213, 206, 206);
                  border-radius: 4px;">
                  <input type="file" name="image" style="display: inline;" class="filestyle get_image" data-input="false" data-size="sm" data-buttonText="Select file">
				   <p class="text-yellow"> Image should be in the ratio of (3:2) </p>
                </div>
</div>

<div class='col-md-12 form-group'>
<textarea class='form-control' name='item_description' placeholder='Enter Item Description Here....'></textarea>
</div> 
<input type="hidden" name="venue_id" value="<?php echo $vid; ?>">
<input type="hidden" name="event" value="add-item">
<div class='col-md-12 form-group'>
<button class="btn btn-primary" id="form_submit" >Submit</button>
</div>
</form>
</section><!-- /.content -->
</aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<!-- jQuery 2.0.2 -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<!-- jQuery UI 1.10.3 -->
<script src="../assets/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
<!-- Bootstrap -->
<script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/js/plugins/timepicker/bootstrap-timepicker.min.js" type="text/javascript"></script>
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
    <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="../assets/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="../assets/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="../assets/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script src="../assets/js/bootstrap-switch.js"></script>

<script src="../assets/js/jquery.validate.js" type="text/javascript"></script>

<script type="text/javascript">
 $(document).ready(function() {
   // $('#serving').multiselect({
      //includeSelectAllOption: true
  //});

    $("[name='specials']").bootstrapSwitch();
    $("[name='item_menu']").bootstrapSwitch();                    
});

</script>

<script>
function menus(){
$('.menus').click(function(){
$('.item_field input').empty();
 $('.item_field input').attr('value',"");
 $('.item_field input').attr('required',true);
 $('.item_field input').attr('placeholder',"Item Name");
 
 var level=parseInt($(this).attr('level'));
 var levelid=$(this).attr('id');
 var select_id=$(this).children(":selected").attr('id');
  var nextlevel=level+1;
  var postlevel=level+2;
  var nlevel=level+3;
  
 if(select_id){
 var menu_id=$(this).children(":selected").attr('menu_id');
 var event='get-subcategory';

 var prevlevel=level-1;
 if(level==1){
var pid=0;
 }
 else{
 var pid=$(this).children(":selected").attr('id');
 }
      $.post("functions.php",
      {
        event: event,
        menucategory_id: menu_id,
        subcategory_id: select_id,
        parent_id: pid
    },   

    function(data){
	//alert(data);
        console.log(data);
      //alert(data);

         $('.level'+nextlevel).empty(); 
         $('.level'+postlevel).empty(); 
         $('.level'+nlevel).empty(); 
         if(data[0]=='0'){
         //alert("No more lower level categories");
         //alert(data[1]);
         $('.item_field').empty();
         $('.item_field').append(" <label > Item Name </label> <br><input type='text' value='"+ data[1] +"' class='form-control' readonly >");
         }
         else{
         $('.level'+nextlevel).append("<label>Category <br>(Level "+ level +")</label><br>");
         $('.level'+nextlevel).append("<select id='level"+nextlevel+"' level="+nextlevel+" name='sub_id' class='form-control menus'> ");
     $.each(data, function(index,value) {

         var field= "<option id='"+ value.id +"' value='"+ value.id +"' menu_id='"+ value.menucategory_id +"' sub-id='"+ value.parent_id +"'>"+ value.name+" </option>";
        
        $('.level'+nextlevel+' #level'+nextlevel).append(field).fadeIn(1000);

     });
         $('.level'+nextlevel+' #level'+nextlevel).append("</select>");
         menus();
         }
         
    },"json"
    ); 
    }
    else
    $('.level'+nextlevel).empty();  
         $('.level'+postlevel).empty(); 
         $('.level'+nlevel).empty(); 
});
}
menus();
/*$('.menus').click(function(){
       $('.menus').show();
     
       var select_id=$(this).attr('id');
       var level=parseInt($(this).attr('level'));
       $('#'+select_id).click(function() { 
         var nextlevel=level+1;   
         var nlevel=level+2;
         if($(this).data('options') == undefined){
            //Taking an array of all options-2 and kind of embedding it on the select1
            $(this).data('options',$('#level'+nextlevel+' option').clone());

        } 
        
        var id = $(this).find(':selected').attr('id');
        
        var options = $(this).data('options').filter('[pid=' + id + ']');

        $('#level'+ nextlevel ).html(options);
        //alert(('#level'+nlevel+ ' option'));
        if($("#level"+nextlevel+ ' option[value]').length==0){
        //$('#level'+nextlevel).hide();
        $('#level'+nlevel).hide();
    }


});     
   });*/

   $('#append_tax_text').on('click','.item-tax',function(){
      //var mid=$(this).parent().attr('menucategory_id');
      var mid=$(this).parents('.add-item-content').find('#level1 :selected').attr('id');
      var event='get-tax';

      $.post("functions.php",
      {
        event: event,
        menucategory_id: mid
    },
    function(data){
	
        console.log(data);
     //alert(data);
        if(data=='0'){
       alert('Select a Category or No tax found in selected category');
       }
      /* else if(data=='2'){
        alert('First Select a Level0 Category');
        }*/
       else {
        $('#append_tax_text .tax').empty();
        //$('#append_tax_text .tax ').append("<select id='tax' multiple='multiple' name='taxes[]'> ");
        $.each(data, function(index,value) {
	var field="<div class='field col-md-12' tax_id='"+ value.id +"' style='padding-left: 0px;'  > <input type='hidden' name='tax_id[]' value='"+value.id+"'><input type='text' value='" + value.tax_name + "' id='" + value.id + "' name='tax[]' class='field-name col-md-6' readonly/> <input type='text' value='" + value.percentage + "' name='percentage[]' style='padding: 0 4px 2px 4px;' class='field-name col-md-2' /><span class='input-group-addon' style='display: inline;' >%</span> </div>"
         //var field= "<option value='"+ value.id +"'>"+ value.tax_name+" </option>";
         $('#append_tax_text .tax ').append(field).fadeIn(1000);

     });
       
       }
       
    },"json"
    );

  });

   function taxselect(){
    $('#tax').multiselect({
      //includeSelectAllOption: true
  });
}


   $('#submit-item').click(function(){
      //var mid=$(this).parent().attr('menucategory_id');
      var mid=$(this).parents('.add-item-content').find('#level1 :selected').attr('id');
      var sid=$(this).parents('.add-item-content').find('#level2 :selected').attr('id');
      var sid2=$(this).parents('.add-item-content').find('#level3 :selected').attr('id');
      var sid3=$(this).parents('.add-item-content').find('#level4 :selected').attr('id');
      var sid4=$(this).parents('.add-item-content').find('#level5 :selected').attr('id');
        /* var formData = $('#add_item_form').serialize();
         //var formData=$('.get_image').attr('files');
          alert(formData);*/
         // var ap=$('get_image').attr('src');
         //alert($('.def-image').getAttribute('src')); // foo.jpg
	var image_src=$('.def-image').attr('src'); 
          //alert(ap);
      var sp=$('.specials').prop('checked');
      var active=$('.item_menu').prop('checked');
      var price=$('.reg_pric').val();
      var serving=$('#serving').find(':selected').attr('value');
         var tax = new Array();

$(".tax option:selected").each(function(){
    tax.push($(this).attr('value'));
});

 
      var event='add-item';

      $.post("functions.php",
      {
        event: event,
        menucategory_id: mid,
        special: sp,
        price: price,
        tax: tax,
        pic: image_src
    },

    function(data){

        console.log(data);
        
        
        
    }
    );

  });
</script>
<script>
                  //Flat red color scheme for iCheck
                  $(document).ready(function() {
                    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                      checkboxClass: 'icheckbox_flat-red',
                      radioClass: 'iradio_flat-red'
                  });
                });
              </script>
              <script type="text/javascript">

                  $('.image-upload').on("change","input[type='file']",function () {
                    // alert('hey');
                    var files = this.files;
                    var reader = new FileReader();
                    name=this.value;
                    var this_input=$(this);
                    reader.onload = function (e) {

                       this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100 %').height(150);
                   }
                   reader.readAsDataURL(files[0]);
               });

              </script>

<script> 

		$(document).ready(function () {
		
		$("#add_item_form").validate();
		
			$('#form_submit').on('click', function(e)
			{ 
			
			if($('.tax .field ').length){
				//alert("Div1 exists");
				}
				else{
				e.preventDefault();
				e.stopPropagation();
				alert("Please Select Tax");
				}
				
			$('#item_name').rules('add', {  
		            required: true,
		            minlength: 3,
			}); 
				
				
			$('#add_item_form').submit();
			}); 	
	});
</script>			  
			</body>
          </html>