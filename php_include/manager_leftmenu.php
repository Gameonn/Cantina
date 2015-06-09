<?php 
$sql="SELECT id from manager where token=:token and is_deleted=0";
$sth=$conn->prepare($sql);
$sth->bindValue("token", $key);
try{$sth->execute();}catch(Exception $e){}
$mgid=$sth->fetchAll();
$mid=$mgid[0]['id'];

$sql="SELECT venue_id from manager_venue where manager_id=:manager_id and is_live=1 and is_deleted=0";
$sth=$conn->prepare($sql);
$sth->bindValue("manager_id", $mid);
try{$sth->execute();}catch(Exception $e){}
$venueid=$sth->fetchAll();
$vid=$venueid[0]['venue_id'];

$sth=$conn->prepare("select url from pictures where venue_id=:venue_id and is_deleted=0");
$sth->bindValue("venue_id", $vid);
try{$sth->execute();}catch(Exception $e){}
$picture=$sth->fetchAll();
$url=$picture[0]['url'];
?>  
  
  <aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <ul class="sidebar-menu" id="leftmenu-ul">
      
      <?php if($vid){ ?>
        <li <?php if(stripos($_SERVER['REQUEST_URI'],"view_venue.php")) echo 'class="active"'; ?> >
          <a href="view_venue.php">
            <img src="../uploads/<?php echo $url; ?>" style='width:100%;' >
          </a>
        </li>
        <?php } ?>
      
        <li <?php if(stripos($_SERVER['REQUEST_URI'],"dashboard.php")) echo 'class="active"'; ?> >
          <a href="dashboard.php">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
		
	  <li class="treeview <?php if(stripos($_SERVER['REQUEST_URI'],"edit_profile.php") || stripos($_SERVER['REQUEST_URI'],"create_venue.php") || stripos($_SERVER['REQUEST_URI'],"view_venue.php")) echo 'active'; ?>" >
                            <a href="#">
                                <i class="fa fa-building-o"></i>
                                <span>Venue</span>
                                <i class="fa fa-angle-right pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"edit_profile.php")) echo 'class="active"'; ?>>
								<a href="edit_profile.php"><i class="fa fa-angle-double-right"></i>Venue Owner Profile</a></li>
                                <?php if(!$vid){ ?>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"create_venue.php")) echo 'class="active"'; ?>> 
								<a href="create_venue.php"><i class="fa fa-angle-double-right"></i>Create Venue</a></li>
                                <?php } else{ ?>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"view_venue.php")) echo 'class="active"'; ?>> 
								<a href="view_venue.php"><i class="fa fa-angle-double-right"></i>Venue Profile</a></li>
                                <?php } ?>
                            </ul>
                        </li>	
                       
                        <?php if($vid){ ?> 
              <li class="treeview <?php if(stripos($_SERVER['REQUEST_URI'],"serving.php") || stripos($_SERVER['REQUEST_URI'],"price_category.php") || stripos($_SERVER['REQUEST_URI'],"tax.php") || stripos($_SERVER['REQUEST_URI'],"menu_items.php") || stripos($_SERVER['REQUEST_URI'],"add_item.php") || stripos($_SERVER['REQUEST_URI'],"menu_dashboard.php")) echo 'active'; ?>">
                            <a href="#">
                                <i class="fa fa-signal"></i>
                                <span>Menu</span>
                                <i class="fa fa-angle-right pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"serving.php")) echo 'class="active"'; ?>><a href="serving.php"><i class="fa fa-angle-double-right"></i>Serving Sizes</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"price_category.php")) echo 'class="active"'; ?>><a href="price_category.php" style="display: inline-flex;"><i class="fa fa-angle-double-right"></i>Multiple Pricing Categories</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"tax.php")) echo 'class="active"'; ?>><a href="tax.php"><i class="fa fa-angle-double-right"></i>Tax</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"menu_items.php")) echo 'class="active"'; ?>><a href="menu_items.php"><i class="fa fa-angle-double-right"></i>Menu Levels</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"add_item.php")) echo 'class="active"'; ?>><a href="add_item.php"><i class="fa fa-angle-double-right"></i>Menu Creation</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"menu_dashboard.php")) echo 'class="active"'; ?>><a href="menu_dashboard.php" style="display: inline-flex;"><i class="fa fa-angle-double-right"></i>Menu Management</a></li>
                               
                            </ul>
                        </li>  
                        
                        
                  <li class="treeview <?php if(stripos($_SERVER['REQUEST_URI'],"add_staff.php") || stripos($_SERVER['REQUEST_URI'],"manage_staff.php")) echo 'active'; ?>">
                            <a href="#">
                                <i class="fa fa-th-large"></i>
                                <span>Staff</span>
                                <i class="fa fa-angle-right pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"add_staff.php")) echo 'class="active"'; ?>><a href="add_staff.php?key=<?php echo $key; ?>"><i class="fa fa-angle-double-right"></i>Onboarding</a></li>
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"manage_staff.php")) echo 'class="active"'; ?>><a href="manage_staff.php"><i class="fa fa-angle-double-right"></i>Management</a></li>
                               
                            </ul>
                        </li> 
                        
                   <li class="treeview <?php if(stripos($_SERVER['REQUEST_URI'],"coupons.php") ) echo 'active'; ?>">
                            <a href="#">
                                <i class="fa fa-tags"></i>
                                <span>Coupons</span>
                                <i class="fa fa-angle-right pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"coupons.php")) echo 'class="active"'; ?>><a href="coupons.php" style="display: inline-flex;"><i class="fa fa-angle-double-right"></i>Creation & Management</a></li>
                               
                            </ul>
                        </li> 
                        
                   <li class="treeview <?php if(stripos($_SERVER['REQUEST_URI'],"orders.php") ) echo 'active'; ?>">
                            <a href="#">
                                <i class="fa fa-xing"></i>
                                <span>Orders</span>
                                <i class="fa fa-angle-right pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php if(stripos($_SERVER['REQUEST_URI'],"orders.php")) echo 'class="active"'; ?>><a href="orders.php"><i class="fa fa-angle-double-right"></i>Orders Details</a></li>
                               
                            </ul>
                        </li> 
                        
                         <li <?php if(stripos($_SERVER['REQUEST_URI'],"feedback.php")) echo 'class="active"'; ?>>
		          <a href="feedback.php">
		            <i class="fa fa-compress"></i> <span>Feedback</span>
		          </a>
		        </li>    
                	  <?php }  ?>    
                	      
               <li <?php if(stripos($_SERVER['REQUEST_URI']," ")) echo 'class="active"'; ?>>
          <a href="../admin/eventHandler.php?event=manager-signout">
            <i class="fa fa-sign-out"></i> <span>LogOut</span>
          </a>
        </li>
          	         
      </ul>
      
    </section>
    <!-- /.sidebar -->
  </aside>  

