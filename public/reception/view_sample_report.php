<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php
access_receptionist();
$reception = get_user_info('reception');
if(isset($_GET['success'])){
    $query_success = TRUE;
}
if(isset($_GET['customer_id'])&&isset($_GET['lab'])){
    $customer_id = $_GET['customer_id'];
    $lab = $_GET['lab'];
    $customer_order =find_order_by_customer_id($customer_id);
    if(!$customer_order){
       
        $_SESSION["message"] = "Customer ID isn't valid.";
        redirect_to("pending_reports.php");    
    }
    if($customer_order['status']=='finalized' || $customer_order['status']=='finished'){
        if($lab!=$customer_order['lab']){
                $_SESSION["message"] = "Sample does not belong to lab";
                redirect_to("pending_reports.php"); 
        }
        $lab_table = $lab . '_' . 'samples';
        $sample_id = $customer_order['sample_id'];
        $sample = get_lab_sample($sample_id,$lab);
        $test_names=find_test_names_of_sample_by_lab_sample($sample,$lab);

    }
    elseif($customer_order['status']=='submiited'){
        $_SESSION["message"] = "Sample is not submiited to lab yet";
        redirect_to("view_customer_sample_record.php?customer_id={$customer_id}");
    }
    elseif($customer_order['status']=='pending'){
        $_SESSION["message"] = "Sample Test Report is not completed";
        redirect_to("pending_reports.php");
    }
    else{
        redirect_to("pending_reports.php");
    }
    
}
else{
     $_SESSION["message"] = "Access denied due to incorrect url";
     redirect_to("pending_reports.php");
}
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8"/>
    <title>View Sample Test Report</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="description" content="NTRC Labs Information Management System"/>
    <meta name="author" content="Abdullah Sajid, Phone# 03012745906"/>

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
   
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
                <li class="has-sub active">
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
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Completed Reports</a></li>
            <li class="active">View Sample Test Report</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">View Sample Test Results Report
            <small>View all sample test results in report</small>
        </h1>
        <!-- end page-header -->
       <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-12">
           
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="ui-typography-14">
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
                        <h4 class="panel-title">View <?php echo beautify_fieldname($lab); ?> Sample Detail</h4>
                    </div>
                        <!-- start notification  -->
                    <?php 
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                   <!-- end notification -->
                    <div class="panel-toolbar">
                            <a href="javascript:history.back(-1);" class="btn btn-inverse btn-xs">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                            </a>
                        
                    </div>
                    <div class="panel-body">
                       <div class="row">
                          <!-- begin col-6 -->
                           <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Sample ID</dt>
                                <dd><?php echo $sample['sample_id']; ?></dd>
                                <dt>Sample Type</dt>
                                <dd><?php echo $sample['sample_type']; ?></dd>
                                <dt>Sample Category</dt>
                                <dd><?php echo $sample['sample_category']; ?></dd>
                            </dl>
                            </div>
                            <!-- end col-6 -->
                            <!-- begin col-6 -->
                            <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Physical Desciption</dt>
                                <?php 
                                $sample_description = [];
                                if($sample['sample_color']){$sample_description[]=$sample['sample_color'];
                                 }  
                                 if($sample['sample_style']){$sample_description[]=$sample['sample_style'];
                                 } 
                                 if($sample['sample_weight']){$sample_description[]=$sample['sample_weight'];
                                 }                                         $sample_physical_description = implode(', ',$sample_description);         
                                ?>
                                <dd><?php echo $sample_physical_description; ?></dd>
                                <dt>Sample Image</dt>
                                <?php
                                $sample_image_label = "N/A";
                                if($sample['sample_image']){
                                    $sample_image_label = "<a href=\"../../includes/samples-pics/{$sample['sample_image']}\" target=\"_blank\">View Image</a>";
                                }
                                ?>
                                <dd><?php echo $sample_image_label; ?></dd>
                                <dt>No. of Tests</dt>
                                <dd><?php echo $sample['no_of_tests']; ?></dd>
                            
                            </dl>
                            </div>
                            <!-- end col-6 -->
                        </div>
                        <!-- end row -->
                        <!-- begin row -->
                        <div class="row">
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel-group" id="description_accordion">
                   <?php if($sample['sample_test_detail']) { ?>
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                   data-parent="#description_accordion" href="#collapse_description">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                    Sample Test Description
                                </a>
                            </h3>
                        </div>
                        <div id="collapse_description" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php echo $sample['sample_test_detail']; ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                            </div>
                           <!-- end col-12 --> 
                        </div>
                        <!-- end row -->
                    </div>
                </div>
                <!-- end panel -->
            </div>
        </div>
        <!-- begin row for single test results -->
        <?php $counter = 0;?>
        <?php foreach($test_names as $testname){
            $counter +=1;
            $test_table = uglify_fieldname($testname) . '_' . 'test'; 
            $sample_test=get_test($sample_id,$test_table);
        ?>
        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-12">
           
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="ui-typography-14">
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
                        <h4 class="panel-title"><?php echo $counter . '. ' . $testname; ?> Test Results</h4>
                    </div>
                  
                    <div class="panel-body">
                       <div class="row">
                          <!-- begin col-6 -->
                           <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Test Name</dt>
                                <dd><?php echo $testname . ' Test'; ?></dd>
                                <dt>Temperature</dt>
                                <dd><?php echo $sample_test['temperature']; ?> &deg;C</dd>
                                <dt>Test File</dt>
                                <?php
                                $test_file_label = "N/A";
                                if($sample_test['test_file']){
                                    $test_file_label = "<a href=\"../../includes/sample-test-results/{$lab}/{$sample_test['test_file']}\" download>View File</a>";
                                }
                                ?>
                                <dd><?php echo $test_file_label; ?></dd>
                                
                            </dl>
                            </div>
                            <!-- end col-6 -->
                            <!-- begin col-6 -->
                            <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Test Standard</dt>
                                <dd><?php echo $sample_test['test_standard']; ?></dd>
                                <dt>Humidity</dt>
                                <dd><?php echo $sample_test['humidity']; ?> &#37;</dd>
                                <dt>Finished Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($sample_test['finished_date'])); ?></dd>
                               
                            </dl>
                            </div>
                            <!-- end col-6 -->
                        </div>
                        <!-- end row -->
        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel-group" id="accordion<?php echo $counter;?>">
                   
                    <?php if($sample_test['test_result']) { ?>
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                   data-parent="#accordion<?php echo $counter;?>" href="#collapseOne<?php echo $counter;?>">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                    Sample Test Results
                                </a>
                            </h3>
                        </div>
                        <div id="collapseOne<?php echo $counter;?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php echo $sample_test['test_result']; ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($sample_test['test_conditions']) { ?>
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                   data-parent="#accordion<?php echo $counter;?>" href="#collapseTwo<?php echo $counter;?>">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                    Sample Test Conditions
                                </a>
                            </h3>
                        </div>
                        <div id="collapseTwo<?php echo $counter;?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php echo $sample_test['test_conditions']; ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
        </div>
        <!-- end col-12 -->
                        </div>
                        <!-- end row -->
                    </div>
                </div>
                <!-- end panel -->
            </div>
        </div>
        <!-- end row for single test results -->
        <?php } ?>
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
<script src="../assets/js/reception_dashboard/reception_pages.js"></script>
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