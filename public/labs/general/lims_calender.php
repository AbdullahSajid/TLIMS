<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php 
$lab = access_lab_manager();
$beautify_lab = beautify_fieldname($lab);
$user = get_user_info($lab);?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8"/>
    <title>LIMS Calender</title>
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
     <link href="../../assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet"/>
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
           
                <li class="has-sub">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-cogs"></i>
                        <span>Privacy Settings</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="change_display_settings.php">Change Display Settings</a></li>
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
       
        <!-- begin page-header -->
        <h1 class="page-header">Calender
            <small>See the schedules of events</small>
        </h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-10 -->
            <div class="col-md-8">

                <div class="panel panel-inverse" data-sortable-id="index-10">
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
                        <h4 class="panel-title">Calendar</h4>
                    </div>
                    <div class="panel-body">
                        <div id="datepicker-inline" class="datepicker-full-width">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col-8 -->
        </div>
        <!-- end row -->
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
<script src="../../assets/plugins/sparkline/jquery.sparkline.js"></script>
<script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../assets/js/dashboard_demo.js"></script>
<script src="../../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
        Dashboard.init();
    });
</script>
</body>
</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>