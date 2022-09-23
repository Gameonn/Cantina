<?php require_once "../php_include/db_connection.php";
session_start();
 ?>
<?php require_once "../GeneralFunctions.php"; ?>
  
<?php

$obj = new GeneralFunctions; 
$username=$_SESSION['manager']['username'];
	$sth=$conn->prepare("select * from manager where username=:username");
	$sth->bindValue("username",$username);
		
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
		$key=$result[0]['token'];

$sth=$conn->prepare('select * from manager where token=:token');
$sth->bindValue('token',$key);
try{$sth->execute();}catch(Exception $e){}
  $result=$sth->fetchAll(PDO::FETCH_ASSOC);
  
if(count($result)){
    $username=$result[0]['username'];
    $email=$result[0]['email']; 
	$name=$result[0]['name']; 
    }
else{
$msg="Invalid Token";
}
?>
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    
    <title> Edit Profile </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />

  </head>
  <body class="bg-black">
		<div class="col-md-12">
        <div class="form-box" id="login-box" >
            <div class="header">Edit Profile</div>
      <form action="functions.php" method="post" enctype="multipart/form-data">
                <div class="body bg-gray">
                  <?php //error div
                  if(isset($_REQUEST['success']) && isset($_REQUEST['msg']) && $_REQUEST['msg']){ ?>
                    <div style="margin:0px 0px 10px 0px;" class="alert alert-<?php if($_REQUEST['success']) echo "success"; else echo "danger"; ?> alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $_REQUEST['msg']; ?>
                  </div>
              <?php } // --./ error -- ?>
              
         <div class="col-md-7">     
         <div class="form-group" style="margin-top: 15px;">
		  <label> Username </label><br>
                        <input type="username" name="username" class="form-control" value="<?php echo $username; ?>" readonly />
          </div>
          
                    <div class="form-group" style="margin-bottom: 7px;">
					 <label> Email </label><br>
                        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" readonly />
          </div>
		  
		         <div class="form-group" style="margin-bottom: 7px;">
					 <label> Name </label><br>
                        <input type="name" name="name" class="form-control" value="<?php echo $name; ?>"  />
          </div>

         <div class="form-group" style="margin-bottom: 7px;">
		  <label> Mobile Number</label><br>
                        <input type="mobile" name="mobile" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask  value="<?php echo $result[0]['mobile_number']; ?>" required/>
          </div>
                                       
                </div>

                 <div class="col-md-5 image-upload">
				 <?php if($result[0]['pic']) {?>
				   <img src="<?php echo $obj->getImagePath($result[0]['pic']) ?>" class="def-image" style="width: 100%; height:auto;padding: 3px;border: 1px solid rgb(213, 206, 206);
                  border-radius: 4px;">
				  <?php } else { ?>
                <img src="../uploads/default-profilepic.jpg" class="def-image" style="margin-top: 15px;width: 100%;height: auto; padding: 3px;border: 1px solid rgb(213, 206, 206);
    border-radius: 4px;"> 
			<?php } ?>
	 <input type="file" name="pic" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
	 <p class="text-yellow"> Image should be in the ratio of (3:2) </p>
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block" onclick="return 0;">Submit</button>
                    <a class="btn bg-red btn-block" href="dashboard.php" > Back</a>
                </div>
                <!-- hidden -->
                <input type="hidden" name="event" value="edit-profile">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="username" value="<?php echo $username; ?>">
                
                <input type="hidden" name="key" value="<?php echo $key; ?>">
                <input type="hidden" name="redirect" value="dashboard.php">         
        </div>
        </form>
        </div>
		</div>

        <!-- jQuery 2.0.2 -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>   
   <script src="../assets/js/bootstrap-filestyle.js" type="text/javascript"></script>   
           <!-- InputMask -->
   <script src="../assets/js/jquery.inputmask.js" type="text/javascript"></script>
   <script src="../assets/js/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
   <script src="../assets/js/jquery.inputmask.extensions.js" type="text/javascript"></script>  

</body>
</html>

 <script type="text/javascript">
$("[data-mask]").inputmask();
            $('.image-upload').on("change","input[type='file']",function () {
              // alert('hey');
              var files = this.files;
              var reader = new FileReader();
              name=this.value;
              var this_input=$(this);
              reader.onload = function (e) {

               this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100%').height('auto');
             }
             reader.readAsDataURL(files[0]);
           });

          </script>