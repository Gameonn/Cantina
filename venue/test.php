<?php session_start();?>
<?php require_once "../php_include/db_connection.php"; ?>
<?php require_once "../php_include/manager_header.php"; ?>
<?php require_once "../php-excel/class-excel-xml.inc.php"; ?>

<?php
 error_reporting(E_ALL);
function getxls(){

	if($_POST['export_content'])){
	$filename=time().'.xls';
	
	header("Content-Type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=".$filename);
	}
	}
		?>
<?php
    error_reporting(E_ALL);
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
  
  $sql="select id,name from pricing_names where venue_id=:venue_id and id IN (select pricing.pricing_name_id from pricing join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0 where pricing.venue_id=:venue_id)";
$sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $namings=$sth->fetchAll();
  
  //level1 main-categories
  $sql="SELECT * from menucategory where menucategory.venue_id=:vid and is_live=1 and is_deleted=0 order by id DESC";
  $sth=$conn->prepare($sql);
  $sth->bindValue("vid",$vid);
  try{$sth->execute();}catch(Exception $e){ }
  $menus=$sth->fetchAll();

  $sql="SELECT menucategory.*,(select group_concat(tax.tax_name separator '<br>') from tax left join category_tax on category_tax.tax_id=tax.id where category_tax.menucategory_id=menucategory.id and category_tax.is_deleted=0) as tax_name,(select group_concat(servings.type separator ',') from servings) as serving from menucategory where menucategory.venue_id=:venue_id and menucategory.is_live=1 and menucategory.is_deleted=0";

  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){echo $e->getMessage();}
  $venuecategories=$sth->fetchAll();

  $sql="select l1.id as l1_id, l1.name  as l1_name, l2.id as l2_id, l2.name as l2_name, l3.id as l3_id, l3.name as l3_name, l4.id as l4_id, l4.name as l4_name, l5.id as l5_id, l5.name as l5_name, l6.id as l6_id, l6.name as l6_name, item.id as item_id, item.name as item_name, item.pic, servings.type as serving, pricing.quantity as qty,pricing.status,pricing.special_flag, pricing_names.name as pr_name, serving_price.serving_id as serve_id, serving_price.item_price,(select group_concat(tax.tax_name separator ',') from pricing_tax join tax on tax.id=pricing_tax.tax_id where pricing_tax.serving_price_id=serving_price.id) as tax_name ,  (select group_concat(item_tax.percentage separator ',') from item_tax where item_tax.item_id=item.id) as tax_percent ,TRUNCATE((serving_price.agg_price),2) as agg_price
  from menucategory as l1 left outer join subcategory as l2  on l2.menucategory_id = l1.id and l2.parent_id=0 and l2.is_live=1 and l2.is_deleted=0 left outer join subcategory as l3
    on l3.menucategory_id=l1.id and l3.parent_id = l2.id and l3.is_live=1 and l3.is_deleted=0 left outer join subcategory as l4 on l4.menucategory_id=l1.id and l4.parent_id = l3.id and l4.is_live=1 and l4.is_deleted=0 left outer join subcategory as l5 on l5.menucategory_id=l1.id and l5.parent_id = l4.id and l5.is_live=1 and l5.is_deleted=0
left outer join subcategory as l6 on l6.menucategory_id=l1.id and l6.parent_id = l5.id and l6.is_live=1 and l6.is_deleted=0 left outer join item on item.menucategory_id=l1.id and (item.parent_id=l2.id or item.parent_id=l3.id or item.parent_id=l4.id or item.parent_id=l5.id or item.parent_id=l6.id) and item.is_live=1 and item.is_deleted=0 join pricing on pricing.item_id=item.id join pricing_names on pricing_names.id=pricing.pricing_name_id join serving_price on serving_price.pricing_id=pricing.id and serving_price.is_deleted=0
join servings on servings.id=serving_price.serving_id where l1.venue_id=:venue_id and l1.is_live=1 and l1.is_deleted=0 
order by l1_name, l2_name, l3_name, l4_name, l5_name, l6_name";
  $sth=$conn->prepare($sql);
  $sth->bindValue("venue_id",$vid);
  try{$sth->execute();}catch(Exception $e){}
  $data=$sth->fetchAll();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Gambay| Menu Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Category Block Design -->

    <link rel="stylesheet" href="../assets/css/my.css" type="text/css" />  
	
  </head>
  <style>
    .modal-title {text-align: center!important;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
      text-align: center; vertical-align: middle; }
      .fa {        line-height: 1.5;}
		.btn-check{	background-color: rgba(0,0,0,0.3);	border-color: rgba(0,0,0,0.1);	outline: none!important;
	color: rgba(255,255,255,0.5)!important;	font-size: 15px;padding: 8px 10px;border-radius: 2px;}

