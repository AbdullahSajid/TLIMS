<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php
access_receptionist();
$reception = get_user_info('reception');
  if(isset($_POST['find'])){
    
    $lab = $_POST['lab'];
    $stats_by = $_POST['stats_by'];
    $time_period = $_POST['dates'];
    $starting_date = "";
    $ending_date = "";
    
    if(!empty($time_period)){
        $dates = explode(' - ',$time_period);
        $starting_date = date("Y-m-d", strtotime($dates[0]));
        $ending_date = date("Y-m-d", strtotime($dates[1]));
      
        $time_interval = "AND timestamp BETWEEN '$starting_date' AND '$ending_date' + INTERVAL 1 DAY ";  
    }
      
    if($stats_by=='overall_aggregiation'){
       $simple_query = "SELECT count(id) as total_count,sum(payment) as total_payment,sum(payment_received) as payment_received,sum(payment_pending) as payment_pending FROM orders WHERE type IN('commercial','academic commercial') ";
        
       $detailed_query  = "SELECT type,count(id) as total_count,sum(payment) as total_payment,sum(payment_received) as payment_received,sum(payment_pending) as payment_pending FROM orders WHERE type IN('commercial','academic commercial') ";
        // time period will never empty
        if(!empty($time_period)){
            $simple_query .= $time_interval;
            $detailed_query .= $time_interval;
        }
        if($lab!='all'){
            $simple_query .= $time_interval;
            $detailed_query .= "AND lab='{$lab}' ";
        }
        $detailed_query .= "GROUP BY type ";
    }
    else{
        $simple_query  = "SELECT MONTH(timestamp) as M, year(timestamp) as Y ,CONCAT((SELECT DATE_FORMAT((DATE_ADD('2018-01-01',INTERVAL M-1 MONTH)),\"%b\")),' ',(SELECT Y)) as label, count(id) as total_count, sum(payment) as total_payment, sum(payment_received) as payment_received, sum(payment_pending) as payment_pending FROM orders WHERE type IN('commercial','academic commercial') ";
        
        $detailed_query  = "SELECT MONTH(timestamp) as M, year(timestamp) as Y ,CONCAT((SELECT DATE_FORMAT((DATE_ADD('2018-01-01',INTERVAL M-1 MONTH)),\"%b\")),' ',(SELECT Y)) as label, count(id) as total_count, sum(payment) as total_payment, sum(payment_received) as payment_received, sum(payment_pending) as payment_pending, type FROM orders WHERE type IN('commercial','academic commercial') ";
        // time period will never empty
        if(!empty($time_period)){
            $simple_query .= $time_interval;
            $detailed_query .= $time_interval;
        }
        if($lab!='all'){
            $simple_query .= "AND lab='{$lab}' ";
            $detailed_query .= "AND lab='{$lab}' ";
        }
        $simple_query .= "GROUP BY M,Y ORDER BY Y,M ";
        $detailed_query .= "GROUP BY M,Y,type ORDER BY Y,M ";
    }
    
    $simple_statistics = mysqli_query($connection, $simple_query);
    $stats_count = mysqli_num_rows($simple_statistics);
    confirm_query($simple_statistics);
    $detailed_statistics = mysqli_query($connection, $detailed_query);
    // $detailed_stats_count = mysqli_num_rows($detailed_statistics);
    confirm_query($detailed_statistics);
    // total customrs  
    $total_sales = 0;
    foreach($simple_statistics as $stats){
        // calculating total sales
        $total_sales += $stats['total_count'];
    }     
}
else{
    $lab = "all";
    $stats_by = "overall_aggregiation";
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
    <title>Sales Statistics Chart</title>
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
                        <li><a href="sales_statistics.php">Sales Statistics</a></li>
                        <li class="active"><a href="sales_statistics_charts.php">Sales Statistics Chart</a></li>
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
            <li><a href="javascript:;">Sales</a></li>
            <li class="active">Sales Statistics Chart</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Sales Statistics Chart
            <small>See sales statistics chart</small>
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
                        <h4 class="panel-title">
                        <?php if(isset($total_sales)) echo "Total Sales: " . $total_sales; else echo "Sales Statistics Chart"; ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form action="sales_statistics_charts.php" method="POST">
                        <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-12 col-sm-12 col-md-12">
                                <label class="control-label">Time Period *</label>
                                  <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input id="dates" name="dates" type="text" class="form-control" required/>
                                  </div>
                             </div>
                             
                        </div>
                        <!-- end row -->
                        <!-- begin row -->
                         <div class="form-group row">
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                     <label for="lab">Lab *</label>
                                     <select id="lab" name="lab" class="form-control" required>
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
                                <div class="form-group col-5 col-sm-5 col-md-5">
                                <label class="control-label" for="stats_by">Stats By *</label>
                                    <select class="form-control" id="stats_by" name="stats_by" required>
                                          <option value="">Select Category for Stats</option>
                                          <option value="overall_aggregiation" <?php if($stats_by=='overall_aggregiation') echo "selected";?>>Overall Aggregiation</option>
                                          <option value="monthly_aggregiation" <?php if($stats_by=='monthly_aggregiation') echo "selected";?>>Monthly Aggregiation</option>
                                    </select>
                                </div>
                            
                            <button style="margin-top: 20px; margin-left: 8px;" type="submit" name="find" class="btn btn-primary">Find</button>
                         
                         </div>
                        <!-- end row -->
               
                        </form>
                       <!-- display message if no stats available-->
                      <?php if(isset($_POST['find'])&&$total_sales==0){ 
                            echo "<hr/>";
                            echo "<p class=\"text-center\">No Sales Statistics Found</p>";
                        }
                      ?>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-12 -->
        </div>
        <!-- end row -->
        <?php if(isset($_POST['find'])&&$total_sales!=0&&$stats_by=='overall_aggregiation'){ ?>
        <div class="row">
          
            <!-- begin col-6 -->
            <div class="col-md-6">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Column Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="column-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-6 -->
            
            <!-- begin col-6 -->
            <div class="col-md-6">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Bar Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="bar-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-6 -->
            
            <!-- begin col-6 -->
            <div class="col-md-6">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Pie Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="pie-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-6 -->
            
            <!-- begin col-6 -->
            <div class="col-md-6">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Pyramid Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="pyramid-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-6 -->
            
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Multiple Line Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="line-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-12 -->
          
        </div>
        <!-- end graphs row -->
        <?php } ?>
        <?php if(isset($_POST['find'])&&$total_sales!=0&&$stats_by=='monthly_aggregiation'){ ?>
        <div class="row">
          
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Multiple Axis Column Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="multiple-axis-column-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-12 -->
            
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Spline Chart With Secondary Axis</h4>
                    </div>
                    <div class="panel-body">
                        <div id="spline-chart_secondary_axis" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-12 -->
            
            <!-- begin col-12 -->
            <div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="flot-chart-3">
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
                        <h4 class="panel-title">Multi Series Bar Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="multi-series-bar-chart" class="height-sm"></div>
                    </div>
                </div>
            </div>
            <!-- end col-12 -->
            
            </div>
        <!-- end graphs row -->
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
<script src="../assets/plugins/jquery-daterangepicker/moment.min.js"></script>
<script src="../assets/plugins/jquery-daterangepicker/daterangepicker.js"></script>
<script src="../assets/plugins/jquery-canvasjs-charts/jquery.canvasjs.min.js"></script>
<script src="../assets/js/app.js"></script>
<script type="text/javascript">
    $(function() {
        
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
    <?php if(isset($_POST['find']) && $stats_by=='overall_aggregiation' && $total_sales!=0){ ?>    
    // column chart options
        var options = {
            animationEnabled: true,
            exportEnabled: true,
            theme: "light2",
            title: {
                text: "Sales Statistics By Column Chart"             
            },
            axisY: {
                title: "Amount (Rs.)",
                prefix: "Rs. ",
                includeZero: true
            },
            data: [{
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                toolTipContent: "<b>{label}:</b> Rs. {y}",
                dataPoints: [
                    <?php
                    // $simple_statistics data result will only have one row
                     foreach($simple_statistics as $stats){
                        echo "{label: \"Total Payment\", y: {$stats['total_payment']}},"; 
                        echo "{label: \"Received Payment\", y: {$stats['payment_received']}},"; 
                        echo "{label: \"Pending Payment\", y: {$stats['payment_pending']}}"; 
                     }
                    ?> 
                ]
            }]
        };  
    // column chart options
    // bar chart options
    var options_1 = {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title: {
            text: "Sales Statistics By Bar Chart"
        },	
        axisY: {
            tickThickness: 0,
            lineThickness: 0,
            valueFormatString: " ",
            gridThickness: 0                    
        },
        axisX: {
            tickThickness: 0,
            lineThickness: 0,
            labelFontSize: 18,
            labelFontColor: "Peru"				
        },
        data: [{
            indexLabelFontSize: 14,
            toolTipContent: "<span style=\"color:#62C9C3\">{indexLabel}:</span> <span style=\"color:#CD853F\"><strong>Rs. {y}</strong></span>",
            indexLabelPlacement: "inside",
            indexLabelFontColor: "white",
            indexLabelFontWeight: 600,
            indexLabelFontFamily: "Verdana",
            type: "bar",
            dataPoints: [
                <?php
                // only one iteration
                 foreach($simple_statistics as $stats){
                    // total payment
                     echo "{y: {$stats['total_payment']}, label: \"100.0%\", indexLabel: \"Total Payment\" },";
                     
                    // payment received
                    $payment_type_percentage = ($stats['payment_received'] / $stats['total_payment']) * 100;
                    $type_percentage=number_format((float)$payment_type_percentage,1, '.', '');  // Outputs -> ##.#
                    echo "{y: {$stats['payment_received']}, label: \"{$type_percentage}%\", indexLabel: \"Received Payment\" },";
                     
                    // payment pending
                    $payment_type_percentage = ($stats['payment_pending'] / $stats['total_payment']) * 100;
                    $type_percentage=number_format((float)$payment_type_percentage,1, '.', '');  // Outputs -> ##.#
                    echo "{y: {$stats['payment_pending']}, label: \"{$type_percentage}%\", indexLabel: \"Pending Payment\" },";
                 }
                ?>
            ]
        }]
    };
    // bar chart options
    // pie chart options
    var options_2 = {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title: {
            text: "Sales Statistics By Pie Chart"
        },
        data: [{
                type: "pie",
                startAngle: 45,
                showInLegend: "true",
                legendText: "{label}",
                toolTipContent: "<b>{label}:</b> Rs. {y}",
                indexLabel: "{label} (Rs. {y})",
                yValueFormatString:"#,##0.#"%"",
                dataPoints: [
                    <?php
                    // $simple_statistics data result will only have one row
                     foreach($simple_statistics as $stats){
                        echo "{label: \"Total Payment\", y: {$stats['total_payment']}},"; 
                        echo "{label: \"Received Payment\", y: {$stats['payment_received']}},"; 
                        echo "{label: \"Pending Payment\", y: {$stats['payment_pending']}}"; 
                     }
                    ?>    
                ]
        }]
    };
    // pie chart options    
    // pyramid chart options
       var options_3 = {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title: {
            text: "Sales Statistics By Pyramid Chart"
        },
        data: [{
            type: "pyramid",
            indexLabelFontSize: 12,
            showInLegend: true,
            legendText: "{indexLabel}",
            toolTipContent: "<b>{indexLabel}:</b> {y}%",
            dataPoints: [
                <?php
                    
                     foreach($simple_statistics as $stats){
                         // total payment
                     echo "{y: 100.0, indexLabel: \"Total Payment\" },";
                     
                    // payment received
                    $payment_type_percentage = ($stats['payment_received'] / $stats['total_payment']) * 100;
                    $type_percentage=number_format((float)$payment_type_percentage,1, '.', '');  // Outputs -> ##.#
                    echo "{y:{$type_percentage}, indexLabel: \"Received Payment\" },";
                     
                    // payment pending
                    $payment_type_percentage = ($stats['payment_pending'] / $stats['total_payment']) * 100;
                    $type_percentage=number_format((float)$payment_type_percentage,1, '.', '');  // Outputs -> ##.#
                    echo "{y:{$type_percentage}, indexLabel: \"Pending Payment\" },";
                     }
                ?>
            ]
        }]
    };
    // pyramid chart options
    // line chart options 
    var options_4 = {
	animationEnabled: true,
    exportEnabled: true,
	theme: "light2",
	title:{
		text: "Sales Statistics w.r.t Customer Types"
	},
	axisX:{
		title: "Payment Types",
		crosshair: {
			enabled: true,
			snapToDataPoint: true
		}
	},
	axisY: {
		title: "Amount (Rs.)",
        prefix: "Rs. ",
		crosshair: {
			enabled: true
		}
	},
	toolTip:{
		shared:true
	},
	data: [{
		type: "line",
		name: "Commercial Customers",
        showInLegend: true,
        toolTipContent: "{label}<br><span style=\"color:#6D78AD\">{name}:</span> Rs. {y}",
		lineDashType: "solid",
		dataPoints: [
			<?php
                // $detailed_statistics data result will have mostly two rows
                $found = false;
                 foreach($detailed_statistics as $stats){
                    if($stats['type']=='commercial'){
                        echo "{label: \"Total Payment\", y: {$stats['total_payment']},indexLabel:\"{$stats['total_count']} sales\"},"; 
                        echo "{label: \"Received Payment\", y: {$stats['payment_received']}},"; 
                        echo "{label: \"Pending Payment\", y: {$stats['payment_pending']}}";
                        $found = true;
                 }
                }
                if(!$found){
                    echo "{label: \"Total Payment\", y:0,indexLabel:\"No sale\"},"; 
                    echo "{label: \"Received Payment\", y:0},"; 
                    echo "{label: \"Pending Payment\", y:0}";
                }
            ?>
		]
	},
    {
		type: "line",
		name: "Academic Commercial Customers",
        showInLegend: true,
        toolTipContent: "<span style=\"color:#51CDA0\">{name}:</span> Rs. {y}",
		markerType: "square",
        lineDashType: "dash",
		dataPoints: [
			<?php
                // $detailed_statistics data result will have two rows
                $found = false;
                 foreach($detailed_statistics as $stats){
                    if($stats['type']=='academic commercial'){
                        echo "{label: \"Total Payment\", y: {$stats['total_payment']},indexLabel:\"{$stats['total_count']} sales\"},"; 
                        echo "{label: \"Received Payment\", y: {$stats['payment_received']}},"; 
                        echo "{label: \"Pending Payment\", y: {$stats['payment_pending']}}"; 
                        $found = true;
                 }
                }
                if(!$found){
                    echo "{label: \"Total Payment\", y:0,indexLabel:\"No sale\"},"; 
                    echo "{label: \"Received Payment\", y:0},"; 
                    echo "{label: \"Pending Payment\", y:0}";
                }
            ?> 
		]
	}]
};
    // line chart options
    $("#column-chart").CanvasJSChart(options);
    $("#bar-chart").CanvasJSChart(options_1);
    $("#pie-chart").CanvasJSChart(options_2);
    $("#pyramid-chart").CanvasJSChart(options_3);
    $("#line-chart").CanvasJSChart(options_4);
    <?php } ?>   
    <?php if(isset($_POST['find']) && $stats_by=='monthly_aggregiation' && $total_sales!=0){ ?>  
    // multiple axis column chart options
    var options = {
	animationEnabled: true,
    exportEnabled: true,
    theme: "light2",
	title:{
		text: "Monthly Sales Stats by Multiple Axis Column Chart"
	},	
	axisY: {
		title: "Amount (Rs.)",
        prefix: "Rs. ",
	},
    axisY2: {
		title: "No. of Sales",
        titleFontColor: "#4C9CA0",
	},
	toolTip: {
		shared: true
	},
	data: [{
		type: "column",
		name: "Total Payment",
		legendText: "Total Payment",
		showInLegend: true,
        toolTipContent: "{label}<br><span style=\"color:#99A1C6\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){
                        echo "{label: \"{$stats['label']}\", y: {$stats['total_payment']}},"; 
                }
            ?> 
		]
	},
	{
		type: "column",	
		name: "Received Payment",
		legendText: "Received Payment",
		showInLegend: true,
        toolTipContent: "<span style=\"color:#51CDA0\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['payment_received']}},"; 
                }
            ?>
		]
	},
    {
		type: "column",	
		name: "Pending Payment",
		legendText: "Pending Payment",
		showInLegend: true,
        toolTipContent: "<span style=\"color:#DF7970\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['payment_pending']}},"; 
                }
            ?>
		]
	},
    {
		type: "column",	
		name: "No. of Sales",
		legendText: "No. of Sales",
        showInLegend: true,
        axisYType: "secondary",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['total_count']}},"; 
                }
            ?>
		]
	}]
};  
// multiple axis column chart options
// secondary axis spline chart options
var options_1 = {
	animationEnabled: true,
    exportEnabled: true,
    theme: "light2",
	title:{
		text: "Monthly Sales Stats by Secondary Axis Spline Chart"
	},	
	axisY: {
		title: "Amount (Rs.)",
        prefix: "Rs. ",
	},
    axisY2: {
		title: "No. of Sales",
        titleFontColor: "#4C9CA0",
	},
	toolTip: {
		shared: true
	},
	data: [{
		type: "spline",
		name: "Total Payment",
		legendText: "Total Payment",
		showInLegend: true,
        toolTipContent: "{label}<br><span style=\"color:#99A1C6\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                /*we can use dates here, as we used in customer_statistics_charts.php but this is also fine*/
                foreach($simple_statistics as $stats){
                        echo "{label: \"{$stats['label']}\", y: {$stats['total_payment']}},"; 
                }
            ?> 
		]
	},
	{
		type: "spline",	
		name: "Received Payment",
		legendText: "Received Payment",
		showInLegend: true,
        toolTipContent: "<span style=\"color:#51CDA0\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['payment_received']}},"; 
                }
            ?>
		]
	},
    {
		type: "spline",	
		name: "Pending Payment",
		legendText: "Pending Payment",
		showInLegend: true,
        toolTipContent: "<span style=\"color:#DF7970\">{name}:</span> Rs. {y}",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['payment_pending']}},"; 
                }
            ?>
		]
	},
    {
		type: "spline",	
		name: "No. of Sales",
		legendText: "No. of Sales",
        showInLegend: true,
        axisYType: "secondary",
		dataPoints:[
			<?php
                foreach($simple_statistics as $stats){ 
                        echo "{label: \"{$stats['label']}\", y: {$stats['total_count']}},"; 
                }
            ?>
		]
	}]
};
// secondary axis spline chart options
// multi series bar chart options
var options_2 = {
    animationEnabled: true,
    exportEnabled: true,
    theme: "light2",
	title:{
		text: "Monthly Sales Stats w.r.t Customer Types"
	},
	axisY: {
		title: "Anount (Rs.)",
        prefix: "Rs. "
	},
	toolTip: {
		shared: true,
	},
	data: [{
		type: "bar",
		showInLegend: true,
		name: "Commercial Customers Total Payment",
        toolTipContent: "{label}<br><span style=\"color:#6D78AD\">{name}:</span> Rs. {y}",
		dataPoints: [
            <?php 
			    // get unique labels 
                foreach($detailed_statistics as $stats){
                    $labels_array[] = $stats['label'];
                    
                }
                 $unique_labels = array_unique($labels_array);
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             echo "{y: {$stats['total_payment']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
		     ?>
		]
	},
	{
		type: "bar",
		showInLegend: true,
		name: "Commercial Customers Received Payment",
        toolTipContent: "<span style=\"color:#51CDA0\">{name}:</span> Rs. {y}",
		dataPoints: [
            <?php
			 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             echo "{y: {$stats['payment_received']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
			?>
		]
	},
	{
		type: "bar",
		showInLegend: true,
		name: "Commercial Customers Pending Payment",
        toolTipContent: "<span style=\"color:#DF7970\">{name}:</span> Rs. {y}",
		dataPoints: [
			<?php
			 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             echo "{y: {$stats['payment_pending']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
			?>
		]
	},
    {
		type: "bar",
		showInLegend: true,
		name: "Academic Commercial Customers Total Payment",
        toolTipContent: "<span style=\"color:#4C9CA0\">{name}:</span> Rs. {y}",
		dataPoints: [
			<?php
			 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             echo "{y: {$stats['total_payment']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
			?>
		]
	},
    {
		type: "bar",
		showInLegend: true,
		name: "Academic Commercial Customers Received Payment",
        toolTipContent: "<span style=\"color:#AE7D99\">{name}:</span> Rs. {y}",
		dataPoints: [
			<?php
			 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             echo "{y: {$stats['payment_received']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
			?>
		]
	},
    {
		type: "bar",
		showInLegend: true,
		name: "Academic Commercial Customers Pending Payment",
        color: "#FF7974",
        toolTipContent: "<span style=\"color:#FF7974\">{name}:</span> Rs. {y}",
		dataPoints: [
		      <?php
			 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($detailed_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             echo "{y: {$stats['payment_pending']},label: \"{$label}\"},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         echo "{y: 0,label: \"{$label}\"},";
                     }
                     
                 }
			?>
		]
	}]
};
// multi series bar chart options
$("#multiple-axis-column-chart").CanvasJSChart(options); 
$("#spline-chart_secondary_axis").CanvasJSChart(options_1);
$("#multi-series-bar-chart").CanvasJSChart(options_2);
    <?php } ?>    
});
</script>
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