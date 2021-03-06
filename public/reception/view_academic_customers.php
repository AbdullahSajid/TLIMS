<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php
access_receptionist();
$reception = get_user_info('reception');
if(isset($_POST['filter'])){
    // Initializing variables to use in reporting form at the bottom of the page
    $type = $_POST['customer_type'];
    $lab = $_POST['lab'];
    $time_period = $_POST['dates'];
    $order = $_POST['order'];
    $starting_date = "";
    $ending_date = "";
    

    $query  = "SELECT * ";
    $query .= "FROM academic_students,orders ";
    $query .= "WHERE academic_students.customer_id=orders.customer_id ";
    $query .= "AND type='{$type}' ";
    if($lab!='all'){
      $query .= "AND lab='{$lab}' ";  
    }
    if(!empty($time_period)){
        $dates = explode(' - ',$time_period);
        $starting_date = date("Y-m-d", strtotime($dates[0]));
        $ending_date = date("Y-m-d", strtotime($dates[1]));
      
        $query .= "AND creation_time BETWEEN '$starting_date' AND '$ending_date' + INTERVAL 1 DAY ";
        
    }
     if($order=='asc')
        $query .= "ORDER BY orders.id ASC "; 
     else
        $query .= "ORDER BY orders.id DESC "; 
     $customer_set = mysqli_query($connection, $query);
     confirm_query($customer_set);
     if(mysqli_num_rows($customer_set)>0) 
        $selected_customers = mysqli_num_rows($customer_set);
     else
        $not_found=1;
}
else{
    $type = 'academic';
    $lab = 'all';
    $time_period = "";
    $order = "desc";
}
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="UTF-8"/>
    <title>View Academic customers</title>
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
    <link href="../assets/plugins/parsley/src/parsley.css" rel="stylesheet"/>
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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-filter"></i>
                        <span>Filter Customers</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="view_academic_customers.php">Academic Customers</a></li>
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
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Filter Customers</a></li>
            <li class="active">Academic customers</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Academic customers
            <small>Filter Academic Customers</small>
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
                            <?php if(isset($selected_customers)) echo 'Number of Customers: ' . $selected_customers;
                            elseif(isset($not_found)) echo 'Number of Customers: 0';
                            else echo 'Filter Academic Customers';
                            ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <form action="view_academic_customers.php" method="POST">
                        <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                <label for="customer_type" class="control-label">Customers Type *</label>
                                    <select class="form-control" id="customer_type" name="customer_type" required>
                                      <option value="">Select Customer Type</option>
                                      <option value="academic" <?php if($type=='academic') echo "selected";?>>Academic</option>
                                         
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
                                <label class="control-label" for="order">Order *</label>
                                    <select class="form-control" id="order" name="order" required>
                                          <option value="">Select Order</option>
                                          <option value="asc" <?php if($order=='asc') echo "selected";?>>Ascending</option>
                                          <option value="desc" <?php if($order=='desc') echo "selected";?>>Descending</option>
                                    </select>
                            </div>
                             <button style="margin-top: 20px; margin-left: 8px;" type="submit" name="filter" class="btn btn-primary">Filter</button>
                        </div>
                      <!-- end row -->  
                             
                        </form>
                        <?php if(isset($selected_customers)) {?>
                        <hr/>
                        <table id="data-table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Registration No.</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Phone</th>
                                <th>Arrival time</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php 
                             $counter = 0;                                 
                            foreach($customer_set as $cus){ 
                              $counter +=1;  
                            ?>
                            <tr>
                                <td><?php echo $counter; ?></td>
                                <td><?php echo $cus['customer_id']; ?></td>
                                <td><?php echo $cus['name']; ?></td>
                                <td><?php echo $cus['reg_no']; ?></td>
                                <td><?php echo $cus['designation']; ?></td>
                                <td><?php echo $cus['department']; ?></td>
                                <td><?php echo $cus['phone']; ?></td>
                                <td><?php echo date('d-m-Y h:i:s A',strtotime($cus['creation_time'])); ?></td>
                                <td><span><a href="view_customer_sample_record.php?customer_id=<?php echo urlencode($cus["customer_id"]); ?>"><i class="fa fa-eye fa-lg"></i> &nbsp;View Detail 
                                </a></span></td>
                            </tr>
                           <?php } ?>
                            </tbody>
                        </table>
                     <?php } elseif(isset($not_found)&&$not_found==1){
                        echo "<hr/>";
                        echo "<p class=\"text-center\">No Customer Found</p>";
                        } ?>
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
<script src="../assets/js/reception_dashboard/reception_pages.js"></script>
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