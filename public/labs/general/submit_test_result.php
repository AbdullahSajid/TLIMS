<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php require_once("../../../includes/fileUpload/UploadFile.php"); ?>
<?php 
$lab = access_lab_manager();
$beautify_lab = beautify_fieldname($lab);
$user = get_user_info($lab);
$max_file_size = 2*1024*1024;
use includes\FileUpload\UploadFile;
if(isset($_GET['sample_id'])&&isset($_GET['test'])){
    $sample_id = $_GET['sample_id'];
    $test = $_GET['test'];
    $sample = get_lab_sample($sample_id,$lab);
    if(!$sample){
        $_SESSION["message"] = "Sample ID isn't valid";
        redirect_to("lab_pending_samples.php");
    }
    $test_names=find_test_names_of_sample_by_lab_sample($sample,$lab);
    
    $test_table_exists=FALSE;
    $test_name="";
    foreach($test_names as $testname){
    $test_table = uglify_fieldname($testname) . '_' . 'test';
    if($test_table==$test){
        $test_name = $testname;
        $test_table_exists=TRUE;
    }
   }
    if(!$test_table_exists){
        $_SESSION["message"] = "Test isn't valid";
        redirect_to("lab_pending_samples.php");
    }
    $sample_test=get_test($sample_id,$test);
    if($sample_test['status']!='submitted'){
         $_SESSION["message"] = "Sample Test Results already submitted";
         redirect_to("lab_pending_samples.php");
    }
    
}
else{
     $_SESSION["message"] = "Access denied due to incorrect url";
     redirect_to("lab_pending_samples.php");
}
if(isset($_POST['submit'])){
    // validations
    $fields = array("sample_id","test_standard","temperature","humidity");
    validate_presences($fields);
    $fields_with_max_lengths = array("sample_id" => 12,"test_standard" => 400,"temperature" => 30,"humidity" => 30);
    validate_max_lengths($fields_with_max_lengths);
    //validating image upload
    $sample_test_file_name = "";
    $error_messages = array();
    if($_FILES['test_file']['name'] != ""){
    $destination = __DIR__ . '/../../../includes/sample-test-results/' . $lab . '/';
    try {
        $upload = new UploadFile($destination);
        $upload->setMaxSize($max_file_size);
        $upload->allowAllTypes();
        $upload->upload();
        $error_messages = $upload->getMessages();
        $sample_test_file_name = $upload->getFileName();
    } catch (Exception $e) {
        $errors['file'] = $e->getMessage();
    }
}
  if (empty($errors)&&empty($error_messages)) {
        $sample_id = mysql_prep($_POST["sample_id"]);
        $test_standard = mysql_prep($_POST["test_standard"]);
        $temperature = mysql_prep($_POST["temperature"]);
        $humidity = mysql_prep($_POST["humidity"]);
        $test_results = mysql_prep($_POST["test_results"]);
        $test_conditions = mysql_prep($_POST["test_conditions"]);
        mysqli_autocommit($connection, false);
        $flag = true;
       // submit sample test result 
        $status = 'finished';
        $finished_date = date('Y-m-d H:i:s');
        $result = submit_sample_test_result($test,$sample_id,$status,$test_standard,$temperature,$humidity,$test_results,$test_conditions,$sample_test_file_name,$finished_date);

      if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['first_query']= mysqli_error($connection);
      }
      // commit and check order status to change if it's the only or last finished test
      if($flag){
          $all_tests_finished = TRUE;
          foreach($test_names as $testname){
                $test_table = uglify_fieldname($testname) . '_' . 'test';
                $samples_test=get_test($sample_id,$test_table);
                if($samples_test['status']=='submitted'){
                     $all_tests_finished = FALSE;
                }
           }
          if($all_tests_finished){
             $flag = TRUE;
             $status='finalized';
             $completion_date = date('Y-m-d H:i:s');
             $result = update_orders_status_and_completion_date($sample_id,$status,$completion_date);
             if (!($result && mysqli_affected_rows($connection) == 1)) {
                  $flag = false;
                  $errors['second_query']= mysqli_error($connection);
                }
              if($flag){
                  mysqli_commit($connection);
                  $_SESSION["message"] = "All sample tests completed successfully";
                  redirect_to("lab_pending_samples.php?success=1");
              }
              else{
                  mysqli_rollback($connection);
                  $_SESSION["message"] = "Error Submitting Sample Test Result";
                  redirect_to("lab_pending_samples.php");
              }
          }
          else{
              mysqli_commit($connection);
              $_SESSION["message"] = "Sample Test Result Submitted Successfully";
              redirect_to("lab_pending_samples.php?success=1");
          }
          
      }
      else{
          mysqli_rollback($connection);
          $_SESSION["message"] = "Sample Sumission Failed";
      }
       
  }
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
    <title>Submit Test Results</title>
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
     <link href="../../assets/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet"/>
     <!-- all packeged css files is also available in frola editor folder, we don't need to import all css files, here we are importing it because table border is not working in packeged css files-->
     <link href="../../assets/plugins/froala_editor/css/froala_editor.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/froala_style.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/table.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/code_view.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/char_counter.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/fullscreen.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/help.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/quick_insert.min.css" rel="stylesheet">
     <link href="../../assets/plugins/froala_editor/css/plugins/special_characters.min.css" rel="stylesheet">
