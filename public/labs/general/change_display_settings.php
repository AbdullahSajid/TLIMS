<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php require_once("../../../includes/fileUpload/UploadFile.php"); ?>
<?php 
$lab = access_lab_manager();
$beautify_lab = beautify_fieldname($lab);
use includes\FileUpload\UploadFile;
$max_file_size = 2*1024*1024;

if (isset($_POST['submit'])) {
    
    $fields_with_max_lengths = array("full_name" => 30,"username" => 30, "email"=>100);
    validate_max_lengths($fields_with_max_lengths);
  
    //validating image upload
    $display_pic = "";
    $error_messages = array();
    
    if($_FILES['new_pic']['name'] != ""){
    $destination = __DIR__ . '/../../assets/img/users_pics/' . $lab . '/';
    try {
        $upload = new UploadFile($destination);
        $upload->setMaxSize($max_file_size);
        $upload->uploadDisplayPicture();
        $error_messages = $upload->getMessages();
        $display_pic = $upload->getFileName();
    } catch (Exception $e) {
        $errors['file'] = $e->getMessage();
    }
 }
    if (empty($errors)&&empty($error_messages)) {
        
    $full_name = mysql_prep($_POST["full_name"]);
    $user_name = mysql_prep($_POST["username"]);
    $email = mysql_prep($_POST["email"]);
        
    $privileges = $lab;
    $result = update_users_settings($full_name,$user_name,$email,$privileges,$display_pic);

    if ($result) {
      // Success
      $_SESSION["message"] = "Settings Updated Successfully.";
      $query_success = TRUE;
    } else {
      // Failure
      $_SESSION["message"] = "Settings Updation Failed.";
    }
 } // end checking image errors
    
}
 else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))
// written here to resolve old picture loading after picture update
$user = get_user_info($lab);
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8"/>
    <title>Change Display Settings</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="description" content="NTRC Labs Information Management System"/>
    <meta name="author" content="Abdullah Sajid, Phone# 03012745906"/>

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
    <link href="../../assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet"/>
    <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../../assets/css/animate.min.css" rel="stylesheet"/>
    <link href="../../assets/css/style.css" rel="stylesheet"/>
    <link href="../../assets/css/style-responsive.css" rel="stylesheet"/>
    <link href="../../assets/css/theme/default.css" rel="stylesheet" id="theme"/>
    <link href="../../assets/css/essential.css" rel="stylesheet"/>
    <!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
    <link href="../../assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet"/>
    <link href="../../assets/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet"/>
    <!-- ================== END PAGE LEVEL STYLE ================== -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="../../assets/plugins/pace/pace.min.js"></script>
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
                <a href="index.php" class="navbar-brand"><img src="../../assets/img/ntrc.png" alt="logo">&nbsp;&nbsp;NTRC TLIMS</a>
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
                       <?php $path = '../../assets/img/users_pics/' . $lab .'/' . $user['display_picture'];?>
                        <img src="<?php echo $path; ?>" alt="user picture"/>
                        <span class="hidden-xs"><?php echo ucfirst($user['username']);?></span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                        <li><a href="index.php">Home Page</a></li>
                        <li><a href="change_display_settings.php">Display Settings</a></li>
                        <li><a href="lims_calender.php">Calendar</a></li>
                        <li><a href="../../faq.html">FAQ</a></li>
                        <li class="divider"></li>
                        <li><a href="../../logout.php">Sign Out</a></li>
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
                        <a href="javascript:;"><img src="<?php echo $path; ?>" alt="user picture"/></a>
                    </div>
                    <div class="info">
                        <?php echo set_dashboard_lab_title($beautify_lab); ?>
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
                        <i class="fa fa-file-text-o"></i>
                        <span>Tests Catalog</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="view_all_lab_tests.php">Tests Detail</a></li>
                        <li><a href="manage_tests_detail.php">Manage Test Details</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-spinner"></i>
                        <span class="pending_samples_indicator">Sample Status</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="pending_samples_sub_indicator" href="lab_pending_samples.php">Pending Samples</a></li>
                        <li><a href="lab_completed_samples.php">Completed Samples</a></li>
                    </ul>
                </li>
                
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Statistics</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="samples_statistics.php">Samples Statistics</a></li>
                        <li><a href="samples_statistics_charts.php">Samples Statistics Chart</a></li>
                    </ul>
                </li>
           
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-cogs"></i>
                        <span>Privacy Settings</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="change_display_settings.php">Change Display Settings</a></li>
                    </ul>
                </li>
                <li>
                    <a href="../../logout.php">
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
            <li class="active">Change Display Settings</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Change Display Settings
            <small>change display settings on your dashboard</small>
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
                        <img id="image" style="width:200px;height:175px;max-width:200px;max-height:175px;" src="<?php echo $path; ?>" />
                    </div>
                    <!-- end profile-image -->
                    <form action="change_display_settings.php" method="post" enctype="multipart/form-data">
                      
                       <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>">
                        <div class="m-b-10">
                            <input type="file" id="new_pic" name="new_pic" class="btn btn-warning btn-block btn-sm" value=""/>
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
                                    <td class="field pull-right">Name</td>
                                    <td>
                                        <input class="p-l-3" type="text" name="full_name" value="<?php echo $user['name']; ?>" required/>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="highlight">
                                    <td class="field">Username</td>
                                    <td><input class="p-l-3" type="text" name="username" value="<?php echo $user['username']; ?>" required/></td>
                                </tr>
                                <tr>
                                    <td class="field">Email</td>
                                    <td><input size="30" class="p-l-3" type="email" name="email" value="<?php echo $user['email']; ?>" required/></td>
                                </tr>
                                
                                <tr class="divider">
                                    <td colspan="2"></td>
                                </tr>
                                
                                <tr class="highlight">
                                    <td class="field">Privileges</td>
                                    <td><?php echo beautify_fieldname($user['privileges']); ?></td>
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
    
    </div>
    <!-- end page-container -->
    <!-- ================== BEGIN BASE JS ================== -->
<script src="../../assets/plugins/jquery/jquery-1.9.1.min.js"></script>
<script src="../../assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="../../assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="../../assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="../../assets/js/lab_dashboard/lab_pages.js"></script>
<script src="../../assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
<script src="../../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->
<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
    });
    $('[data-click="swal-success"]').on("click", function () {
            swal({
                title             : "Changes Updated Successfully",
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
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>