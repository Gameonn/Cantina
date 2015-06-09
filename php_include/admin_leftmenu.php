<aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <ul class="sidebar-menu" id="leftmenu-ul">
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"dashboard.php")) echo 'class="active"'; ?>>
                            <a href="dashboard.php">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"create_user.php")) echo 'class="active"'; ?> >
                            <a href="create_user.php" style="display: inline-flex;">
                                <i class="fa fa-user" style="margin-top: 10px;"></i> <span>Create Venue Owner Account</span>
                            </a>
                        </li>
						
						
                        <li <?php if(stripos($_SERVER['REQUEST_URI'],"venues.php")) echo 'class="active"'; ?>>
                            <a href="venues.php">
                                <i class="fa fa-building-o"></i> <span>Venues</span>
                            </a>
                        </li>
                        <li <?php if(stripos($_SERVER['REQUEST_URI'],"manager_details.php")) echo 'class="active"'; ?>>
                            <a href="manager_details.php">
                                <i class="fa fa-user"></i> <span>Managers</span>
                            </a>
                        </li>
                        </li>
                        
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"customers.php")) echo 'class="active"'; ?>>
                            <a href="customers.php">
                                <i class="fa fa-users"></i> <span>Customers</span>
                            </a>
                        </li>
                        
                        <li <?php if(stripos($_SERVER['REQUEST_URI'],"cusines.php")) echo 'class="active"'; ?>>
                            <a href="cusines.php">
                                <i class="fa fa-cutlery"></i> <span>Cusines</span>
                            </a>
                        </li>
                        
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"orders.php")) echo 'class="active"'; ?>>
                            <a href="orders.php">
                                <i class="fa fa-tasks"></i> <span>Orders</span>
                            </a>
                        </li>
                        <li <?php if(stripos($_SERVER['REQUEST_URI']," ")) echo 'class="active"'; ?>>
						  <a href="../admin/eventHandler.php?event=signout">
							<i class="fa fa-sign-out"></i> <span>Sign Out</span>
						  </a>
						</li>
                       
                      
						
                    </ul>
										
                </section>
                <!-- /.sidebar -->
            </aside>