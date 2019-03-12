<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php access_admin();?>
 <?php 
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
      
        $time_interval = "WHERE timestamp BETWEEN '$starting_date' AND '$ending_date' + INTERVAL 1 DAY ";  
    }
    if($stats_by=='overall_aggregiation'){
       $query  = "SELECT type,count(id) FROM orders ";
        // time period will never empty
        if(!empty($time_period)){
            $query .= $time_interval;
        }
        if($lab!='all'){
            $query .= "AND lab='{$lab}' ";
        }
        $query .= "GROUP BY type ";
    }
    else{
        $query  = "SELECT MONTH(timestamp) as M, year(timestamp) as Y ,CONCAT((SELECT DATE_FORMAT((DATE_ADD('2018-01-01',INTERVAL M-1 MONTH)),\"%b\")),' ',(SELECT Y)) as label,count(id), type FROM orders ";
        // time period will never empty
        if(!empty($time_period)){
            $query .= $time_interval;
        }
        if($lab!='all'){
            $query .= "AND lab='{$lab}' ";
        }
        $query .= "GROUP BY M,Y,type ORDER BY Y,M ";
        // output data format
        // 'M','Y','label',count(id),type
        // '3','2018','Mar 2018', 3 , 'commercial' 
        // '3' is the month number, we converted month number to month name by using a technique discussed in stack overflow
    }
    $total_statistics = mysqli_query($connection, $query);
    $stats_count = mysqli_num_rows($total_statistics);  
    confirm_query($total_statistics);
    // total customrs  
    $total_customers = 0;
    foreach($total_statistics as $stats){
         // calculating total sum to find percentage of each type
        $total_customers +=$stats['count(id)'];
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
    <title>Customers Statistics Chart</title>
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
                        <li><a href="sales_statistics.php">Sales Statistics</a></li>
                        <li><a href="sales_statistics_charts.php">Sales Statistics Chart</a></li>
                        <li class="active"><a href="customers_statistics_charts.php">Customers Statistics Chart</a></li>
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
            <li class="active">Customers Statistics Chart</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Customers Statistics Chart
            <small>See customers statistics chart</small>
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
                        <?php if(isset($total_customers)) echo "Total Customers: " . $total_customers; else echo "Customer Statistics Chart"; ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form action="customers_statistics_charts.php" method="POST">
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
                      <?php if(isset($_POST['find'])&&isset($stats_count)&&$stats_count==0){ 
                            echo "<hr/>";
                            echo "<p class=\"text-center\">No Customers Statistics Found</p>";
                        }
                      ?>
                       
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-12 -->
        </div>
        <!-- end row -->
        <?php if(isset($_POST['find'])&&isset($stats_count)&&$stats_count!=0&&$stats_by=='overall_aggregiation'){ ?>
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
          
        </div>
        <!-- end graphs row -->
        <?php } ?>
        <?php if(isset($_POST['find'])&&isset($stats_count)&&$stats_count!=0&&$stats_by=='monthly_aggregiation'){ ?>
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
                        <h4 class="panel-title">Stacked Column Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="stacked-column-chart" class="height-sm"></div>
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
                        <h4 class="panel-title">Spline Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="spline-chart" class="height-sm"></div>
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
                        <!-- stacked bar 100% chart-->
                        <h4 class="panel-title">Stacked Percentage Bar Chart</h4>
                    </div>
                    <div class="panel-body">
                        <div id="stacked-bar-chart" class="height-sm"></div>
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
<script src="../assets/js/admin_dashboard/admin_pages.js"></script>
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
    <?php if(isset($_POST['find']) && $stats_by=='overall_aggregiation' && $stats_count!=0){ ?>    
    // column chart options
        var options = {
            animationEnabled: true,
            exportEnabled: true,
            title: {
                text: "Customers Statistics By Column Chart"             
            },
            axisY: {
                title: "No. of Customers",
                includeZero: true
            },
            data: [{
                // Change type to "doughnut", "line", "splineArea", etc.
                type: "column",
                dataPoints: [
                    <?php
                     foreach($total_statistics as $stats){
                        $customer_type = ucwords($stats['type']);
                        echo "{label: \"{$customer_type}\", y: {$stats['count(id)']}},"; 
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
        title: {
            text: "Customers Statistics By Bar Chart"
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
            toolTipContent: "<span style=\"color:#62C9C3\">{indexLabel}:</span> <span style=\"color:#CD853F\"><strong>{y}</strong></span>",
            indexLabelPlacement: "inside",
            indexLabelFontColor: "white",
            indexLabelFontWeight: 600,
            indexLabelFontFamily: "Verdana",
            type: "bar",
            dataPoints: [
                <?php
                 foreach($total_statistics as $stats){
                    $customer_type = $stats['type'];
                    if($customer_type=='commercial'){
                        $customer_type = 'C';
                    }
                    elseif($customer_type=='academic commercial'){
                         $customer_type = 'AC';
                    }
                    else{
                         $customer_type = 'A';
                    }
                    $type_percentage = ($stats['count(id)'] / $total_customers) * 100;
                    $type_percentage=number_format((float)$type_percentage,1, '.', '');  // Outputs -> 15.5
                    echo "{y: {$stats['count(id)']}, label: \"{$type_percentage}%\", indexLabel: \"{$customer_type}\" },";
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
        title: {
            text: "Customers Statistics By Pie Chart"
        },
        data: [{
                type: "pie",
                startAngle: 45,
                showInLegend: "true",
                legendText: "{label}",
                indexLabel: "{label} ({y})",
                yValueFormatString:"#,##0.#"%"",
                dataPoints: [
                    <?php
                     foreach($total_statistics as $stats){
                        $customer_type = ucwords($stats['type']);
                        echo "{label: \"{$customer_type}\", y: {$stats['count(id)']}},"; 
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
        title: {
            text: "Customers Statistics By Pyramid Chart"
        },
        data: [{
            type: "pyramid",
            indexLabelFontSize: 12,
            showInLegend: true,
            legendText: "{indexLabel}",
            toolTipContent: "<b>{indexLabel}:</b> {y}%",
            dataPoints: [
                <?php
                    
                     foreach($total_statistics as $stats){
                        $type_percentage = ($stats['count(id)'] / $total_customers) * 100;
                        $type_percentage=number_format((float)$type_percentage,1, '.', '');  // Outputs -> 15.5
                        $customer_type = ucwords($stats['type']);
                        echo "{ y: {$type_percentage}, indexLabel: \"{$customer_type}\"},"; 
                     }
                ?>
            ]
        }]
    };
    // pyramid chart options    
    $("#column-chart").CanvasJSChart(options);
    $("#bar-chart").CanvasJSChart(options_1);
    $("#pie-chart").CanvasJSChart(options_2);
    $("#pyramid-chart").CanvasJSChart(options_3);
    <?php } ?>   
    <?php if(isset($_POST['find']) && $stats_by=='monthly_aggregiation' && $stats_count!=0){ ?>  
    // stacked column chart options
    var options = {
	animationEnabled: true,
    exportEnabled: true,
	title:{
		text: "Customers Monthly Stats By Stacked Column Chart"   
	},
	axisY:{
		title: "No. of Customers"
	},
	toolTip: {
		shared: true,
//		reversed: true
	},
	data: [{
		type: "stackedColumn",
		name: "Academic Customers",
		showInLegend: "true",
		yValueFormatString: "#,##0",
		dataPoints: [
            <?php
                // get unique labels 
                foreach($total_statistics as $stats){
                    $labels_array[] = $stats['label'];
                    
                }
                 $unique_labels = array_unique($labels_array);
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
		type: "stackedColumn",
		name: "Commercial Customers",
		showInLegend: "true",
		yValueFormatString: "#,##0",
		dataPoints: [
			<?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
		type: "stackedColumn",
		name: "Academic-Commercial Customers",
		showInLegend: "true",
		yValueFormatString: "#,##0",
		dataPoints: [
			<?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
// stacked column chart options
// spline chart options
var options_1 = {
	animationEnabled: true,
    exportEnabled: true,
	title:{
		text: "Customers Monthly Stats By Spline Chart"
	},
	axisX: {
		valueFormatString: "MMM YYYY"
	},
	axisY: {
		title: "No. of Customers",
		includeZero: true
	},
    toolTip: {
		shared: true,
//		reversed: true
	},
	data: [{
        type: "spline",
        name: "Academic Customers",
        showInLegend: "true",
		yValueFormatString: "#,##0",
		xValueFormatString: "MMMM YYYY",
		dataPoints: [
            <?php
                // get unique labels 
                foreach($total_statistics as $stats){
                    $labels_array[] = $stats['label'];
                    
                }
                 $unique_labels = array_unique($labels_array);
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic'){
                             $year = $stats['Y'];
                       // Javascript  Date function takes months from 0 to 11
                             $month = $stats['M'] - 1;
                             echo "{x: new Date({$year},{$month}), y: {$stats['count(id)']}},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         $month_year = explode(' ',$label);
                         // coverting month name to month number
                     /* https://stackoverflow.com/questions/3283550/convert-month-from-name-to-number*/
                          $date = date_parse($month_year[0]);
                        // Javascript  Date function takes months from 0 to 11
                          $mon = $date['month'] - 1;
                         echo "{x: new Date({$month_year[1]},{$mon}),y: 0},";
                     }
                     
                 }
            ?>
			
		]
	},
    {
        type: "spline",
        name: "Commercial Customers",
        showInLegend: "true",
		yValueFormatString: "#,##0",
		xValueFormatString: "MMMM YYYY",
		dataPoints: [
              <?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             $year = $stats['Y'];
                             $month = $stats['M'] - 1;
                             echo "{x: new Date({$year},{$month}), y: {$stats['count(id)']}},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         $month_year = explode(' ',$label);
                         // coverting month name to month number
                          $date = date_parse($month_year[0]);
                          $mon = $date['month'] - 1;
                         echo "{x: new Date({$month_year[1]},{$mon}),y: 0},";
                     }
                     
                 }
            ?>
			
		]
	},
    {
        type: "spline",
        name: "Academic-Commercial Customers",
        showInLegend: "true",
		yValueFormatString: "#,##0",
		xValueFormatString: "MMMM YYYY",
		dataPoints: [
             <?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             $year = $stats['Y'];
                             $month = $stats['M'] - 1;
                             echo "{x: new Date({$year},{$month}), y: {$stats['count(id)']}},";
                             $label_written = 1;
                             break 1;
                         }
                     }
                     if($label_written == 0){
                         $month_year = explode(' ',$label);
                         // coverting month name to month number
                          $date = date_parse($month_year[0]);
                          $mon = $date['month'] - 1;
                         echo "{x: new Date({$month_year[1]},{$mon}),y: 0},";
                     }
                     
                 }
            ?>
		]
	}]
};
// spline chart options
// stacked bar chart options
var options_2 = {
	animationEnabled: true,
    exportEnabled: true,
//	theme: "light2", //"light1", "dark1", "dark2"
	title:{
		text: "Customers Monthly Stats By Stacked Percentage Bar Chart"             
	},
	axisY:{
		interval: 10,
		suffix: "%"
	},
	toolTip:{
		shared: true
	},
	data:[{
		type: "stackedBar100",
		toolTipContent: "{label}<br><span style=\"color:#4F81BC\">{name}:</span> {y} (#percent%)",
		showInLegend: true, 
		name: "Academic Customers",
		dataPoints: [
			<?php
                // get unique labels 
                foreach($total_statistics as $stats){
                    $labels_array[] = $stats['label'];
                    
                }
                 $unique_labels = array_unique($labels_array);
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
		type: "stackedBar100",
		toolTipContent: "<span style=\"color:#C0504E\">{name}:</span> {y} (#percent%)",
		showInLegend: true, 
		name: "Commercial Customers",
		dataPoints: [
			<?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='commercial'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
		type: "stackedBar100",
		toolTipContent: "<span style=\"color:#9BBB58\">{name}:</span> {y} (#percent%)",
		showInLegend: true, 
		name: "Academic-Commercial Customers",
		dataPoints: [
			<?php
                 foreach($unique_labels as $label){
                     $label_written = 0;
                     foreach($total_statistics as $stats){
                        if($stats['label']==$label&&$stats['type']=='academic commercial'){
                             echo "{y: {$stats['count(id)']},label: \"{$label}\"},";
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
// stacked bar chart options
$("#stacked-column-chart").CanvasJSChart(options); 
$("#spline-chart").CanvasJSChart(options_1);
$("#stacked-bar-chart").CanvasJSChart(options_2);
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