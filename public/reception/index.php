<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php 
access_receptionist();
$reception = get_user_info('reception');?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8"/>
    <title>Receptionist Dashboard</title>
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
                       <?php $path = '../assets/img/users_pics/reception/' . $reception['display_picture'];?>
                        <img src="<?php echo $path; ?>" alt="reception picture"/>
                        <span class="hidden-xs"><?php echo ucfirst($reception['username']);?></span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                        <li><a href="index.php">Home Page</a></li>
                        <li><a href="change_display_settings.php">Display Settings</a></li>
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
                        <a href="javascript:;"><img src="<?php echo $path; ?>" alt="receptionist picture"/></a>
                    </div>
                    <div class="info">
                        Receptionist
                        <small>NTRC</small>
                    </div>
                </li>
            </ul>
            <!-- end sidebar user -->
            <!-- begin sidebar nav -->
            <ul class="nav">
                <li class="nav-header">Navigation</li>
                <li class="has-sub active">
                    <a href="javascript:;">
                        <b class="caret pull-right"></b>
                        <i class="fa fa-laptop"></i>
                        <span>Dashboard</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="index.php">Home</a></li>
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
                        <i class="fa fa-database"></i>
                        <span>Add Sample</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="add_commercial_sample.php">Add Commercial Sample</a></li>
                        <li><a href="add_academic_sample.php">Add Academic Sample</a></li>
                        <li><a href="add_academic_commercial_sample.php">Add Academic-Commercial Sample</a></li>
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
                        <li><a href="manage_tests_detail.php">Manage Test Details</a></li>
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
                        <li><a href="change_display_settings.php">Change Display Settings</a></li>
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
            <li><a href="javascript:;">Dashboard</a></li>
            <li class="active">Home</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Dashboard
            <small>Receptionist Homepage</small>
        </h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-red-darker">
                    <div class="stats-icon"><i class="fa fa-file-o"></i></div>
                    <div class="stats-info">
                        <h4>Pending Samples</h4>
                        <p class="pending_samples">0</p>
                    </div>
                    <div class="stats-link">
                        <a href="pending_samples.php">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-blue-darker">
                    <div class="stats-icon"><i class="fa fa-file-text-o"></i></div>
                    <div class="stats-info">
                        <h4>Pending Reports</h4>
                        <p class="pending_reports">0</p>
                    </div>
                    <div class="stats-link">
                        <a href="pending_reports.php">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-purple-darker">
                    <div class="stats-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="stats-info">
                        <h4>Delayed Samples</h4>
                        <p class="delayed_samples">0</p>
                    </div>
                    <div class="stats-link">
                        <a href="delayed_samples.php">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-green-darker">
                    <div class="stats-icon"><i class="fa fa-qrcode"></i></div>
                    <div class="stats-info">
                        <h4>Today's Sample Orders</h4>
                        <p class="todays_customers">0</p>
                    </div>
                    <div class="stats-link">
                        <a href="view_commercial_customers.php">View Detail <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- end col-3 -->
        </div>
        <!-- end row -->
        <!-- begin row -->
        <div class="row">
            <!-- begin col-6 -->
            <div class="col-md-6">
                <!-- begin panel -->
                <div class="panel panel-inverse panel-with-tabs" data-sortable-id="ui-widget-9">
                    <div class="panel-heading">
                        <ul id="myTab" class="nav nav-tabs pull-right" style="background-color:#242A30;">
                            <li class="active"><a href="#todays_lab_samples" data-toggle="tab"><span
                                    class="hidden-xs">Today</span></a></li>
                            <li><a href="#yesterdays_lab_samples" data-toggle="tab"><span class="hidden-xs">Yesterday</span></a>
                            </li>
                        </ul>
                        <h4 class="panel-title">Lab Sample Orders</h4>
                    </div>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade in active" id="todays_lab_samples"> 
                        </div>
                        <div class="tab-pane fade" id="yesterdays_lab_samples">  
                        </div>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-6 -->
            <!-- begin col-6 -->
            <div class="col-md-6">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="index-4">
                    <div class="panel-heading">
                        <h4 class="panel-title">Online Users <span class="pull-right label label-success"><span class="online_users_count"></span> online users</span>
                        </h4>
                    </div>
                    <ul class="registered-users-list online_users clearfix">
                        
                        
                    </ul>
                    <div class="panel-footer text-center">
                        <a href="manage_users.php" class="text-inverse">View All Users</a>
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
<script src="../assets/js/reception_dashboard/reception_homepage.js"></script>
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
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>