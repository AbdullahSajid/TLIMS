<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php 
access_admin();
if(isset($_GET['success'])){
    $query_success = TRUE;
}
if(isset($_GET['customer_id'])){
   
   $get_customer_id = $_GET['customer_id'];
   $customer = get_customer_order_by_id($get_customer_id);
   if(!$customer){
       // now checking for record in academic_students table
       $customer = get_student_order_by_id($get_customer_id);
       if(!$customer) {
           $_SESSION["message"] = "Customer ID isn't valid.";
       }
   }
   // now gather the name of tests of this sample in string
   if(isset($customer)){
       $test_names = find_test_names_of_sample( $customer['sample_id'],$customer['lab']);
       $test_names_list = implode(', ',$test_names);
       $lab_sample = get_lab_sample($customer['sample_id'],$customer['lab']);
   }
}
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
    <title>View Customer Sample Record</title>
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
    <link href="../assets/plugins/twitter-typehead/typeahead.css" rel="stylesheet">
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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-group"></i>
                        <span>Manage Customers</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="view_customer_sample_record.php">View Customer Record</a></li>
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
            <li><a href="javascript:;">Manage Customers</a></li>
            <li class="active">View Customer Record</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">View Customer Sample Record
            <small>View and manage customer sample record</small>
        </h1>
        <!-- end page-header -->
        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-12">
               <!-- start notification  -->
                    <?php 
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                    <!-- end notification -->
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-5">
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
                        <h4 class="panel-title">Search Customer Sample Record</h4>
                    </div>
                    <div class="panel-body">
                        <form action="view_customer_sample_record.php" method="GET">
                        <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-11 col-sm-11 col-md-11">
                                <label class="control-label">Select Customers </label>
                                    <input class="typeahead form-control" type="text" id="customer_id" name="customer_id" placeholder="Customer ID" required/>
                                    
                            </div>
                               <button style="margin-top: 20px; margin-left: 8px;" type="submit" name="search" class="btn btn-primary">Search</button>
                         </div>
                        <!-- end row -->
                             
                        </form>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-12 -->
        </div>
        <!-- end row -->
        <!-- #delete_modal_confirmation start -->
            <div class="modal fade" id="delete_modal_confirmation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                    <h4 class="modal-title">User Deletion</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger m-b-0">
                        <h4><i class="fa fa-warning"></i> Do you want to delete this sample submission?</h4>
                        <p>Warning!! Deleting sample will delete all the customer and sample record from the database. Click Yes to delete and No to cancel.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-sm btn-white"
                       data-dismiss="modal">Close</a>
                    <a href="operations/delete_customer_sample_record.php?customer_id=<?php if(isset($customer)){ echo urlencode($customer["customer_id"]);} else{echo "";} ?>" class="btn btn-sm btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <!-- #delete_modal_confirmation end-->
    <?php if(isset($customer)&&isset($lab_sample)&&$customer['type']!='academic'){ ?>
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
                        <h4 class="panel-title">View Customer Sample Record</h4>
                    </div>
                    <div class="panel-toolbar text-center">
                        
                            <a class="btn btn-danger btn-sm" href="#delete_modal_confirmation" data-toggle="modal"><i class="fa fa-trash-o"> Delete</i></a>
                            <a class="btn btn-primary btn-sm" href="print_customer_sample_receipt.php?customer_id=<?php echo urlencode($customer["customer_id"]); ?>" target="_blank"><i class="fa fa-print"></i> Print Receipt</a>
                            <?php if($customer['status']=='finished'){ ?>
                             <a class="btn btn-info btn-sm" href="view_sample_report.php?customer_id=<?php echo $customer['customer_id']; ?>&lab=<?php echo $customer['lab']; ?>"><i class="fa fa-file-text-o"></i> View Report</a>
                             <a class="btn btn-default btn-sm" href="print_sample_report.php?customer_id=<?php echo $customer['customer_id']; ?>&lab=<?php echo $customer['lab']; ?>" target="_blank"><i class="fa fa-download"></i> Download Report</a>           
                            <?php } ?>
                        
                    </div>
                    <div class="panel-body">
                       <div class="row">
                          <!-- begin col-6 -->
                           <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Customer ID</dt>
                                <dd><?php echo $customer['customer_id']; ?></dd>
                                <dt>Type</dt>
                                <dd><?php echo beautify_fieldname($customer['type']); ?></dd>
                                <dt>Name</dt>
                                <dd><?php echo beautify_fieldname($customer['name']); ?></dd>
                                <dt>City</dt>
                                <dd><?php echo beautify_fieldname($customer['city']); ?></dd>
                                <dt>Designation</dt>
                                <dd><?php echo $customer['designation']; ?></dd>
                                <dt>Organization</dt>
                                <dd><?php echo beautify_fieldname($customer['organization']); ?></dd>
                                <dt>Customer Ref No.</dt>
                                <dd><?php echo $customer['customer_ref']; ?></dd>
                                <dt>Phone</dt>
                                <dd><?php echo $customer['phone']; ?></dd>
                                <dt>Email</dt>
                                <dd><?php echo $customer['email']; ?></dd>
                                <dt>Address</dt>
                                <?php
                                $address = 'N/A';
                                if($customer['address']){
                                  $address = $customer['address'];  
                                 }?>
                                <dd><?php echo $address; ?></dd>
                                <dt>Arrival Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['creation_time'])); ?></dd>
                            </dl>
                            </div>
                            <!-- end col-6 -->
                            <!-- begin col-6 -->
                            <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Sample ID</dt>
                                <dd><?php echo $customer['sample_id']; ?></dd>
                                <dt>Type</dt>
                                <dd><?php echo $lab_sample['sample_type']; ?></dd>
                                <dt>Category</dt>
                                <dd><?php echo $lab_sample['sample_category']; ?></dd>
                                <dt>Physical Desciption</dt>
                                <?php 
                                                                                      
                                 $sample_description = [];
                                if($lab_sample['sample_color']){$sample_description[]=$lab_sample['sample_color'];
                                 }  
                                 if($lab_sample['sample_style']){$sample_description[]=$lab_sample['sample_style'];
                                 } 
                                 if($lab_sample['sample_weight']){$sample_description[]=$lab_sample['sample_weight'];
                                 }                                         $sample_physical_description = implode(', ',$sample_description);         
                                ?>
                                <dd><?php echo $sample_physical_description; ?></dd>
                                <dt>Image</dt>
                                <?php
                                $sample_image_label = "N/A";
                                if($lab_sample['sample_image']){
                                    $sample_image_label = "<a href=\"../../includes/samples-pics/{$lab_sample['sample_image']}\" target=\"_blank\">View Image</a>";
                                }
                                ?>
                                <dd><?php echo $sample_image_label; ?></dd>
                                <dt>Lab</dt>
                                <dd><?php echo beautify_fieldname($customer['lab']); ?></dd>
                                <dt>No. of Tests</dt>
                                <dd><?php echo $lab_sample['no_of_tests']; ?></dd>
                                <dt>Tests</dt>
                                <dd><?php echo $test_names_list; ?></dd>
                                <dt>Total Payment (Rs.)</dt>
                                <dd><?php echo $customer['payment']; ?></dd>
                                <dt>Received Payment (Rs.)</dt>
                                <dd><?php echo $customer['payment_received']; ?></dd>
                                <dt>Pending Payment (Rs.)</dt>
                                <dd><?php echo $customer['payment_pending']; ?></dd>
                                <dt>Status</dt>
                                <dd><?php echo beautify_fieldname($customer['status']); ?></dd>
                                <dt>Expected Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['expected_date'])); ?></dd>
                                <?php
                                if($customer['completion_date']){?>
                                <dt>Completion Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['completion_date'])); ?></dd>  
                                <?php } ?>
                                <?php
                                if($customer['finished_date']){?>
                                <dt>Finished Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['finished_date'])); ?></dd>  
                                <?php } ?>
                            
                            </dl>
                            </div>
                            <!-- end col-6 -->
                        </div>
                        <!-- end row -->
            <?php if($lab_sample['sample_test_detail']) {?>
            <!-- begin row -->
            <div class="row">          
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                   data-parent="#accordion" href="#collapseOne">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                 Sample Test Description
                                </a>
                            </h3>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                               <?php echo $lab_sample['sample_test_detail']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <!-- end col-12 -->
        </div>
        <!-- end row -->
          <?php } ?>          
                    </div>
                    <!-- end panel-body -->
                </div>
                <!-- end panel -->
         <?php } 
         elseif(isset($customer)&&isset($lab_sample)&&$customer['type']=='academic'){ ?>
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
                        <h4 class="panel-title">View Customer Sample Record</h4>
                    </div>
                    <div class="panel-toolbar text-center">
                        <a class="btn btn-danger btn-sm" href="#delete_modal_confirmation" data-toggle="modal"><i class="fa fa-trash-o"> Delete</i></a>
                        <a class="btn btn-primary btn-sm" href="print_customer_sample_receipt.php?customer_id=<?php echo urlencode($customer["customer_id"]); ?>" target="_blank"><i class="fa fa-print"></i> Print Receipt</a>
                        <?php if($customer['status']=='finished'){ ?>
                             <a class="btn btn-info btn-sm" href="view_sample_report.php?customer_id=<?php echo $customer['customer_id']; ?>&lab=<?php echo $customer['lab']; ?>"><i class="fa fa-file-text-o"></i> View Report</a>
                             <a class="btn btn-default btn-sm" href="print_sample_report.php?customer_id=<?php echo $customer['customer_id']; ?>&lab=<?php echo $customer['lab']; ?>" target="_blank"><i class="fa fa-download"></i> Download Report</a>           
                        <?php } ?>
                        
                    </div>
                    <div class="panel-body">
                       <div class="row">
                          <!-- begin col-6 -->
                           <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Customer ID</dt>
                                <dd><?php echo $customer['customer_id']; ?></dd>
                                <dt>Type</dt>
                                <dd><?php echo beautify_fieldname($customer['type']); ?></dd>
                                <dt>Name</dt>
                                <dd><?php echo beautify_fieldname($customer['name']); ?></dd>
                                <dt>Reg No.</dt>
                                <dd><?php echo beautify_fieldname($customer['reg_no']); ?></dd>
                                <dt>City</dt>
                                <dd><?php echo beautify_fieldname($customer['city']); ?></dd>
                                <dt>Designation</dt>
                                <dd><?php echo $customer['designation']; ?></dd>
                                <dt>Institute</dt>
                                <dd><?php echo beautify_fieldname($customer['institute']); ?></dd>
                                <dt>Department</dt>
                                <dd><?php echo $customer['department']; ?></dd>
                                <dt>Phone</dt>
                                <dd><?php echo $customer['phone']; ?></dd>
                                <dt>Email</dt>
                                <dd><?php echo $customer['email']; ?></dd>
                                <dt>Topic of Study</dt>
                                <?php
                                $topic_of_study = 'N/A';
                                if($customer['topic_of_study']){
                                  $topic_of_study = $customer['topic_of_study'];  
                                 }?>
                                <dd><?php echo $topic_of_study; ?></dd>
                                <dt>Arrival Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['creation_time'])); ?></dd>
                            </dl>
                            </div>
                            <!-- end col-6 -->
                            <!-- begin col-6 -->
                            <div class="col-6 col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Sample ID</dt>
                                <dd><?php echo $customer['sample_id']; ?></dd>
                                <dt>Sample Type</dt>
                                <dd><?php echo $lab_sample['sample_type']; ?></dd>
                                <dt>Category</dt>
                                <dd><?php echo $lab_sample['sample_category']; ?></dd>
                                <dt>Physical Desciption</dt>
                                <?php 
                                $sample_description = [];
                                if($lab_sample['sample_color']){$sample_description[]=$lab_sample['sample_color'];
                                 }  
                                 if($lab_sample['sample_style']){$sample_description[]=$lab_sample['sample_style'];
                                 } 
                                 if($lab_sample['sample_weight']){$sample_description[]=$lab_sample['sample_weight'];
                                 }                                         $sample_physical_description = implode(', ',$sample_description);         
                                ?>
                                <dd><?php echo $sample_physical_description; ?></dd>
                                <dt>Image</dt>
                                <?php
                                $sample_image_label = "N/A";
                                if($lab_sample['sample_image']){
                                    $sample_image_label = "<a href=\"../../includes/samples-pics/{$lab_sample['sample_image']}\" target=\"_blank\">View Image</a>";
                                }
                                ?>
                                <dd><?php echo $sample_image_label; ?></dd>
                                <dt>Lab</dt>
                                <dd><?php echo beautify_fieldname($customer['lab']); ?></dd>
                                <dt>No. of Tests</dt>
                                <dd><?php echo $lab_sample['no_of_tests']; ?></dd>
                                <dt>Tests</dt>
                                <dd><?php echo $test_names_list; ?></dd>
                                <dt>Total Payment (Rs.)</dt>
                                <dd><?php echo $customer['payment']; ?></dd>
                                <dt>Status</dt>
                                <dd><?php echo beautify_fieldname($customer['status']); ?></dd>
                                <dt>Expected Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['expected_date'])); ?></dd>
                                <?php
                                if($customer['completion_date']){?>
                                <dt>Completion Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['completion_date'])); ?></dd>  
                                <?php } ?>
                                <?php
                                if($customer['finished_date']){?>
                                <dt>Finished Time</dt>
                                <dd><?php echo date('d-m-Y h:i:s A',strtotime($customer['finished_date'])); ?></dd>  
                                <?php } ?>
                            
                            </dl>
                            </div>
                            <!-- end col-6 -->
                        </div>
                        <!-- end row -->
            <?php if($lab_sample['sample_test_detail']) {?>
            <!-- begin row -->
            <div class="row">          
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                   data-parent="#accordion" href="#collapseOne">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                 Sample Test Description
                                </a>
                            </h3>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body">
                               <?php echo $lab_sample['sample_test_detail']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <!-- end col-12 -->
        </div>
        <!-- end row -->
          <?php } ?>          
                    </div>
                    <!-- end panel-body -->
                </div>
                <!-- end panel -->
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
<script src="../assets/js/admin_dashboard/admin_pages.js"></script>
<script src="../assets/plugins/twitter-typehead/typeahead.min.js"></script>
<script src="../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
        $('#customer_id').typeahead({
            remote:'./ajax/search_customer_id.php?key=%QUERY',
            limit : 8
        });
    });
</script>

</body>

</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>