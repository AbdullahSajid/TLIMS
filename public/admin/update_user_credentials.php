<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php
access_admin();
if(isset($_GET["id"])){
    $user = find_user_by_id($_GET["id"]);
    if (!$user) {
    // admin ID was missing or invalid or 
    // admin couldn't be found in database
    redirect_to("manage_users.php");
  }
}
else{
    redirect_to("manage_users.php");
}
?>
<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  $fields_with_max_lengths = array("full_name" => 30,"username" => 30,
                                  "password"=>30,"email"=>100);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    // Perform Create
    $id = $_GET["id"];
    $full_name = mysql_prep($_POST["full_name"]);
    $user_name = mysql_prep($_POST["username"]);
    $pass_word = mysql_prep($_POST["password"]);
    $email = mysql_prep($_POST["email"]);
    $privileges = mysql_prep($_POST["privileges"]);  
      
    $result = update_user($id,$full_name,$user_name,$pass_word,$email,$privileges);

    if ($result) {
       if(isset($_POST["send_email"])){
          // sent email to new user
          require_once("./operations/send_email_to_user.php");
          if(isset($is_email_sent)&&$is_email_sent!=0){
               $_SESSION["message"] = "User credentials updated and Email sent.";
               redirect_to("manage_users.php?success=1"); 
          }
          else{
              $_SESSION["message"] = "User credentials updated but email sending failed.";
              redirect_to("manage_users.php?success=1");
          }
        }
        else{
             $_SESSION["message"] = "User Credentials Updated.";
             redirect_to("manage_users.php?success=1");
        }
      
    } else {
      // Failure
      $_SESSION["message"] = "User Updation Failed.";
      redirect_to("manage_users.php?success=0");
    }
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8"/>
    <title>Update User</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="description" content="NTRC Labs Information Management System"/>
    <meta name="author" content="Abdullah Sajid, Phone# 03012745906"/>

       <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link href="../assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../assets/css/animate.min.css" rel="stylesheet"/>
    <link href="../assets/css/style.css" rel="stylesheet"/>
    <link href="../assets/css/style-responsive.css" rel="stylesheet"/>
    <link href="../assets/css/theme/default.css" rel="stylesheet" id="theme"/>
    <link href="../assets/css/essential.css" rel="stylesheet"/>
    <!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
    <link href="../assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet"/>
    <link href="../assets/plugins/parsley/src/parsley.css" rel="stylesheet"/>
    <!-- ================== END PAGE LEVEL STYLE ================== -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="../assets/plugins/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body>
