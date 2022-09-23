<?php
session_start();
//print_r($_SESSION);die;
if(isset($_SESSION['manager']) && isset($_SESSION['manager']['id'])){
}
else{
	$success=0;
	//session_destroy();
	$msg="Signed Out! Sign In Again!";
	header("Location: index.php?success=$success&msg=$msg");
}
?>
<?php 

$username=$_SESSION['manager']['username'];
	$sth=$conn->prepare("select * from manager where username=:username");
	$sth->bindValue("username",$username);
		
	try{$sth->execute();}catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
		$key=$result[0]['token'];
		

 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gambay| Venue Owner</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
                <!-- Theme style -->
        <link href="../assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap 3.0.2 -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="../assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
			 <link href="../assets/css/sweetalert.css" rel="stylesheet">
        <!-- Ionicons -->
        <link href="../assets/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="../assets/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="../assets/css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="../assets/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="../assets/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="../assets/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
	  <link href="../assets/css/datepicker.css" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="../assets/css/jquery.custom-scrollbar.css"/>
        <link href="../assets/css/iCheck/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
		<!-- MY ICONS 
		<link href="../assets/css/Flaticons/flaticon.css" rel="stylesheet" type="text/css" /> -->
		<!-- MY style
        <link href="../assets/css/override-style.css" rel="stylesheet" type="text/css" />  -->
        <style>
	 a {
	text-decoration: none!important;
	}
        </style
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesnot work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="dashboard.php" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
             <img src='../uploads/Logo(Splash)@2x.png'>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span style="text-transform: capitalize;">
                                <?php echo $username; ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu" style="min-width:160px;width:160px;">
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="">
                                   
                                    						<input class="btn btn-default btn-flat btn-block" id="2" type="button" value="Add Staff" 
											onclick="window.location='<?php echo BASE_PATH; ?>venue/add_staff.php?key=<?php echo $key; ?>'">

										
										<input class="btn btn-default btn-flat btn-block" id="2" type="button" value="Change Password" 
											onclick="window.location='<?php echo BASE_PATH; ?>venue/changePassword.php?key=<?php echo $key; ?>'">
											
										<input class="btn btn-default btn-flat btn-block" id="1" type="button" value="Sign Out" 
											onclick="window.location='<?php echo BASE_PATH; ?>admin/eventHandler.php?event=manager-signout'">
										
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

        </header>

       