<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php
access_admin();
if (isset($_POST['submit'])) {
  // Process the form
  
  $fields_with_max_lengths = array("full_name" => 30,"username" => 30,
                        "password"=>30,"confirm_password"=>30,"email"=>100);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    // Perform Create
    
    $full_name = mysql_prep($_POST["full_name"]);
    $user_name = mysql_prep($_POST["username"]);
    $pass_word = mysql_prep($_POST["password"]);
    $confirm_password = mysql_prep($_POST["confirm_password"]);
    $email = mysql_prep($_POST["email"]);
    $privileges = mysql_prep($_POST["privileges"]); 
      
    // check if lab user exists
    $old_user_exists = exists_old_user($privileges);
    if($old_user_exists){
        $_SESSION["message"] = "Lab User Already Exists";
        // redirect to itself to show session error message
        redirect_to("add_user.php");
    }
      
    if(!password_check($pass_word,$confirm_password)){
         $_SESSION["message"] = "Both Passwords are unmatched";
        // redirect to itself to show session error message
         redirect_to("add_user.php");
    } 
    
    $result = add_new_user($full_name,$user_name,$pass_word,$email,$privileges);
      // Success
      if ($result) {
        if(isset($_POST["send_email"])){
          // sent email to new user
          require_once("./operations/send_email_to_user.php");
          if(isset($is_email_sent)&&$is_email_sent!=0){
               $_SESSION["message"] = "User Created and Email Sent.";
               $query_success = TRUE; 
          }
          else{
              $_SESSION["message"] = "User created but email sending failed.";
              $query_success = TRUE;
          }
        }
        else{
             $_SESSION["message"] = "User created.";
             $query_success = TRUE; 
        }
    } else {
      // Failure
      $_SESSION["message"] = "User creation failed. Try to change username";
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
    <title>Add New User</title>
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
                        <li class="active"><a href="add_user.php">Add New User</a></li>
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
            <li class="active">Add New User</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Add New User
            <small>Add new system users</small>
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
                        <h4 class="panel-title">Add New User</h4>
                    </div>
        
                    <!-- start notification  -->
                    <?php 
//                 display form errors if any
                    echo form_errors($errors);
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                    <!-- end notification -->
                    <div class="panel-body panel-form">
                        <form class="form-horizontal form-bordered" data-parsley-validate="true" name="demo-form" action="add_user.php" method="post">
                           <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="fullname">Full Name * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" id="fullname" name="full_name"
                                           placeholder="Full Name" data-parsley-required="true"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="fullname">Username * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" id="username" name="username"
                                           placeholder="Username" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="password">Password * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="password" id="password" name="password"
                                           placeholder="Password" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="confirm_password">Confirm Password * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                                           placeholder="Confirm Password" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" for="email">Email * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="email" id="email" name="email"
                                           placeholder="Email" data-parsley-required="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4">Privileges * :</label>
                                <div class="col-md-6 col-sm-6">
                                    <select class="form-control" id="select-required" name="privileges"
                                    data-parsley-required="true">
                                          <option value="" selected>Select Privilege</option>
                                          <option value="reception" >Reception</option>
                                          <option value="mechanical_lab" >Mechanical Lab</option>
                                          <option value="spectroscopy_lab" >Spectroscopy Lab</option>
                                          <option value="comfort_lab" >Comfort Lab</option>
                                          <option value="anti_microbial_lab" >Antimicrobial Testing Lab</option>
                                          <option value="applied_chemistry_lab" >Applied Chemistry Lab</option>
                                          <option value="chemistry_lab" >Chemistry Lab</option>
                                          <option value="coating_lab" >Coating Lab</option>
                                          <option value="composite_characterization_lab" >Composite Characterization Lab</option>
                                          <option value="composite_manufacturing_lab" >Composite Manufacturing Lab</option>
                                          <option value="eco_textiles_lab" >Eco Textiles Lab</option>
                                          <option value="garments_dept_lab" >Garments Department Lab</option>
                                          <option value="garments_manufacturing_lab" >Garments Manufacturing Lab</option>
                                          <option value="knitting_lab" >Knitting Lab</option>
                                          <option value="materials_and_testing_lab" >Materials and Testing Lab</option>
                                          <option value="nano_materials1_lab" >Nano materials Lab 1</option>
                                          <option value="nano_materials2_lab" >Nano materials Lab 2</option>
                                          <option value="non_wooven_lab" >Non Wooven Lab</option>
                                          <option value="organic_chemistry_lab" >Organic Chemistry Lab</option>
                                          <option value="physical_chemistry_lab" >Physical Chemistry Lab</option>
                                          <option value="plasma_coating_lab" >Plasma Coating Lab</option>
                                          <option value="polymer_dept_lab" >Polymer Department Lab</option>
                                          <option value="sem_lab" >Scanning Electron Microscopy Lab</option>
                                          <option value="spinning_lab" >Spinning Lab</option>
                                          <option value="tpcl_lab" >TPCL Lab</option>
                                          <option value="weaving_lab" >Weaving Lab</option>
                                          <option value="wet_processing_lab" >Wet Processing Lab</option>
                                          <option value="xray_diffraction_lab" >X-Ray Diffraction Lab</option>
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
                                    <button type="submit" name="submit" class="btn btn-primary">Create User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- begin hidden button for success message -->
                     <a href="#" data-click="swal-success" class="btn btn-success hidden">Success</a>
                    <!-- end hidden button for success message -->
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
    $('[data-click="swal-success"]').on("click", function () {
            swal({
                title             : "User Created Successfully",
                text              : "",
                type              : "success",
                showCancelButton  : !0,
                confirmButtonClass: "btn-success",
                confirmButtonText : "Okay!"
            })
    });
    <?php if(isset($query_success)&&$query_success===TRUE){ ?>
         
      $('[data-click="swal-success"]').click();
    <?php } ?>
</script>

</body>

</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>