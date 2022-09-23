<?php require_once "../php_include/db_connection.php"; ?>
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$key=$_REQUEST['key'];

$sth=$conn->prepare('select * from manager where token=:token');
$sth->bindValue('token',$key);
try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
if(count($result)){
	$mid=$result[0]['id'];
$sth=$conn->prepare('select venue_id from manager_venue where manager_id=:manager_id');
$sth->bindValue('manager_id',$mid);
try{$sth->execute();}catch(Exception $e){}
$res=$sth->fetchAll(PDO::FETCH_ASSOC);		
	$vid=$res[0]['venue_id'];	
	
		}
else{
$msg="Invalid Token";
}
?>

<!DOCTYPE html>
<html>
<head>
		<meta charset="UTF-8">
		<title>Add Staff</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
	</head>
	<body class="bg-black">

        <div class="form-box" id="login-box">
            <div class="header">Add Staff Member</div>
			<form action="../admin/eventHandler.php" method="post" autocomplete='off'>
                <div class="body bg-gray">
                	<?php //error div
                	if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
                		<div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
			            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			            	<?php echo $_REQUEST['msg']; ?>
			            </div>
			        <?php } // --./ error -- ?>
			        
		     <div class="form-group">
		     <label> Username </label><br>
                        <input type="username" name="username" class="form-control" placeholder="Username" required/>
					</div>
					
                    <div class="form-group">
                    <label> Email </label><br>
                        <input type="email" name="email" class="form-control" placeholder="Email" autocomplete="off" required/>
					</div>
		
					
		 <div class="form-group">
		 <label> Mobile Number </label><br>
                        <input type="mobile" name="mobile" class="form-control" placeholder="Mobile Number" autocomplete="off" required/>
					</div>
                              
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block" onclick="return 0;">Submit</button>
                    <a class="btn bg-red btn-block" href="dashboard.php">Cancel</a>
                </div>
                <!-- hidden -->
                <input type="hidden" name="event" value="add-staff">
                <input type="hidden" name="venue_id" value="<?php echo $vid; ?>">
                <input type="hidden" name="redirect" value="manage_staff.php">
            </form>
        </div>
        
        


        <!-- jQuery 2.0.2 -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>        

    </body>
</html>