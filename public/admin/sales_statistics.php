<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php
access_admin();
if(isset($_POST['find'])){
    
    $customer_type = $_POST['customer_type'];
    $lab = $_POST['lab'];
    $status = $_POST['order_status'];
    $time_period = $_POST['dates'];
    $starting_date = "";
    $ending_date = "";
    
    if(!empty($time_period)){
        $dates = explode(' - ',$time_period);
        $starting_date = date("Y-m-d", strtotime($dates[0]));
        $ending_date = date("Y-m-d", strtotime($dates[1]));
      
        $time_interval = "AND timestamp BETWEEN '$starting_date' AND '$ending_date' + INTERVAL 1 DAY ";  
    }
    
    $query  = "SELECT * FROM orders ";
    $stats_query = "SELECT sum(payment), sum(payment_received), sum(payment_pending) FROM orders ";
    
    if($customer_type=='all'){
        $query .= "WHERE type IN('commercial','academic commercial') ";
        $stats_query .= "WHERE type IN('commercial','academic commercial') ";
    }
    else{
        $query .= "WHERE type IN('{$customer_type}') ";
        $stats_query .= "WHERE type IN('{$customer_type}') ";
    }
    if($status!='all'){ 
        $query .= "AND status='{$status}' ";
        $stats_query .= "AND status='{$status}' ";
    }
    if($lab!='all'){
        $query .= "AND lab='{$lab}' ";
        $stats_query .= "AND lab='{$lab}' ";
    }
    if(!empty($time_period)){
        $query .= $time_interval;
        $stats_query .= $time_interval;
    }
    $total_samples = mysqli_query($connection, $query);
    $samples_count = mysqli_num_rows($total_samples);
    confirm_query($total_samples);
    
    $total_statistics = mysqli_query($connection, $stats_query);
    confirm_query($total_statistics);
    $total_statistics = mysqli_fetch_row($total_statistics);
}
else{
   $customer_type = $lab = $status = 'all';
   $time_period = "";
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
    <title>Sales Statistics</title>
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
    <link href="../assets/plugins/jquery-daterangepicker/daterangepicker.css" rel="stylesheet"/>
    <link href="../assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/DataTables/extensions/Responsive/css/responsive.bootstrap.min.css" rel="stylesheet"/>
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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Sales</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="sales_statistics.php">Sales Statistics</a></li>
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
            <li><a href="javascript:;">Sales</a></li>
            <li class="active">Sales Statistics</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Sales Statistics
            <small>Filter sales statistics for customers</small>
        </h1>
        <!-- end page-header -->
        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-12">
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
                        <h4 class="panel-title"><?php if(isset($samples_count)) echo 'Number of Sales: ' . $samples_count; else echo 'Find Sales Statistics';?></h4>
                    </div>
                            <div class="alert alert-info fade in">
                                <button type="button" class="close" data-dismiss="alert">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <i class="fa fa-lg fa-info-circle"></i>&nbsp;&nbsp;50% Test Fee concession is given to Academic-Commercial Customers
                            </div>
                <div class="panel-body">
                    <form action="sales_statistics.php" method="POST">
                    <!-- begin row -->
                    <div class="form-group row">
                            
                        <div class="form-group col-6 col-sm-6 col-md-6">
                                <label for="customer_type" class="control-label">Customers Type *</label>
                                    <select class="form-control" id="customer_type" name="customer_type" required>
                                          <option value="">Select Customer Type</option>
                                          <option value="commercial" <?php if($customer_type=='commercial') echo "selected";?> >Commercial</option>
                                          <option value="academic commercial" <?php if($customer_type=='academic commercial') echo "selected";?>>Academic Commercial</option>
                                          <option value="all" <?php if($customer_type=='all') echo "selected";?>>All</option>
                                    </select>
                                    
                         </div>
                         
                         <div class="form-group col-6 col-sm-6 col-md-6">
                                <label for="lab" class="control-label">Lab *</label>
                                    <select class="form-control" id="lab" name="lab" required>
                                          <option value="">Select Lab</option>
                                          <option value="all" <?php if($lab=='all') echo "selected";?>>All</option>
                                          <option value="mechanical_lab" <?php if($lab=='mechanical_lab') echo "selected";?>>Mechanical Lab</option>
                                          <option value="spectroscopy_lab" <?php if($lab=='spectroscopy_lab') echo "selected";?>>Spectroscopy Lab</option>
                                          <option value="comfort_lab" <?php if($lab=='comfort_lab') echo "selected";?>>Comfort Lab</option>
                                          <option value="anti_microbial_lab" <?php if($lab=='anti_microbial_lab') echo "selected";?>>Antimicrobial Testing Lab</option>
                                          <option value="applied_chemistry_lab" <?php if($lab=='applied_chemistry_lab') echo "selected";?>>Applied Chemistry Lab</option>
                                          <option value="chemistry_lab" <?php if($lab=='chemistry_lab') echo "selected";?>>Chemistry Lab</option>
                                          <option value="coating_lab" <?php if($lab=='coating_lab') echo "selected";?>>Coating Lab</option>
                                          <option value="composite_characterization_lab" <?php if($lab=='composite_characterization_lab') echo "selected";?>>Composite Characterization Lab</option>
                                          <option value="composite_manufacturing_lab" <?php if($lab=='composite_manufacturing_lab') echo "selected";?>>Composite Manufacturing Lab</option>
                                          <option value="eco_textiles_lab" <?php if($lab=='eco_textiles_lab') echo "selected";?>>Eco Textiles Lab</option>
                                          <option value="garments_dept_lab" <?php if($lab=='garments_dept_lab') echo "selected";?>>Garments Department Lab</option>
                                          <option value="garments_manufacturing_lab" <?php if($lab=='garments_manufacturing_lab') echo "selected";?>>Garments Manufacturing Lab</option>
                                          <option value="knitting_lab" <?php if($lab=='knitting_lab') echo "selected";?>>Knitting Lab</option>
                                          <option value="materials_and_testing_lab" <?php if($lab=='materials_and_testing_lab') echo "selected";?>>Materials and Testing Lab</option>
                                          <option value="nano_materials1_lab" <?php if($lab=='nano_materials1_lab') echo "selected";?>>Nano materials Lab 1</option>
                                          <option value="nano_materials2_lab" <?php if($lab=='nano_materials2_lab') echo "selected";?>>Nano materials Lab 2</option>
                                          <option value="non_wooven_lab" <?php if($lab=='non_wooven_lab') echo "selected";?>>Non Wooven Lab</option>
                                          <option value="organic_chemistry_lab" <?php if($lab=='organic_chemistry_lab') echo "selected";?>>Organic Chemistry Lab</option>
                                          <option value="physical_chemistry_lab" <?php if($lab=='physical_chemistry_lab') echo "selected";?>>Physical Chemistry Lab</option>
                                          <option value="plasma_coating_lab" <?php if($lab=='plasma_coating_lab') echo "selected";?>>Plasma Coating Lab</option>
                                          <option value="polymer_dept_lab" <?php if($lab=='polymer_dept_lab') echo "selected";?>>Polymer Department Lab</option>
                                          <option value="sem_lab" <?php if($lab=='sem_lab') echo "selected";?>>Scanning Electron Microscopy Lab</option>
                                          <option value="spinning_lab" <?php if($lab=='spinning_lab') echo "selected";?>>Spinning Lab</option>
                                          <option value="tpcl_lab" <?php if($lab=='tpcl_lab') echo "selected";?>>TPCL Lab</option>
                                          <option value="weaving_lab" <?php if($lab=='weaving_lab') echo "selected";?>>Weaving Lab</option>
                                          <option value="wet_processing_lab" <?php if($lab=='wet_processing_lab') echo "selected";?>>Wet Processing Lab</option>
                                          <option value="xray_diffraction_lab" <?php if($lab=='xray_diffraction_lab') echo "selected";?>>X-Ray Diffraction Lab</option>
                                    </select>
                                </div>
                         </div>
                        <!-- end row -->
                        <!-- begin row -->
                         <div class="form-group row">
                            
                            <div class="form-group col-6 col-sm-6 col-md-6">
                        
                                <label class="control-label">Time Period *</label>
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input id="dates" name="dates" type="text" class="form-control" required/>
                                  </div>
                             
                             </div>
                             
                             <div class="form-group col-5 col-sm-5 col-md-5">
                                <label class="control-label" for="order_status">Sample Status *</label>
                                    <select class="form-control" id="order_status" name="order_status" required>
                                          <option value="">Select Sample Status</option>
                                          <option value="all" <?php if($status=='all') echo "selected";?>>All</option>
                                          <option value="submitted" <?php if($status=='submitted') echo "selected";?>>Submitted Samples</option>
                                          <option value="pending" <?php if($status=='pending') echo "selected";?>>Pending Samples</option>
                                          <option value="completed" <?php if($status=='completed') echo "selected";?>>Pending Reports</option>
                                          <option value="finished" <?php if($status=='finished') echo "selected";?>>Completed Reports</option>
                                    </select>
                            </div>
                             <button style="margin-top: 20px; margin-left: 8px;" type="submit" name="find" class="btn btn-primary">Find</button>
                         </div>
                         <!-- end row -->  
                             
                        </form>
                        
                     <?php 
                     if(isset($samples_count)){
                        echo "<hr/>";
                        if($samples_count==0){
                            echo "<p class=\"text-center\">No Sales Record Found</p>";
                        }
                        else { ?>
                        <table id="data-table" class="table table-condensed table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Customer ID</th>
                                <th>Organization</th>
                                <th>Type</th>
                                <th>Arrival Time</th>
                                <th>Status</th>
                                <th>Lab</th>
                                <th>No. of tests</th>
                                <th>Total Payment(Rs.)</th>
                                <th>Pending Payment(Rs.)</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php 
                            $counter = 1;            
                            while($sample = mysqli_fetch_assoc($total_samples)) {  
                            ?>
                            <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $sample["customer_id"]; ?></td>
                            <?php $customer = get_customer_by_type($sample['customer_id'],$sample['type']); ?>
                            <td><?php echo $customer["organization"]; ?></td>
                            <td><?php echo beautify_fieldname($sample["type"]); ?></td>
                            <?php date_default_timezone_set('Asia/Karachi'); ?>
                            <td><?php echo date('d-m-Y h:i:s A',strtotime($sample['timestamp']));?></td>
                            <td><?php echo beautify_fieldname($sample["status"]); ?></td>
                            <?php
                            $lab_sample=get_lab_sample($sample['sample_id'],$sample['lab']);?> 
                            <td><?php echo beautify_fieldname($sample["lab"]); ?></td>    
                            <td><?php echo $lab_sample["no_of_tests"]; ?></td>
                            <td><?php echo $sample["payment"]; ?></td>
                            <td><?php echo $sample["payment_pending"]; ?></td>
                        </tr>
                           <?php } ?>
                            </tbody>
                        </table>
                        <hr/>
                     <!-- second table for showing total stats --> 
                       <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="color:grey;">Total Payment (Rs.)</th>
                                    <th class="text-center" style="color:grey;">Received Payment (Rs.)</th>
                                    <th class="text-center" style="color:grey;">Pending Payment (Rs.)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center"><?php echo $total_statistics[0]; ?></td>
                                    <td class="text-center"><?php echo $total_statistics[1]; ?></td>
                                    <td class="text-center"><?php echo $total_statistics[2]; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- second table for showing total stats -->    
                        
                    <!-- begin row -->
                     <div class="row">
                        <div class="col-12 col-sm-12 col-md-12" style="text-align: center;">
                            <form id="second_form" action="generate_sales_report.php" method="post">
                            <input type="hidden" name="customer_type" value="<?php echo $customer_type;?>">
                            <input type="hidden" name="lab" value="<?php echo $lab;?>">
                            <input type="hidden" name="order_status" value="<?php echo $status;?>">
                            <input type="hidden" name="dates" value="<?php echo $time_period;?>">
                            <button class="btn btn-info" type="submit" name="generate_report">Generate Report</button>
                            </form>
                          
                        </div> 
                     </div>
                     <!-- end row -->
                     <?php } ?>
                     <!-- end else -->
                     <?php } ?>
                     <!-- end if -->
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
<script src="../assets/plugins/jquery-daterangepicker/moment.min.js"></script>
<script src="../assets/plugins/jquery-daterangepicker/daterangepicker.js"></script>
<script src="../assets/plugins/DataTables/media/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/js/table_manage_default_demo.js"></script>
<script src="../assets/js/app.js"></script>
<script>
    var start = moment().subtract(29, 'days');
    var end = moment();

    $('#dates').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'Last 60 Days': [moment().subtract(59, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Last 3 Months': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'This Year': [moment().startOf('year'), moment().endOf('year')]
        },
        locale:{
            format: 'MMMM D, YYYY'
        }
    });
    <?php if($time_period!=""){ ?>    
      $('#dates').val('<?php echo $time_period; ?>');
    <?php } ?>   
</script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
        TableManageDefault.init();
    });
</script>
</body>
</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>