.btn-check:hover, .btn-check.active:hover{color: white!important;outline: none!important;}
.btn-check .facheck, .btn-check.active .fauncheck{display: none;}

.btn-check .fauncheck, .btn-check.active .facheck{	display: block;}

.btn-check.active{background-color: #74B550;color: rgba(255,255,255,1)!important;border-color: #63A83D;	outline: none!important;color: white;}
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
                Menu Listing
              </h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#"><i class="fa fa-suitcase"></i> Menu Dashboard</a></li>
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
              
              <div class="col-xs-12">
				<div class="box">
				<div class="box-header">
						<h3 class="box-title">Menu Listing</h3>
						<div class="box-tools" style="text-align: right;  padding-right: 20px;padding-bottom: 0px;padding-top: 0px;">
						
						 <div class="btn-group" style="margin-top:10px;margin-left:10px;text-align: center;">
			<button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-tags"></i> Pricing Category</button> 
					<ul class="dropdown-menu price_naming_dropdown" role="menu">
					<?php foreach($namings as $name){ ?>
						<li>
						<a href="#" nid="<?php echo $name['id']; ?>" venue_id="<?php echo $vid; ?>" ><?php echo $name['name']; ?></a>
						</li>				
						<?php } ?>
				<!-- <li><a href="#" onclick="$('#summary_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});">PDF</a></li> -->
					</ul>
				</div>
				
				<div class="btn-group" style="margin-top:10px;margin-left:10px;text-align: center;">
				<!--<button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-tags"></i> Export Table Data</button> 
			-->
				<form method="post" action="test.php">
				<input type="hidden" name="export_content" value="<?php echo $data; ?>"> 
				<input type="submit" name="submit" class="btn btn-primary btn-sm" >
				<i class="fa fa-bars"></i> Export Table Data</a> 
				</form> 
					<!-- <ul class="dropdown-menu " role="menu">
						<li><a href="#" onclick="$('#summary_table').tableExport({type:'csv',escape:'false'});"><i class="fa fa-file"></i>CSV</a></li>				
						<li><a href="#" onclick="$('#summary_table').tableExport({type:'excel',escape:'false'});"><i class="fa fa-file-text-o"></i>XLS</a></li>
				<!-- <li><a href="#" onclick="$('#summary_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});"><i class="fa fa-file-pdf-o"></i>PDF</a></li> -->
				<!--	</ul> -->
				</div>
						</div>
					</div>
					 <div class="box-body table-responsive no-padding">
						<table id="summary_table" class="table table-bordered table-hover">
					   
							<tr>
								<th>ID</th>
								<th>Category<br>(Level 0)</th>
								<th>Category<br>(Level 1)</th>
								<th>Category<br>(Level 2)</th>
								<th>Category<br>(Level 3)</th>
								<th>Category<br>(Level 4)</th>
								<th>Category<br>(Level 5)</th>
								<th>Item Name</th>
								<th>Serving Size</th>
								<th>Pricing Category</th>
								<!--<th>Tax</th> -->
								<th>Special Flag</th>
								<th>Status</th>
								<th>Item Price</th>
								<th>Aggregate Price</th>
								<th> </th>
							</tr>
							<?php  $r=1;
							foreach($data as $row){ 
							$chk=($row['special_flag'])?active:'';
							$chk1=($row['status'])?active:'';
							$txn_explode=explode(',',$row['tax_name']);
							$txp_explode=explode(',',$row['tax_percent']);
							$c=sizeof($txp_explode);
							?> 
							<tr>
								<td><?php echo $r; ?></td>
								<td><?php echo $row['l1_name']; ?></td>
								<td><?php if($row['l2_name']) echo $row['l2_name']; else echo '-';?></td>
								<td><?php if($row['l3_name']) echo $row['l3_name']; else echo '-'; ?></td>
								<td><?php if($row['l4_name']) echo $row['l4_name']; else echo '-'; ?></td>
								<td><?php if($row['l5_name']) echo $row['l5_name']; else echo '-'; ?></td>
								<td><?php if($row['l6_name']) echo $row['l6_name']; else echo '-'; ?></td>
								<td><a href="multiple_pricing.php?item_id=<?php echo $row['item_id']; ?>" style='text-decoration: none;' class="item_pricing" item_id="<?php echo $row['item_id']; ?>" serving_id="<?php echo $row['serve_id']; ?>"><?php if($row['item_name']) echo $row['item_name']; else echo '-'; ?></a></td>
								<td><?php if($row['serving']) echo $row['serving']; else echo '-'; ?></td>
								<td><?php if($row['pr_name']) echo $row['pr_name']; else echo '-';?></td>
								<!-- <td><table><?#php for($i=0;$i<$c;$i++){ ?>
									<tr><td><?#php if($txn_explode[$i]) echo $txn_explode[$i]; else echo '-';?></td>
									<td><?#php if($txp_explode[$i]) echo $txp_explode[$i]; else echo '0';?> </td>
									</tr>
									<?#php } ?>
									</table>
									</td> -->
								<td><button type="button" class="btn btn-check <?php echo $chk; ?> checkToggle" serving-id="<?php echo $row['serve_id'];?>"  item-id="<?php echo $row['item_id']; ?>" table="specials" flag="<?php echo $row['specials'];?>" data-toggle="button">
								<i class="fa  fa-check facheck"></i> <i class="fa  fa-circle-o fauncheck"></i> 
								</button></td>
								<td><button type="button" class="btn btn-check <?php echo $chk1; ?> checkToggle" serving-id="<?php echo $row['serve_id'];?>"  item-id="<?php echo $row['item_id']; ?>" table="status" flag="<?php echo $row['status'];?>" data-toggle="button">
								<i class="fa  fa-check facheck"></i> <i class="fa  fa-circle-o fauncheck"></i> 
								</button></td>
								<td><?php if($row['item_price']) echo $row['item_price']; else echo '-'; ?></td>
								<td><?php if($row['agg_price']) echo $row['agg_price']; else echo '-'; ?></td>
								<td> <a href="#" item="<?php echo $row['item_id']; ?>" serving="<?php echo $row['serve_id']; ?>" class="fa fa-trash-o user_del" style='text-decoration: none;color: #9A9CBB;' ></a> </td>
							</tr>
							<?php
							$r++;
							 } ?>
						 </table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
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
<script src="../assets/js/sweet-alert.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="../assets/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<!-- iCheck -->
<script src="../assets/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../assets/js/html2canvas.js"></script>
<script type="text/javascript" src="../assets/js/tableExport.js"></script>
<script type="text/javascript" src="../assets/js/jquery.base64.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="../assets/js/jspdf/libs/base64.js"></script>
			<script type="text/javascript" src="../assets/js/data-confirm-modal.js"></script>
<!-- AdminLTE App -->
<script src="../assets/js/AdminLTE/app.js" type="text/javascript"></script>
<!-- Category Block effect -->

<script>
$('.price_naming_dropdown').on('click','li a',function(){

var venue_id=$(this).attr('venue_id');
var naming_id=$(this).attr('nid');
var event='set-pricing';
  $.post('functions.php', 
  {
  event: event,
  venue_id: venue_id, 
  naming_id: naming_id
  }, 
  function(data) {
       console.log(data);
     location.reload();
   });
});

	$('.checkToggle').click(function(){

    var flag= $(this).attr('flag');
	var it_id= $(this).attr('item-id');
	var ser_id= $(this).attr('serving-id');
	var field= $(this).attr('table');
	var event='set-status';
    $(this).attr('flag', toggleFlag(flag));

	// ajax request
    $.post("functions.php",
        {
            flag: toggleFlag(flag),
			event: event,
			field: field,
            item_id: it_id,
            serving_id: ser_id
        },
            function(data){
			console.log(data);
                if(!data.error){  
                    swal({ title:"Success!",
                            text: "Changes saved successfully!",
							confirmButtonClass: "btn-primary",
                            type: "success",
                            timer: 1500
                        }); 
                      
                }
                else{
                    $(current).attr('flag', flag);
                    $(current).toggleClass('active');
                	swal({ title:"Oops!",
                            text: "Changes could not be saved!",
							confirmButtonClass: "btn-danger",
                            type: "error",
                            timer: 1500
                        });
                }           
            }
        ); 
});		  
	
function toggleFlag(flag){

    if(flag == 0){
        return 1;
    }
    else if(flag == 1){
        return 0;
    }
}
	</script>
	
         <script>
		$('.user_del').on('click', function () {
		
		var item=$(this).attr('item');
		var serving=$(this).attr('serving');
	
	dataConfirmModal.confirm({
		        title: 'Are you sure?',
		        text: 'Removing this will remove all related information',
		        commit: 'Yes do it',
		        cancel: 'Not really', 
		
		        onConfirm: function () {
		            var event='delete-item-serving';

			    $.post("functions.php",
			    {
			    event: event,
			    item: item,
				serving: serving
			    },
			    
			    function(data){
			    console.log(data);
			    location.reload();
			    }
			    );   
		           
		           
		        },
		        onCancel: function () {
		           
		        }
		    });
		});
	   </script>

  </body>
  </html>
          