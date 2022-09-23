<?php require_once "../php_include/db_connection.php"; ?>


<?php

$key=$_REQUEST['key'];

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
<head>
    <meta charset="UTF-8">
    <title>Owner Signup</title>
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
        <div class="form-box" id="login-box">
            <div class="header">Venue Owner Signup</div>
      <form action="../admin/eventHandler.php" method="post" enctype="multipart/form-data">
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
                        <input type="username" name="username" class="form-control" value="<?php echo $username; ?>" readonly />
          </div>
          
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" readonly />
          </div>
		  
		              <div class="form-group">
                        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>"  />
          </div>
          
         <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required/>
          </div>
          
         <div class="form-group">
                        <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required/>
          </div>
         <div class="form-group">
                        <input type="mobile" name="mobile" class="form-control" placeholder="Mobile Number" data-inputmask='"mask": "(999) 999-9999"' data-mask required/>
          </div>
                                       
                </div>

                 <div class="col-md-5 image-upload">
                <img src="../uploads/default-profilepic.jpg" class="def-image" style="margin-top: 15px;width: 100%;height: 200px; padding: 3px;border: 1px solid rgb(213, 206, 206);
    border-radius: 4px;">
	 <input type="file" name="pic" class="filestyle" data-input="false" data-size="sm" data-buttonText="Select file">
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block" onclick="return 0;">Submit</button>
                    <input type="button" class="btn bg-red btn-block" onclick="window.history.back()" value="Cancel">
                </div>
                <!-- hidden -->
                <input type="hidden" name="event" value="manager-signup">
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

               this_input.parent('.image-upload').find(".def-image").attr('src', e.target.result).width('100%').height(200);
             }
             reader.readAsDataURL(files[0]);
           });

          </script>