<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->
<!-- begin #page-container -->
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <!-- begin #header -->
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
        <!-- begin container-fluid -->
        <div class="container-fluid">
            <!-- begin mobile sidebar expand / collapse button -->
            <div class="navbar-header">
                <a href="index.php" class="navbar-brand"><img src="../assets/img/ntrc.png" alt="logo">&nbsp;&nbsp;NTRC TLIMS</a>
                <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- end mobile sidebar expand / collapse button -->

            <!-- begin header navigation right -->
            <ul class="nav navbar-nav navbar-right hidden-xs">
                <li>
                    <form class="navbar-form full-width">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Enter keyword for search"/>
                            <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14 messages_indicator_icon">
                    </a>
                    <ul class="dropdown-menu media-list pull-right animated fadeInDown recent_messages_list">
                    </ul>
               </li>
                <li class="dropdown navbar-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                       <?php $path = get_user_picture_path('admin');?>
                        <img src="<?php echo $path; ?>" alt="admin picture"/>
                        <span class="hidden-xs">Dr. Ahsan</span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                        <li><a href="index.php">Home Page</a></li>
                        <li><a href="edit_password.php">Edit Password</a></li>
                        <li><a href="change_display_picture.php">Change Display Picture</a></li>
                        <li><a href="lims_calender.php">Calendar</a></li>
                        <li><a href="../faq.html">FAQ</a></li>
                        <li class="divider"></li>
                        <li><a href="../logout.php">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
            <!-- end header navigation right -->
        </div>
        <!-- end container-fluid -->
    </div>
    <!-- end #header -->
    
    <!-- begin #sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- begin sidebar scrollbar -->
        <div data-scrollbar="true" data-height="100%">
            <!-- begin sidebar user -->
            <ul class="nav">
                <li class="nav-profile">
                    <div class="image">
                        <a href="javascript:;"><img src="<?php echo $path; ?>" alt="admin picture"/></a>
                    </div>
                    <div class="info">
                        Administrator
                        <small>Director ORIC</small>
                    </div>
                </li>
            </ul>
            <!-- end sidebar user -->
            <!-- begin sidebar nav -->
            <ul class="nav">
                <li class="nav-header">Navigation</li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret pull-right"></b>
                        <i class="fa fa-laptop"></i>
                        <span>Dashboard</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="index.php">Home</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-inbox"></i>
                        <span class="messages_indicator">Messages</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="send_message.php">Compose</a></li>
                        <li><a href="message_inbox.php">Inbox</a></li>
                        <li><a href="message_outbox.php">Outbox</a></li>
                        <li><a href="message_draft.php">Draft</a></li>
                        <li><a href="message_trash.php">Trash</a></li>
                    </ul>
                </li>
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-user"></i>
                        <span>Manage Users</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="manage_users.php">Users Detail</a></li>
                        <li><a href="add_user.php">Add New User</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-file-text-o"></i>
                        <span>Tests Catalog</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="tests_detail.php">Tests Detail</a></li>
                        <li><a href="manage_tests_detail.php">Manage Test Prices</a></li>
                        <li><a href="add_test.php">Add New Test</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-group"></i>
                        <span>Manage Customers</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="view_customer_sample_record.php">View Customer Record</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-filter"></i>
                        <span>Filter Customers</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="view_academic_customers.php">Academic Customers</a></li>
                        <li><a href="view_commercial_customers.php">Commercial Customers</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-spinner"></i>
                        <span class="pending_samples_indicator">Sample Status</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="pending_samples_sub_indicator" href="pending_samples.php">Pending Samples</a></li>
                        <li><a href="delayed_samples.php">Delayed Samples</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-file-pdf-o"></i>
                        <span class="pending_reports_indicator">Reports</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="pending_reports_sub_indicator" href="pending_reports.php">Pending Reports</a></li>
                        <li><a href="completed_reports.php">Completed Reports</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Sales</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="sales_statistics.php">Sales Statistics</a></li>
                        <li><a href="sales_statistics_charts.php">Sales Statistics Chart</a></li>
                        <li><a href="customers_statistics_charts.php">Customers Statistics Chart</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-cogs"></i>
                        <span>Privacy Settings</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="edit_password.php">Change Password</a></li>
                        <li><a href="change_display_picture.php">Change Display Picture</a></li>
                    </ul>
                </li>
                <li>
                    <a href="../logout.php">
                        <i class="fa fa-sign-out"></i>
                        <span>Sign Out</span>
                    </a> 
                </li>
                
                <!-- begin sidebar minify button -->
                <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i
                        class="fa fa-angle-double-left"></i></a></li>
                <!-- end sidebar minify button -->
            </ul>
            <!-- end sidebar nav -->
        </div>
        <!-- end sidebar scrollbar -->
    </div>
    <div class="sidebar-bg"></div>
    <!-- end #sidebar --> 
    <!-- begin #content -->
    <div id="content" class="content">
       <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Manage Users</a></li>
            <li class="active">Update User</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Update User
            <small>Update system users</small>
        </h1>
        <!-- end page-header -->
         <!-- begin row -->
        <div class="row">
            <!-- begin col-6 -->
            <div class="col-md-6">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-validation-1">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default"
                               data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success"
                               data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                               data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                               data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">Update User : <?php echo htmlentities($user["name"]); ?></h4>
                    </div>
        
                    <!-- start notification  -->
                    <?php 
//                 display form errors if any
                    echo form_errors($errors);