<!--     we donot need this line break feature here but it's very useful-->
<!--     <link href="../../assets/plugins/froala_editor/css/plugins/line_breaker.min.css" rel="stylesheet">-->
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
                <li class="has-sub active">
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
       <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Pending Samples</a></li>
            <li class="active">Test Results Submission</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Sample Test Results Submission
            <small>Submit test results</small>
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
                        <h4 class="panel-title"><?php echo $test_name; ?> Test Results Submission</h4>
                    </div>
                    <!-- start notification  -->
                    <?php 
//                 display form errors if any
                    if(isset($error_messages)){
                        $errors = array_merge($errors,$error_messages);
                    }
                    echo form_errors($errors);
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                    <!-- end notification -->
                    <div class="panel-body">
            <?php if($sample['sample_test_detail']) {?>
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
                               <?php echo $sample['sample_test_detail']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <!-- end col-12 -->
        </div>
        <!-- end row -->
          <?php } ?>  
                        <form action="submit_test_result.php?sample_id=<?php echo $sample_id;?>&test=<?php echo $test;?>" method="POST" enctype="multipart/form-data" onsubmit="return postForm();">
                        <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                <label class="control-label">Sample ID</label>
                                    <input class="form-control" type="text" id="sample_id" name="sample_id" value="<?php echo $sample_id;?>" readonly/>
                                    
                            </div>
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                <label class="control-label">Test Standard</label>
                                    <input class="form-control" type="text" id="test_standard" name="test_standard" placeholder="ISO 13934-1" required/>
                                    
                            </div>
                              
                         </div>
                        <!-- end row -->
                         <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                <label class="control-label">Temperature</label>
                                    <div class="input-group"> 
                                    <input class="form-control" type="number" id="temperature" name="temperature" placeholder="35" step="0.5" required/>
                                    <span class="input-group-addon">&deg;C</span> 
                                   </div>
                                    
                            </div>
                             <div class="form-group col-6 col-sm-6 col-md-6">
                                <label class="control-label">Humidity</label>
                                   <div class="input-group"> 
                                    <input class="form-control" type="number" id="humidity" name="humidity" placeholder="23" step="0.5" required/>
                                    <span class="input-group-addon">&#37;</span>
                                   </div>
                                    
                            </div>
                              
                         </div>
                        <!-- end row -->

                        <!-- begin row -->
                        <div class="form-group row">
                              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>"/>
                            <div class="form-group col-12 col-sm-12 col-md-12">
                                <label class="control-label">Test File (if any)</label>
                                    
                                <input class="form-control" type="file" id="test_file" name="test_file" />   
                            </div>
                        </div>
                        <!-- end row -->
                         <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-12 col-sm-12 col-md-12">
                                <label class="control-label">Test Results</label>
                                <textarea class="form-control" id="test_results" name="test_results"></textarea>
                             </div>
                         
                         </div>
                        <!-- end row --> 
                           <!--begin row-->
                            <div class="form-group row">
                                  
                                    <div class="col-12 col-sm-12 col-md-12 checkbox checkbox-inline checkbox-circle">
                                    <!-- begin col-6 -->
                                    <div class="col-6 col-sm-6 col-md-6 text-right">
                                        <input type="checkbox" id="set_default_content">
                                        <label for="set_default_content">
                                            Set default content
                                        </label>
                                    </div>
                                    <!-- end col-6 -->
                                    <!-- begin col-6 -->
                                    <div class="col-6 col-sm-6 col-md-6">
                                         <input type="checkbox" id="send_test_file_data">
                                        <label for="send_test_file_data">
                                            Send Test File Only
                                        </label>
                                    </div>
                                    <!-- end col-6 -->
                                </div> 
                            </div>
                             <!-- end row -->
                         <!-- begin row -->
                         <div class="form-group row">
                            
                             <div class="form-group col-12 col-sm-12 col-md-12">
                                <label class="control-label">Test Conditions</label>
                                <textarea class="form-control" id="test_conditions" name="test_conditions"></textarea>
                             </div>
                         
                         </div>
                        <!-- end row -->
                         <!--begin row-->
                                <div class="form-group row">
                                  <!-- begin col-6 -->
                                   <div class="col-md-6">

                                    <div class="checkbox checkbox-circle">
                                        <input type="checkbox" id="set_default_content_2">
                                        <label for="set_default_content_2">
                                            Set default content
                                        </label>
                                    </div>
                                   </div>
                                   <!-- end col-6 -->
                                   <!-- begin col-6 -->
                                   <div class="col-6 col-md-6">
                                   <div class="pull-right">
                                    <button style="margin-left: 8px;" type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    <button style="margin-left: 8px;" type="submit" name="cancel" onclick="location.href='lab_pending_samples.php';" class="btn btn-default">Cancel</button>
                                    </div>
                                    </div>
                                    <!-- end col-6 -->
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
        
    </div>
    <!-- end #content -->
    
    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i
            class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
    
    </div>
    <!-- end page-container -->
    <!-- ================== BEGIN BASE JS ================== -->
<script src="../../assets/plugins/jquery/jquery-1.11.0.min.js"></script>
<script src="../../assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="../../assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="../../assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="../../assets/js/lab_dashboard/lab_pages.js"></script>
<script src="../../assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
<!--All the packeged js for froala editor-->
<script src="../../assets/plugins/froala_editor/js/froala_editor.pkgd.min.js"></script>
<script src="../../assets/js/submit_sample_test_results.js"></script>
<script src="../../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->
<script>
    $(document).ready(function () {
        App.init();
        SubmitTestResults.init();
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