<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>
  
<?php

  $itemid=$_REQUEST['item_id'];
 //$servingid=$_REQUEST['serving_id'];

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

  $sql="SELECT id,type from servings where venue_id=:venue_id";
  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $servings=$sth->fetchAll();
  
  
	$sql="SELECT * FROM `tax` where id IN (select tax_id from category_tax where menucategory_id=:menucategory_id and is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("menucategory_id",$menuid);
    try{$sth->execute();}catch(Exception $e){ }
    $taxes=$sth->fetchAll();
    
    // $sql="SELECT * FROM  `pricing_names` where venue_id=:venue_id and is_live=1 and is_deleted=0 ";
    $sql="SELECT pricing_names.* FROM  `pricing_names` where venue_id=:venue_id and is_live=1 and is_deleted=0 and id NOT IN (SELECT pricing_names.id as pr_id from item join pricing on pricing.item_id=item.id left join pricing_names on pricing_names.venue_id=pricing.venue_id and pricing_names.id=pricing.pricing_name_id join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id  where item.id=:item_id and item.is_live=1 and item.is_deleted=0)";
    $sth=$conn->prepare($sql);
    $sth->bindValue("venue_id",$vid);
    $sth->bindValue("item_id",$itemid);
    //$sth->bindValue("serving_id",$servingid);
    try{$sth->execute();}catch(Exception $e){ }
    $names=$sth->fetchAll();
    
   $sql="SELECT item.id as item_id,item.name as item_name, item.item_description as item_desc,servings.type as serving, item.pic as item_pic ,pricing.quantity as qty, pricing_names.id as pr_id,pricing_names.name as pr_name, serving_price.serving_id as serve_id, serving_price.item_price,serving_price.agg_price 
from item join pricing on pricing.item_id=item.id and pricing.venue_id=:venue_id left join pricing_names on pricing_names.venue_id=pricing.venue_id and pricing_names.id=pricing.pricing_name_id join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 join servings on servings.id=serving_price.serving_id  where item.id=:item_id and item.is_live=1 and item.is_deleted=0 group by serve_id";

/*$sql="select pricing_names.*,pricing_names.id as pr_id,pricing_names.name as pr_name,item.*,item.name as item_name,item.pic as item_pic,servings.*,pricing.*,serving_price.* from pricing_names left join item on item.id=:item_id and item.is_live=1 and item.is_deleted=0 left join pricing on pricing.venue_id=pricing_names.venue_id and pricing.item_id=item.id left join servings on servings.venue_id=pricing_names.venue_id left join serving_price on serving_price.pricing_id=pricing.id and serving_price.serving_id=:serving_id
where pricing_names.venue_id=:venue_id and pricing_names.is_live=1 and pricing_names.is_deleted=0";*/
  
  $sth=$conn->prepare($sql);
  $sth->bindValue("item_id",$itemid);
  $sth->bindValue("venue_id",$vid);
  //$sth->bindValue("serving_id",$servingid);
  try{$sth->execute();}catch(Exception $e){}
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);
  ?>

 <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Menu Items</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
 <link href="../assets/css/bootstrap-switch.css" type="text/css" rel="stylesheet">
  <style>
  .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
  text-align: center; vertical-align: middle; }
  .active{background-color: #ccc!important;}
  </style>
  <body style='height:initial;'>
    <div class="wrapper row-offcanvas row-offcanvas-left">
      <!-- Left side column. contains the logo and sidebar -->
      <?php require_once "../php_include/manager_leftmenu.php"; ?>
      <!-- right-side -->
      <aside class="right-side">                
        <!-- Content Header (Page header) -->
        <section class="content-header" style="display: -webkit-box;">
			<h1>
			   Multiple Pricing
			</h1>
			<!-- <div class="delete-item" style="font-size: x-large;font-family: 'open sans';font-weight: 100;margin-left: 10px;">
			<a href="functions.php?event=delete-item-serving&item=<?php echo $itemid; ?>&serving=<?php echo $servingid; ?>" class="fa fa-trash-o" style='text-decoration: none;color: #9A9CBB;' ></a>
		  </div> -->
			<ol class="breadcrumb">
				<li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
				 <li><a href="menu_items.php"><i class="fa fa-suitcase"></i>Menu Items</a></li>
				  <li><a href="#"><i class="fa fa-tags"></i>Multiple Pricing</a></li>
			</ol>
		</section>

       <section class="content" style="text-align:center;">
            <?php //error div
            if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
            <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?php echo $_REQUEST['msg']; ?>
            </div>
            <?php } // --./ error -- ?>
            
            
            <form action="functions.php" method="post" enctype="multipart/form-data">
              <div class="body">
              
				<?php //error div
				if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
				<div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
				  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				  <?php echo $_REQUEST['msg']; ?>
				</div>
				<?php } // --./ error -- ?>
				
