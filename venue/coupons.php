<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../GeneralFunctions.php"; ?>
<?php require_once "VenueClass.php"; ?>

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

	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
	$limit = 11;
	$startpoint = ($page * $limit) - $limit;
	$sortby = (int) (!isset($_GET["sortby"]) ? 1 : $_GET["sortby"]);
	
	$filterby = (int) (!isset($_GET["filterby"]) ? 25 : $_GET["filterby"]);
	if($filterby)
	$resultSet = VenueClass::getFilteredCoupons($startpoint,$limit,$filterby,$key,$sortby);
	
	else
	$resultSet = VenueClass::getAllCoupons($startpoint,$limit,$sortby,$key);
	$coupons = $resultSet["listing"];
	$total_records = $resultSet["count"];
	
	
	
	  /*  $sql="SELECT * from coupons where venue_id=:venue_id and is_deleted=0";
	    $sth=$conn->prepare($sql);
	    $sth->bindValue("venue_id",$vid);
	    try{$sth->execute();}catch(Exception $e){ }
	    $coupons=$sth->fetchAll();*/
	    ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
	    	<title>Gambay| Coupons</title>
	    	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>  
	    	<link href="../assets/css/bootstrap-switch.css" rel="stylesheet">
	    	
	    	<style type="text/css">
	    		.coupons {
	    			border-width: 2px;
	    			border-color: #e6e6e6;
	    			border-style: dashed;
	    			background-color: #FAFAFA;
	    			padding: 10px;
	    			margin-bottom: 20px;
	    			float: left;
	    			width: 240px;
	    			margin: 10px 8px;
	    			height: 275px;
	    			text-align: center;
					position: relative;
	    		}

	    		.coupon-center{

	    			background: transparent; 
	    			border: 2px dashed #ADA9A9;
	    			font-family: 'open sans';
	    			font-weight: 300;
	    			text-transform: uppercase;
	    			letter-spacing: 1.2px;
	    			cursor: pointer;
	    		}

	    		.coupon-center:hover{
	    			background: #fff;
	    		}
	    		.coupons .expiry {
	    			margin: 0;
	    			font-size: 11px;
	    			text-align: right;
	    			position: absolute;
	    			bottom: 0;
	    			right: 0;
	    			font-weight: bold;
	    			color: #34495e;
	    		}
	    		.coupons .expiry span {
	    			color: #919bac;
	    			font-weight: normal;
	    		}
	    		.add{
	    			margin-top: 10px;
	    			top: 43px;
	    			position: absolute;
	    			left: 85px;
	    		}
	    		.coupon-plus{
	    			font-size: 46px;
	    			display: inline-block;
	    			width: 60px;
	    			line-height: 60px;
	    			color: #888;
	    		}
	    		.add-coupon{
	    			margin-top: 29px;
	    			padding: 10px;
	    			font-size: 23px;
	    		}

	    		div.btn-group label {

	    			margin-left: 0px;
	    			margin-right: 22px;
	    		}
	    		.coupon-desc {
	    			margin-top: 5px;
	    			height: 60px;
	    			margin-bottom: 10px;
	    			overflow: hidden;
	    		}
	    		.coupon-name{
	    			height: 65px;
	    			overflow: hidden;
	    		}
	    		div.edit-coupon {
	    			margin: 0;
	    			font-size: 23px;
	    			text-align: left;
	    			position: absolute;
	    			bottom: 0;
	    			left: 2;
	    			font-weight: bold;
	    			color: #34495e;
	    		}
	    		.coupon-detail{
	    			padding: 7px;
	    			box-sizing: border-box;
	    			background: #555454;
	    			color: #fff;
	    		}
	    		.disabled{
	    			opacity: 0.4;
	    		}
	    		.page-content{
	    			overflow: hidden;
	    		}
	    		.datepicker{
	    			z-index: 999999!important;
	    			text-align: center!important;
	    		}
	    		a:hover, a:active, a:focus {
	    			outline: none;
	    			text-decoration: none;
	    		}
	    		.iradio_minimal {
	    			display: none;
	    		}
	    		.coupon_code{
	    			background-color: #6fa844;
	    			background-image: -webkit-linear-gradient(top,#9bcc63 0,#6fa844 100%);
	    			height: 45px;
	    			padding-top: 10px;
	    			border-radius: 3px;
	    			font-size: 18px;
	    			border: 1px dashed #0D220D;
	    		}
	    		.save-text{
	    			font-size: 10px;
	    			line-height: 10px;
	    			color: #919bac;
	    			margin-left: 10px;
	    		}
	    		.price{
	    			font-weight: 300;
	    			margin-bottom: 0;
	    			font-size: 14px;
	    			line-height: 17px;
	    			margin-top: -5px;
	    			margin-left: 10px;
	    		}

	    	</style>
	    	<link href="../assets/css/bootstrap-switch.css" rel="stylesheet">
	    </head>
	    <body style="height:initial;">
	    	<div class="wrapper row-offcanvas row-offcanvas-left">
	    		<!-- Left side column. contains the logo and sidebar -->
	    		<?php require_once "../php_include/manager_leftmenu.php"; ?>
	    		<!-- right-side -->
	    		<aside class="right-side">                
	    			<!-- Content Header (Page header) -->
	    			<section class="content-header row" style="padding-left:15px;padding-right:15px;">
	    				<h1 class='col-md-2'>
	    					Coupons	
	    				</h1>
	    				<ol class="breadcrumb co-md-3 col-md-offset-7">
	    					<li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Home</a></li>
	    					<li><a href="view_venue.php"><i class="fa fa-list-alt"></i>Venue Profile</a></li>
	    					<li><a href="#"><i class="fa fa-tags"></i>Coupons</a></li>
	    				</ol>
	    			</section>

	    			<section class="content add-item-content" style="text-align:center;">


	    				<div class="container">
	    					<div class="row">
	    						<div class='col-md-5'>
	    							<div class="btn-group srt-btn-grp" role="group" aria-label="...">
	    								<div class=""><label> FILTER BY </label></div>
	    								<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=23'" class="btn btn-default <?php if($filterby==23) echo 'active'; ?>" value='23'>Live</button>
										<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=25'" class="btn btn-default <?php if($filterby==25) echo 'active'; ?>" value="25">All</button>
	    								<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=24'" class="btn btn-default <?php if($filterby==24) echo 'active'; ?>" value="24">Expired</button>
	    								
	    							<!--	<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=25'" class="btn btn-default" value="25">Limited</button>
	    								<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=26'" class="btn btn-default" value="26"> Unlimited</button>
	    								<button type="button" onclick="window.location.href='?&limit=<?php echo $limit;?>&page=1&sortby=<?php echo $sortby; ?>&filterby=27'" class="btn btn-default" value="27">Deleted</button> -->
	    								
	    							</div>
	       <!-- <div class="">FILTER BY </div>
	        <div class="btn-group sort_btn_grp" data-toggle="buttons" >
	  	<label class="btn btn-primary active" style="margin-right: 0px;">
		    <input type="radio" name="options" value="4001" id="option1" autocomplete="off" checked> Name
		  </label>
		  <label class="btn btn-primary" style="margin-right: 0px;">
		    <input type="radio" name="options" value="4002"  id="option2" autocomplete="off"> Date
		  </label>
		  <label class="btn btn-primary">
		    <input type="radio" name="options"  value="4003" id="option3" autocomplete="off"> Value
		  </label>
		</div> --> 
	</div>

	<div class="col-md-4">
		<div id="example2_length" class="dataTables_length ">
			<label>SORT BY <select size="1" name="example2_length" class="form-control btn btn-default " aria-controls="example2" onchange="window.location.href='?&limit=<?php echo $limit;?>&page=1&filterby=<?php echo $filterby; ?>&sortby='+(this.options[this.selectedIndex].value);">
				<?php foreach(array('1'=>'Name','2'=>'Date','3'=>'Value') as $r=>$s){
					echo "<option value='$r' ";
					if($r==$sortby) echo "selected";
					echo ">$s</option>";
				} ?>
			</select></label>
		</div>
	</div>
	
	<div class="dataTables_paginate paging_bootstrap">
		<ul class="pagination">
			<li <?php if($page==1) echo "class='prev disabled'><a href='#'>"; else { echo "class='prev'><a href='?page=".--$page."&limit=$limit&sortby=$sortby'>"; $page++; } ?>← Previous</a></li>
			<?php 
			if(ceil($total_records/$limit) > 6){
				$forstart=1+$page-1;
				$forend = (6+$page-1)<=ceil($total_records/$limit) ? 6+$page-1 : ceil($total_records/$limit);
			}
			else {
				$forstart=1;
				$forend=ceil($total_records/$limit);
			}
			for($i=$forstart;$i<=$forend;$i++){ ?>
			<li <?php if($page==$i) echo "class='active'"; ?>><a href="<?php echo "?page=$i&limit=$limit&sortby=$sortby"; ?>"><?php echo $i; ?></a></li>
			<?php } ?>
			<li <?php if($page==ceil($total_records/$limit)) echo "class='next disabled'><a href='#'>"; else { echo "class='next'><a href='?page=".++$page."&limit=$limit&sortby=$sortby'>"; $page--; } ?>Next → </a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-md-12 ">
	<div class="col-md-3">
		<div class="coupons coupon-center">
			<div class="newcoupon modalBtn" data-no-turbolink ='true' data-toggle="modal" data-vid="<?php echo $vid; ?>" data-target="#add_coupon" data-remote="true" >
				<a><i class="fa fa-plus-circle coupon-plus "></i></a>
				<div class="add-coupon">
					Add New Coupon
				</div>
			</div>
		</div>
		</div>
		<div class="all_coupons">     
			<?php foreach($coupons as $key => $value) { 
			//echo $value['id'];
			$res = VenueClass::getLimit($value['id']);
			//echo $res;
			?>
			
			<!-- <div class=""> -->
			<div class="col-md-3 valid-coupon coupons ">
				<div class="coupon">
					
					<!-- Add winner option/partial -->
					<div class="flip-coupon-main">
						<div class="coupon-name"><?php if($value['pic']){ ?>
							<img src="<?php echo BASE_PATH.'timthumb.php?src=uploads/'.$value['pic']; ?>" style="width:100%; height:100%;" >
							<?php } else { ?>
							<img src="<?php echo BASE_PATH.'timthumb.php?src=uploads/coupon-icon.jpg' ?>" style="width:100%; height:100%;" >
							<?php } ?>
						</div>
						<div class="active" style="position: absolute;right: 0;top: 0;font-size: x-large;">
							<?php if($value['status']) 
							{echo '<i class="fa fa-fw fa-check-square-o text-green"></i>';}
							else{ 
								echo '<i class="fa fa-fw fa-square-o text-red"></i>';} ?>
							</div>  
							<div class="coupon-desc"><?php echo $value['coupon_name'];  ?>  </div>
							
							<div class="">
								<div class="coupon_code"><?php echo $value['coupon_code'];  ?></div>
							</div>
							
							<div class="col-md-12" style='padding-bottom: 8px;border-bottom: 1px solid #eaedef;'>
								<div class="col-md-4 "> <span class="save-text">SAVE</span>
									<p class="price"><?php if($value['value']) echo $value['value'].'$'; else echo $value['percentage'].'%';  ?><!--<span class="webRupee">%</span> --></p>
								</div>
								
								<div class="col-md-4"> <span class="save-text">USED</span>
									<p class="price"><?php if($res > $value['limit'])echo $value['limit']; else echo $res;  ?></p>
								</div>
								
								<div class="col-md-4"> <span class="save-text">LIMIT</span>
									<p class="price"><?php if($value['limit']=='99999999') echo 'Unlimited'; else echo $value['limit'];  ?></p>
								</div>
								
							</div> 
							<div class="info clearfix">
								<div class="edit-coupon">
									<a class="btn btn-default couponedit fa fa-pencil-square-o" vid="<?php echo $vid; ?>" coupon_id="<?php echo $value['id']; ?>" data-toggle="modal" data-target="#editcoupon" data-remote="true" data-no-turbolink ='true' ></a>
									<!--  <button class="coupondelete fa fa-remove" vid="<?php echo $vid; ?>" coupon_id="<?php echo $value['id']; ?>"></button> -->
								</div> 
								
								<p class="expiry"><span>Expiry Date</span> <br>
									<?php echo date('d-M-Y',strtotime($value['expiry_date'])); ?> 
								</p>
								<!-- </div> --><!-- pull-right -->
							</div><!-- info-clearfix -->
							

						</div> <!-- flip-coupon-main -->
					</div><!-- coupon -->
				<!-- </div> -->	
				</div> <!-- valid coupon -->
				<?php } ?>
			</div> 

		</div><!-- col-md-12 -->


	</div> <!-- row -->
	
</div><!-- container -->
</section>
</div>
</aside></div>

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
<script src="../assets/js/bootstrap-datepicker.js"></script>
<script src="../assets/js/bootstrap-switch.js"></script>
<script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script> 
</body>
</html>

<?php require_once "add_coupon.php"; ?>

<script>
	/*   $(document).ready(function(){
	  $('.sort_btn_grp').on('click','input',function(){
	  alert($('input[type="radio"]:selected').val());
	  alert( $("input[type='radio']:checked").val());
	  });
});*/
	$('.srt-btn-grp').on('click', 'button',function(){
		$('.srt-btn-grp button').removeClass('active');
		$(this).addClass('active');
		var btn_val=$(this).val();
		var r=$(location).attr('href');
		var href=r+"&filter='"+btn_val+"'";
		
	   //$(this).attr('onclick',  href );
	});
	
</script>

<script>
	
	$(".modalBtn").click(function(){
		var vid= $(this).data('vid');

		$(".modal-body #vcouponid").val(vid);
	});
	$("#expiry").datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd'
	});
