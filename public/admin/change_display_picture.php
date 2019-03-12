<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php require_once("../../includes/fileUpload/UploadFile.php"); ?>
<?php
access_admin();
use includes\FileUpload\UploadFile;
$max_file_size = 2*1024*1024;

if (isset($_POST['submit'])) {
  
    //validating image upload
    $error_messages = array();
    $destination = __DIR__ . '/../assets/img/users_pics/admin/';
    try {
        $upload = new UploadFile($destination);
        $upload->setMaxSize($max_file_size);
        $upload->uploadDisplayPicture();
        $error_messages = $upload->getMessages();
    } catch (Exception $e) {
        $errors['file'] = $e->getMessage();
    }
    if (empty($errors)&&empty($error_messages)&&$upload->check_saved()) {
        
    $privileges = 'admin';
    $display_pic = $upload->getFileName();
      
    $result = update_users_pic($privileges,$display_pic);

    if ($result) {
      // Success
      $_SESSION["message"] = "Profile Picture Updated.";
      $query_success = TRUE;
    } else {
      // Failure
      $_SESSION["message"] = "Profile Picture Updation Failed.";
    }
 } // end checking image errors
    
}
 else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))
// written here to resolve old picture loading after picture update
$admin = get_admin_info();
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8"/>
    <title>Change Display Picture</title>
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
                <li class="has-sub">
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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-cogs"></i>
                        <span>Privacy Settings</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="edit_password.php">Change Password</a></li>
                        <li class="active"><a href="change_display_picture.php">Change Display Picture</a></li>
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
            <li><a href="javascript:;">Privacy Settings</a></li>
            <li class="active">Change Display Picture</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Change Display Picture
            <small>change display picture on your dashboard</small>
        </h1>
        <!-- end page-header -->
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
        <!-- begin profile-container -->
        <div class="profile-container">
            <!-- begin profile-section -->
            <div class="profile-section">
                <!-- begin profile-left -->
                <div class="profile-left">
                    <!-- begin profile-image -->
                    <div class="profile-image">
                        <img id="image" style="width:200px;height:175px;max-width:200px;max-height:175px;" src="../assets/img/users_pics/admin/<?php echo $admin['display_picture']; ?>"/>
                    </div>
                    <!-- end profile-image -->
                    <form action="change_display_picture.php" method="post" enctype="multipart/form-data">
                      
                       <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>">
                        <div class="m-b-10">
                            <input type="file" id="new_pic" name="new_pic" class="btn btn-warning btn-block btn-sm" value="" required/>
                        </div>
                </div>
                <!-- end profile-left -->
                <!-- begin profile-right -->
                <div class="profile-right">
                    <!-- begin profile-info -->
                    <div class="profile-info">
                        <!-- begin table -->
                        <div class="table-responsive">
                            <table class="table table-profile">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        <h4>
                                        <?php echo $admin['name']; ?>
                                        </h4>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="highlight">
                                    <td class="field">Username</td>
                                    <td><?php echo $admin['username']; ?></td>
                                </tr>
                                <tr class="highlight">
                                    <td class="field">Privileges</td>
                                    <td><?php echo ucfirst($admin['privileges']); ?></td>
                                </tr>
                                <tr class="divider">
                                    <td colspan="2"></td>
                                </tr>
                                 <tr>
                                    <td class="field">Office</td>
                                    <td><i class="fa fa-phone fa-lg m-r-5"></i> +92-41-9230081-85 Ext: 192</td>
                                </tr>
                                <tr>
                                    <td class="field">Mobile</td>
                                    <td><i class="fa fa-mobile fa-lg m-r-5"></i>&nbsp;&nbsp;&nbsp;0333-6782842
                                    </td>
                                </tr>
                                <tr>
                                    <td class="field">Email</td>
                                    <td><i class="fa fa-inbox fa-lg m-r-5"></i> ahsan@ntu.edu.pk</td>
                                </tr>
                               
                                <tr class="divider">
                                    <td colspan="2"></td>
                                </tr>
                               
                                </tbody>
                            </table>
                        </div>
                        <!-- end table -->
                    </div>
                    <!-- end profile-info -->
                        <button type="submit" name="submit" class="btn btn-primary col-md-offset-2">
                            <i class="fa fa-save"></i>
                            <span>&nbsp;Update</span>
                        </button>
                    </form>
                </div>
                <!-- end profile-right -->  
            </div>
            <!-- end profile-section -->  
            <!-- begin hidden button for success message -->
                <a href="#" data-click="swal-success" class="btn btn-success hidden">Success</a>
            <!-- end hidden button for success message -->  
        </div>
        <!-- end profile-container -->
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
<script src="../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
    });
    $('[data-click="swal-success"]').on("click", function () {
            swal({
                title             : "Profile Picture Updated Successfully",
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
    
//  change picture
    document.getElementById('new_pic').addEventListener('change', readURL, true);
    function readURL(){
       var file = document.getElementById("new_pic").files[0];
       var reader = new FileReader();
       reader.onloadend = function(){
          document.getElementById('image').src = reader.result;        
    }
   if(file){
      reader.readAsDataURL(file);
    }else{
    }
}
</script>

</body>

</html>
<?php
    // Close database connection
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>