<div class="row form-group">
				<div class="col-lg-8">
				  <div class="row form-group">
					<div class="col-md-6">
					  <label> Item Name</label>
					   <input type="text" name="itemname" class="form-control" value="<?php echo $result[0]['item_name']; ?>" readonly/>
					   </div>
					   
					   <div class="col-md-6">
					   <label> Serving Size</label><br>
					   <!--<input type="text" name="serving" class="form-control" value="<?#php echo $row['serving']; ?>" readonly/>-->
						<select class='serve_it form-control' name="serving_id" >
						<option value="0" sid="0">Select Serving Size</option>
						 <?php foreach($result as $row){ ?> 
						 <option value="<?php echo $row['serving']; ?>" sid="<?php echo $row['serve_id']; ?>"><?php echo $row['serving']; ?></option>
						<?php } ?>  
					   </select>
					   </div>
				
				  </div>  
							                     
                    <div class="row form-group" id="append_pricing_content" style="margin-left: auto;margin-right: auto;">
					<div class="well" style="padding:60px;">
						Detailed Pricing Categories information will be displayed here.
										
					</div>
                    </div> 
                    
        
                    
                </div>
                <div class="col-lg-4 image-upload">
                 <img src="<?php echo $obj->getImagePath($result[0]['item_pic']) ?>" style="width:100%; height:260px;padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;" class="def-image">
                 <input type="file" name="image" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
                
                </div>
              </div>
			  <div class='col-md-12 form-group'>
			  <label>Item Description </label>
				<textarea class='form-control' name='item_description' ><?php echo $result[0]['item_desc']; ?></textarea>
				</div> 
              <div class="footer" style="width: 50%;margin-left: auto;margin-right: auto;margin-top:15px;">                                                               
                <button type="submit" class="btn bg-olive btn-block add_pricing_btn" onclick="return 0;">Submit</button>
                <a class="btn bg-red btn-block" href="menu_dashboard.php">Back</a>
              </div>
              <!-- hidden -->
              <input type="hidden" name="token" value="<?php echo $key; ?>">
              <input type="hidden" name="venue_id" value="<?php echo $vid; ?>" class="venue">
              <input type="hidden" name="item_id" value="<?php echo $itemid; ?>" class="item-new">
             <!-- <input type="hidden" name="serving_id" value="<?#php echo $servingid; ?>"> -->
              <input type="hidden" name="event" value="add-pricing">
              <input type="hidden" name="redirect" value="menu_dashboard.php">

              
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
      <!-- Bootstrap WYSIHTML5 -->
      <script src="../assets/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
      <!-- iCheck -->
      <script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
      <!-- AdminLTE App -->
      <script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script> 
      <script src="../assets/js/bootstrap-switch.js" type="text/javascript"></script>
      <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>
	  <script src="../assets/js/jquery.blockUI.js" type="text/javascript"></script>
		<script src="../assets/js/jquery.validate.js" type="text/javascript"></script>
		
      <script type="text/javascript">
	  $('select').on('change',function(e){
	  $('#append_pricing_content').empty();
	  var tmp_field='<div class="well" style="padding:60px;">Detailed Pricing Categories information will be displayed here.</div>';
	  	$('#append_pricing_content').append(tmp_field).fadeIn();
	  var serving=$(this).find('option:selected').attr('value');
	  var serving_id=$(this).find('option:selected').attr('sid');
	  var item_id=$('.item-new').attr('value');
	  var vid=$('.venue').attr('value');
	
	  if(serving_id=='0'){
	  e.preventDefault();
	  e.stopPropagation();
	  }
	  else{
	  
	  $.blockUI({ message: ('<h4><img src="../uploads/ajax-loader.gif" /> Please wait...</h4>' ),
    		css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: 0.5, 
            color: '#fff' 
        }
		});
	  var event="get-pricing";
	  $.post('functions.php', 
  {
  event: event,
  serving: serving, 
  serving_id: serving_id,
  item_id: item_id,
  vid: vid
  }, 
  function(data) {
     console.log(data);
	var chk=(data.tags[0]['status']=='1')?"checked":"";
	var chk1=(data.tags[0]['status']=='0')?"checked":"";
	var ch=(data.tags[0]['special_flag']=='1')?"checked":"";
	var ch1=(data.tags[0]['special_flag']=='0')?"checked":"";
	
	var a1=(data.tags[0]['status']=='1')?"active":"";
	var a2=(data.tags[0]['status']=='0')?"active":"";
	var a3=(data.tags[0]['special_flag']=='1')?"active":"";
	var a4=(data.tags[0]['special_flag']=='0')?"active":"";
	$('#append_pricing_content').empty();
	
	var radio_switch='<div class="col-sm-6"> <div class="col-sm-6"><label> Show as Special</label><br><div class="btn-group special_button" data-toggle="buttons"><label class="btn btn-default '+a3+'" id="opt1"><input type="radio" value="1" name="specials" id="option1" '+ch+'> ON</label><label class="btn btn-default '+a4+'" id="opt2"><input type="radio" name="specials" value="0" id="option2" '+ch1+'> OFF</label></div></div><div class="col-sm-6"><label> Show in Menu</label><br><div class="btn-group menu_button" data-toggle="buttons"><label class="btn btn-default '+a1+'" id="opt3"><input type="radio" name="status" value="1" id="option3" '+chk+'> ON</label><label class="btn btn-default '+a2+'" id="opt4"><input type="radio" name="status" value="0" id="option4" '+chk1+'> OFF</label></div></div></div> <div class="append_tax col-sm-6"> <label> Tax </label> <br> </div>';
		$('#append_pricing_content').append(radio_switch).fadeIn();
		
        $.each(data.tax, function(index,value) {
	var tax="<div style=' display: -webkit-box;'><input type='hidden' name='tax_id[]' value='"+value.tax_id+"'><input type='text' value='" + value.tax_name + "' id='" + value.tax_id + "' name='tax[]' class='field-name col-xs-6' readonly/> <input type='text' value='" + value.percentage + "' name='percentage[]' readonly class='field-name col-xs-2' /><span class='input-group-addon' style='display:inline;'>%</span></div> ";
         
		$('#append_pricing_content .append_tax').append(tax).fadeIn();
     });
	
	
	var nxt='<label style="margin-top: 15px;margin-bottom: 10px;"> Detailed Pricing Categories</label><br> <div class="col-sm-6" ><label> Pricing Category Name</label></div><div class="col-sm-3" ><label>Quantity</label></div> <div class="col-sm-3"><label> Price</label></div> <input type="hidden" name="serving_id" value="'+serving_id+'">';
	
		$('#append_pricing_content').append(nxt).fadeIn();
		$.each(data.pricing, function(index,value) {
		value.qty=(value.qty=="99999999")? "Unlimited":value.qty;
		var field=' <div class="col-sm-6" style="padding-bottom: 15px;" ><input type="hidden" name="pr_id[]" value="'+value.pr_id+'"> <input  name="p_name[]" class="form-control" value="'+value.pr_name+'" readonly/></div> <div class="col-sm-3" style="padding-bottom: 15px;" ><input type="text" name="qty[]" class="form-control" value="'+value.qty+'" /></div><div class="col-sm-3" style="padding-bottom: 15px;" > <input type="number" step="0.05" name="item_price[]" class="form-control" value="'+value.item_price+'" /></div>';
		$('#append_pricing_content').append(field).fadeIn();
    });
	
	   $.each(data.names, function(index,value) {
	var field1='<div class="col-sm-6" style="padding-bottom: 15px;" ><input type="hidden" name="pr_id[]" value="'+value.id+'">    <input  name="p_name[]" class="form-control" value="'+value.name+'" readonly/></div><div class="col-sm-3" style="padding-bottom: 15px;"><input type="number" name="qty[]" class="form-control" placeholder="Unlimited" /></div><div class="col-sm-3" style="padding-bottom: 15px;"><input type="number" step="0.05" name="item_price[]" class="form-control" placeholder="Price" /></div>';	
	$('#append_pricing_content').append(field1).fadeIn();
    });
 
    //extrafields(chk1,chk);
	$(document).ajaxStop($.unblockUI); 
   },"json"
   );
	  }
	  });
	  //extrafields();
	$('.image-upload').on("change","input[type='file']",function () {
                var files = this.files;
                var reader = new FileReader();
                name=this.value;
                var this_input=$(this);
                reader.onload = function (e) {

                 this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100 %').height(260);
               }
               reader.readAsDataURL(files[0]);
             });

 /*function extrafields(chk1,chk){
	$("[name='specials'] ").removeAttr('checked',"");
	$("[name='specials'] ").removeClass('active');
	
  console.log(chk1,chk);
	$("[name='status'] ").removeAttr('checked','');
	$("[name='status'] ").removeClass('active');
	if(chk1=='1'){
	$("#option1").attr('checked','');
	$("[name='specials']").parent('#opt1').addClass('active');
	}
	else{
	$("#option2").attr('checked','');
	$("[name='specials']").parent('#opt2').addClass('active');
	}
	
	if(chk=='1'){
	$("#option3").attr('checked','');
	$("[name='status']").parent('#opt3').addClass('active');
	}
	else{
	$("#option4").attr('checked','');
	$("[name='status']").parent('#opt4').addClass('active');
	}
		
 }*/
    </script>

<script> 

		$(document).ready(function () {
		
		//$(".add_pricing_btn").validate();
		
			$('.add_pricing_btn').on('click', function(e)
			{ 
			
			if($('#append_pricing_content .well').length){
				e.preventDefault();
				e.stopPropagation();
				alert("Please Select Serving Size");
				}
			 else{
				
				
			$('.add_pricing_btn').submit();
			}
			}); 	
	});
</script>	
  </body>
  </html>
  