//                 display query errors or query success if any
                    echo query_status(FALSE); // not needed here
                    ?>
                    <!-- end notification -->
                    <div class="panel-body panel-form">
                        <form class="form-horizontal form-bordered" data-parsley-validate="true" name="demo-form" action="update_user_credentials.php?id=<?php echo urlencode($user["id"]);?>" method="post">
                           <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="fullname">Full Name * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" id="fullname" name="full_name"
                                        placeholder="Full Name" value="<?php echo $user["name"]; ?>" data-parsley-required="true"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="fullname">Username * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" id="username" name="username"
                                           placeholder="Username" value="<?php echo $user["username"]; ?>" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="password">Password * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="password" id="password" name="password"
                                           placeholder="Password" value="<?php echo $user["password"]; ?>" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="email">Email * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="email" id="email" name="email"
                                           placeholder="Email" value="<?php echo $user["email"]; ?>" data-parsley-required="true"/>
                                </div>
                            </div>
                           
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4">Privileges * :</label>
                                <div class="col-md-6 col-sm-6">
                            <select class="form-control" id="select-required" name="privileges" data-parsley-required="true">
                              <option value="">Select Privilege</option>
                              <option value="reception" <?php if($user["privileges"]==='reception'){echo "selected";} ?>>Reception</option>
                              <option value="mechanical_lab" <?php if($user["privileges"]==='mechanical_lab'){echo "selected";} ?> >Mechanical Lab</option>
                              <option value="spectroscopy_lab" <?php if($user["privileges"]==='spectroscopy_lab'){echo "selected";} ?>>Spectroscopy Lab</option>
                              <option value="comfort_lab" <?php if($user["privileges"]==='comfort_lab'){echo "selected";} ?>>Comfort Lab</option>
                              <option value="anti_microbial_lab" <?php if($user["privileges"]==='anti_microbial_lab'){echo "selected";} ?>>Antimicrobial Testing Lab</option>
                              <option value="applied_chemistry_lab" <?php if($user["privileges"]==='applied_chemistry_lab'){echo "selected";} ?>>Applied Chemistry Lab</option>
                              <option value="chemistry_lab" <?php if($user["privileges"]==='chemistry_lab'){echo "selected";} ?>>Chemistry Lab</option>
                              <option value="coating_lab" <?php if($user["privileges"]==='coating_lab'){echo "selected";} ?>>Coating Lab</option>
                              <option value="composite_characterization_lab" <?php if($user["privileges"]==='composite_characterization_lab'){echo "selected";} ?>>Composite Characterization Lab</option>
                              <option value="composite_manufacturing_lab" <?php if($user["privileges"]==='composite_manufacturing_lab'){echo "selected";} ?>>Composite Manufacturing Lab</option>
                              <option value="eco_textiles_lab" <?php if($user["privileges"]==='eco_textiles_lab'){echo "selected";} ?>>Eco Textiles Lab</option>
                              <option value="garments_dept_lab" <?php if($user["privileges"]==='garments_dept_lab'){echo "selected";} ?>>Garments Department Lab</option>
                              <option value="garments_manufacturing_lab" <?php if($user["privileges"]==='garments_manufacturing_lab'){echo "selected";} ?>>Garments Manufacturing Lab</option>
                              <option value="knitting_lab" <?php if($user["privileges"]==='knitting_lab'){echo "selected";} ?>>Knitting Lab</option>
                              <option value="materials_and_testing_lab" <?php if($user["privileges"]==='materials_and_testing_lab'){echo "selected";} ?>>Materials and Testing Lab</option>
                              <option value="nano_materials1_lab" <?php if($user["privileges"]==='nano_materials1_lab'){echo "selected";} ?>>Nano materials Lab 1</option>
                              <option value="nano_materials2_lab" <?php if($user["privileges"]==='nano_materials2_lab'){echo "selected";} ?>>Nano materials Lab 2</option>
                              <option value="non_wooven_lab" <?php if($user["privileges"]==='non_wooven_lab'){echo "selected";} ?>>Non Wooven Lab</option>
                              <option value="organic_chemistry_lab" <?php if($user["privileges"]==='organic_chemistry_lab'){echo "selected";} ?>>Organic Chemistry Lab</option>
                              <option value="physical_chemistry_lab" <?php if($user["privileges"]==='physical_chemistry_lab'){echo "selected";} ?>>Physical Chemistry Lab</option>
                              <option value="plasma_coating_lab" <?php if($user["privileges"]==='plasma_coating_lab'){echo "selected";} ?>>Plasma Coating Lab</option>
                              <option value="polymer_dept_lab" <?php if($user["privileges"]==='polymer_dept_lab'){echo "selected";} ?>>Polymer Department Lab</option>
                              <option value="sem_lab" <?php if($user["privileges"]==='sem_lab'){echo "selected";} ?>>Scanning Electron Microscopy Lab</option>
                              <option value="spinning_lab" <?php if($user["privileges"]==='spinning_lab'){echo "selected";} ?>>Spinning Lab</option>
                              <option value="tpcl_lab" <?php if($user["privileges"]==='tpcl_lab'){echo "selected";} ?>>TPCL Lab</option>
                              <option value="weaving_lab" <?php if($user["privileges"]==='weaving_lab'){echo "selected";} ?>>Weaving Lab</option>
                              <option value="wet_processing_lab" <?php if($user["privileges"]==='wet_processing_lab'){echo "selected";} ?>>Wet Processing Lab</option>
                              <option value="xray_diffraction_lab" <?php if($user["privileges"]==='xray_diffraction_lab'){echo "selected";} ?>>X-Ray Diffraction Lab</option>
                            </select>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 col-sm-4 control-label"></label>
                                <div class="col-md-6 col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="send_email" name="send_email" value="send_email"/>
                                            Send email 
                                        </label>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4"></label>
                                <div class="col-md-6 col-sm-6">
                                    <button type="submit" name="submit" class="btn btn-primary">Update User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-6 -->
           
        </div>
        <!-- end row -->
        
    </div>
    <!-- end #content -->
    
    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i
            class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
    
    </div>
    <!-- end page-container -->
    <!-- ================== BEGIN BASE JS ================== -->
<script src="../assets/plugins/jquery/jquery-1.9.1.min.js"></script>
<script src="../assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="../assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="../assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="../assets/js/admin_dashboard/admin_pages.js"></script>
<script src="../assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
<script src="../assets/plugins/parsley/dist/parsley.js"></script>
<script src="../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
    });
</script>

</body>

</html>
<?php
    // Close database connection
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>