</script>
<script>
	$('.edit-coupon').on('click','.couponedit',function(){
		
		var vid= $(this).attr('vid');
		var couponid=$(this).attr('coupon_id');
		var event='get-coupon';

		$.post("functions.php",
		{
			event: event,
			vid: vid,
			coupon_id: couponid
		},
		
		function(data){

			console.log(data);
			$('#coupon-editing1').empty();
			$.each(data, function(index,v) {
				if(v.status)
					v.status='checked';
				if(v.limit==99999999)
					v.limit='Unlimited';
	       // alert(value.name); // will alert each value
	       var field= ' <form action="functions.php" method="post" enctype="multipart/form-data"><div class="row"><div class="col-md-7"><div class="col-md-12 form-group" style="padding: 0px;"><label> Coupon Name </label><br><textarea name="coupon_name" class="form-control" >'+ v.coupon_name +' </textarea></div><div class="row form-group"><div class="col-md-6"><label> Limit </label><br><input type="text" name="limit" class="form-control" value="'+ v.limit +'" /></div> <div class="col-md-6"><label> Status </label><br><input name="status" class="status switch-radio2" checked data-radio-all-off="true" type="radio" ></div></div><div class="col-md-12 form-group" style="padding: 0px;"> <label> Expiry Date </label><br><input type="text" id="expiry" name="expiry" class="form-control" value="'+ v.expiry_date +'"/></div><div class="col-md-12 form-group coupon_val" style="padding: 0px;" id="coupon_val"><label> Coupon Value </label><br> <div style="display: -webkit-box;font-size: 20px;font-weight: bolder;"><input type="number" name="value" class="form-control coupon_value" value="'+ v.value +'" /><span class="input-group-addon" style="display: inline; padding: 8px 12px 9px 12px;">$</span>  </div></div><div class="col-md-12 or_div" style="text-align: center;font-size: 18px;"> OR </div><div class="col-md-12 form-group coupon_per" style="padding: 0px;" id="coupon_percent"> <label> Coupon Percentage</label><br><div style="display: -webkit-box;font-size: 20px;font-weight: bolder;"><input type="number" name="percentage" class="form-control coupon_percentage" value="'+ v.percentage +'" /><span class="input-group-addon" style="display: inline; padding: 8px 12px 9px 12px;">%</span></div> </div><div class="col-md-12 form-group" style="padding: 0px;"><input type="hidden" name="event" value="edit-coupon"><input type="hidden" name="venue_id" value="'+ v.venue_id +'"> <input type="hidden" name="coupon_id" value="'+ v.id +'"></div>  </div><div class="col-md-5 image-upload"><img src="../uploads/' +v.pic +'" class="def-image" style="width: 100%;height: 170px; padding: 3px;border: 1px solid rgb(213, 206, 206); border-radius: 4px;"><input type="file" name="image" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file"> <p class="text-yellow"> Image should be in the ratio of (3:2) </p> </div></div><div class="footer" style="text-align:center;"><button type="submit" class="btn btn-primary">Submit</button></div></form>';

	       $('#coupon-editing1').append(field).fadeIn(1000);
	   });
	
	extrafields();
	img_upload();
},"json"
);
	
});
	
	extrafields();
	img_upload();
	$('.edit-coupon').on('click','.coupondelete',function(){
		
		var vid= $(this).attr('vid');
		var couponid=$(this).attr('coupon_id');
		var event='delete-coupon';

		$.post("functions.php",
		{
			event: event,
			vid: vid,
			coupon_id: couponid
		},
		
		function(data){

			console.log(data);
			location.reload();
		//$('.all_coupons').empty();
		//$('.all_coupons').append('all_coupons.php').html();
	}
	);
		
	});   
	
	function extrafields(){
		$("[name='status']").bootstrapSwitch(); 
		$(":file").filestyle({buttonText: "Select File",input: false,size: "sm"});
		$("[name='expiry']").datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
		});   

	$('[name="value"]').change(function(){
	var val=$(this).val();
	//alert(val);
	if(val!=0){
	$('.coupon_per input').attr('value','0');
	$('.coupon_per').hide();
	$('.or_div').hide();
	}
	else{
	$('.coupon_per').show();
	$('.or_div').show();
	}
	});

	$('[name="percentage"]').change(function(){
	var val=$(this).val();
	if(val!=0){
	$('.coupon_val input').attr('value','0');
	$('.coupon_val').hide();
	$('.or_div').hide();
	}
	else{
	$('.coupon_val').show();
	$('.or_div').show();
	}
});
}
function img_upload(){
            $('.image-upload').on("change","input[type='file']",function () {
              // alert('hey');
              var files = this.files;
              var reader = new FileReader();
              name=this.value;
              var this_input=$(this);
              reader.onload = function (e) {

               this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100%').height(200);
             }
             reader.readAsDataURL(files[0]);
           });          
}


</script>
<div class="modal fade" id="editcoupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Edit Coupon</h4>
			</div>
			<div class="modal-body">
				<div class="" id="coupon-editing1">

				</div>
			</div>
		</div>
	</div>
</div>