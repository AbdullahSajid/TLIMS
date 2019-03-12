<?php require_once("../../includes/session.php"); ?>
<?php require_once("../../includes/db_connection.php"); ?>
<?php require_once("../../includes/functions.php"); ?>
<?php require_once("../../includes/validation_functions.php"); ?>
<?php require_once("../../includes/fileUpload/UploadFile.php"); ?>
<?php 
access_receptionist();
$max_file_size = 2*1024*1024;
$reception = get_user_info('reception');
// For populating previous records
if(isset($_GET['prev_record'])){
   $customer = get_recent_student();
   if(isset($customer)){
        $c_name = $customer['name'];
        $city = $customer['city'];
        $reg_no = $customer['reg_no'];
        $designation = $customer['designation'];
        $department = $customer['department'];
        $institute = $customer['institute'];
        $topic_of_study = $customer['topic_of_study'];
        $phone = $customer['phone'];
        $email = $customer['email'];
   }
   else{
       $c_name = "";
        $city = "";
        $reg_no = "";
        $designation = "Student";
        $department = "";
        $institute = "NTU";
        $topic_of_study = "";
        $phone = "";
        $email = "";
   }
}
elseif(isset($_GET['prev_customer'])){
    $customer =get_student_by_id($_GET['prev_customer']);
    if(isset($customer)){
        $c_name = $customer['name'];
        $city = $customer['city'];
        $reg_no = $customer['reg_no'];
        $designation = $customer['designation'];
        $department = $customer['department'];
        $institute = $customer['institute'];
        $topic_of_study = $customer['topic_of_study'];
        $phone = $customer['phone'];
        $email = $customer['email'];
    }
    else{
        $c_name = "";
        $city = "";
        $reg_no = "";
        $designation = "Student";
        $department = "";
        $institute = "NTU";
        $topic_of_study = "";
        $phone = "";
        $email = "";
    }
}
else{
        $c_name = "";
        $city = "";
        $reg_no = "";
        $designation = "Student";
        $department = "";
        $institute = "NTU";
        $topic_of_study = "";
        $phone = "";
        $email = "";
}
use includes\FileUpload\UploadFile;
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
    $fields = array("cname","city","reg_no","designation","institute","department","phone","email","sample_color","expected_date");
    validate_presences($fields);
    $fields_with_max_lengths = array("cname" => 30,"city" => 30,"reg_no" => 40,"institute" => 40,"department" => 40,"phone" => 50,"email" =>50,"topic_of_study" => 100,"sample_color" => 200,"sample_style" => 200,"sample_weight" => 200);
    validate_max_lengths($fields_with_max_lengths);
// $fields_with_max_values = array("no_of_tests" => 11);
//  validate_max_lengths_for_integers($fields_with_max_values);
    //validating image upload
    $sample_image_name = "";
    $error_messages = array();
    if($_FILES['sample_image']['name'] != ""){
    $destination = __DIR__ . '/../../includes/samples-pics/';
    try {
        $upload = new UploadFile($destination);
        $upload->setMaxSize($max_file_size);
        $upload->upload();
        $error_messages = $upload->getMessages();
        $sample_image_name = $upload->getFileName();
    } catch (Exception $e) {
        $errors['file'] = $e->getMessage();
    }
}
  if (empty($errors)&&empty($error_messages)) {
    // Perform Create
        $sample_id=get_sample_id();
        $customer_id=get_customer_id();
        $c_name = mysql_prep($_POST["cname"]);
        $city = mysql_prep($_POST["city"]);
        $reg_no = mysql_prep($_POST["reg_no"]);
        $designation = mysql_prep($_POST["designation"]);
        $institute = mysql_prep($_POST["institute"]);
        $department = mysql_prep($_POST["department"]);
        $phone = mysql_prep($_POST["phone"]);
        $email = mysql_prep($_POST["email"]);
        $topic_of_study = "";
        if(isset($_POST["topic_of_study"])) {
            $topic_of_study = mysql_prep($_POST["topic_of_study"]);
        }
        $order_type = mysql_prep($_POST["order_type"]); 
        $sample_category = mysql_prep($_POST["sample_category"]);
        $sample_type = mysql_prep($_POST["sample_type"]);
        $sample_color = mysql_prep($_POST["sample_color"]);
        
        $sample_style = "";
        if(isset($_POST["sample_style"])) {
            $sample_style = mysql_prep($_POST["sample_style"]);
        }
        $sample_weight = "";
        if(isset($_POST["sample_weight"])) {
            $sample_weight = mysql_prep($_POST["sample_weight"]);
        }
        
        $sample_test_description = mysql_prep($_POST["content"]);
        $lab = mysql_prep($_POST["lab"]);
        $tests=$_POST['tests'];

        if ($tests)
        {
            foreach ($tests as $test)
            {
                $all_tests[]=$test;
            }
        }
      $no_of_tests = count($all_tests);
      $payment = "0.00";
      $payment_received = "0.00";
      $payment_pending = "0.00";
      $expected_date = date("Y-m-d H:i:s", strtotime($_POST['expected_date']));
      $expected_date = mysql_prep($expected_date);
      $status = 'submitted';
//      start mysqli transaction for data_insertion. follow link http://www.phpknowhow.com/mysql/transactions/ for help
      mysqli_autocommit($connection, false);
      $flag = true;
      // add commercial customer in database
     $result = add_academic_customer( $customer_id,$c_name,$city,$designation,$department,$institute,$reg_no,$topic_of_study,$phone,$email);

      if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['first_query']= mysqli_error($connection);
      }
      
       $result = insert_sample($sample_id,$sample_category,$sample_type,$sample_color,$sample_style,$sample_weight,$sample_test_description,$no_of_tests,$sample_image_name,$lab,$all_tests);
      if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['second_query']= mysqli_error($connection);
      }
       $result = place_order($customer_id,$sample_id,$expected_date,$lab,$order_type,$payment,$payment_received,$payment_pending,$status);
      if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['third_query']= mysqli_error($connection);
      }
      
  if ($flag) {
          mysqli_commit($connection);
          if(isset($_POST["send_email"])){
          // sent email to customer
          require_once("./operations/send_email_to_customer.php");
          if(isset($is_email_sent)&&$is_email_sent!=0){
              $_SESSION["message"] = "Sample submitted successfully and email sent.";
              redirect_to("view_customer_sample_record.php?customer_id={$customer_id}&success=1");
          }
          else{
              $_SESSION["message"] = "Sample submitted successfully but email sending failed.";
              redirect_to("view_customer_sample_record.php?customer_id={$customer_id}&success=1");
          }
        }
        else{
             $_SESSION["message"] = "Sample submitted successfully.";
             redirect_to("view_customer_sample_record.php?customer_id={$customer_id}&success=1");
        }
    }
    else {
        mysqli_rollback($connection);
        $_SESSION["message"] = "Sample Submission failed.";
        $query_success = false;
    }
  }  // if(empty($errors)

} // if(isset($_POST['submit']))

else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))
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
    <title>Add Academic Sample</title>
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
    <link href="../assets/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap-wizard/css/bwizard.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/parsley/src/parsley.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap-eonasdan-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/jquery-tag-it/css/jquery.tagit.css" rel="stylesheet"/>
    <link href="../assets/plugins/summernote-latest/summernote.css" rel="stylesheet">
    <link href="../assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet"/>
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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-database"></i>
                        <span>Add Sample</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="add_commercial_sample.php">Add Commercial Sample</a></li>
                        <li class="active"><a href="add_academic_sample.php">Add Academic Sample</a></li>
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
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">Add Sample</a></li>
            <li class="active">Add Academic Sample</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Academic Testing Request Form
            <small>Add Academic sample record</small>
        </h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-12 -->
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse">
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
                        <h4 class="panel-title">Add Academic Sample Record</h4>
                </div>
                 <!-- start notification  -->
                    <?php 
                //  display form errors if any
                    echo form_errors($errors);
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                    <!-- end notification --> 
                <div class="panel-body">
                <form action="add_academic_sample.php" method="POST" name="form-wizard" enctype="multipart/form-data">
                    <div id="wizard">
                        <ol>
                            <li>
                                Customer Detail
                                <small>Add personal information related to customer</small>
                            </li>
                            <li>
                                Sample Information
                                <small>Add information and attributes of sample
                                </small>
                            </li>
                            <li>
                                Tests Description
                                <small>Add tests description related to sample</small>
                            </li>
                            <li>
                                Completed
                                <small>Submit sample record to store in the database</small>
                            </li>
                        </ol>
                        <!-- begin wizard step-1 -->
                        <div class="wizard-step-1">
                            <fieldset>
                                <legend class="pull-left width-full">Student Detail</legend>
                                <!-- begin row -->
                                <div class="row">
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                    <div class="form-group block1">
                                        <label for="cname">Name *</label>
                                        <input type="text" id="cname" name="cname" value="<?php echo $c_name;?>" placeholder="Ali"
                                        class="form-control" data-parsley-group="wizard-step-1" required/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                        <input type="text" id="city" name="city" value="<?php echo $city;?>" placeholder="Faisalabad"
                                        class="form-control" data-parsley-group="wizard-step-1" required/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                    <div class="form-group block1">
                                        <label for="reg_no">Reg. No. *</label>
                                        <input type="text" id="reg_no" name="reg_no" value="<?php echo $reg_no;?>" placeholder="16-NTU-1014" class="form-control" data-parsley-group="wizard-step-1" required/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                
                                </div>
                                <!-- end row -->
                                <!-- begin row -->
                                <div class="row">
                                
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                <label for="designation">Designation *</label>
                                 <select id="designation" name="designation"
                                 class="form-control" data-parsley-group="wizard-step-1" required>
                                    <option value="" <?php if($designation==="") echo "selected";?>>Choose...</option>
                                    <option value="Student" <?php if($designation==="Student") echo "selected";?>>Student
                                    </option>
                                    <option value="Lecturar" <?php if($designation==="Lecturar") echo "selected";?>>Lecturar
                                    </option>
                                    <option value="Professor" <?php if($designation==="Professor") echo "selected";?>>Professor
                                    </option>
                                    <option value="Q.S" <?php if($designation==="Q.S") echo "selected";?>>QS
                                    </option>
                                    <option value="P.M" <?php if($designation==="P.M") echo "selected";?>>Project Manager
                                    </option>
                                    <option value="Employee" <?php if($designation==="Employee") echo "selected";?>>Employee
                                    </option>
                                    <option value="Manager" <?php if($designation==="Manager") echo "selected";?>>Manager
                                    </option>
                                    
                                </select>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label for="institute">Institute *</label>
                                        <input type="text" id="institute" name="institute" value="<?php echo $institute;?>" placeholder="NTU"
                                        class="form-control" data-parsley-group="wizard-step-1" required/>
                                </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                <label for="department">Department *</label>
                                 <select id="department" name="department"
                                 class="form-control" data-parsley-group="wizard-step-1" required>
                                    <option value="" <?php if($department==="") echo "selected";?>>Choose...</option>
                                    <option value="Yarn Manufacturing" <?php if($department==="Yarn Manufacturing") echo "selected";?>>Yarn Manufacturing
                                    </option>
                                    <option value="Weaving" <?php if($department==="Weaving") echo "selected";?>>Weaving
                                    </option>
                                    <option value="Knitting" <?php if($department==="Knitting") echo "selected";?>>Knitting
                                    </option>
                                    <option value="Textile Processing" <?php if($department==="Textile Processing") echo "selected";?>>Textile Processing
                                    </option>
                                    <option value="Garment Manufacturing" <?php if($department==="Garment Manufacturing") echo "selected";?>>Garment Manufacturing
                                    </option>
                                    <option value="Polymer Engineering" <?php if($department==="Polymer Engineering") echo "selected";?>>Polymer Engineering
                                    </option>
                                    <option value="Materials and Testing" <?php if($department==="Materials and Testing") echo "selected";?>>Materials and Testing
                                    </option>
                                    <option value="Applied Sciences" <?php if($department==="Applied Sciences") echo "selected";?>>Applied Sciences
                                    </option>
                                    <option value="Computer Science" <?php if($department==="Computer Science") echo "selected";?>>Computer Science
                                    </option>
                                    <option value="Management Sciences" <?php if($department==="Management Sciences") echo "selected";?>>Management Sciences
                                    </option>
                                    <option value="Design" <?php if($department==="Design") echo "selected";?>>Design
                                    </option>
                                    <option value="Humanities and Social Sciences" <?php if($department==="Humanities and Social Sciences") echo "selected";?>>Humanities and Social Sciences
                                    </option>
                                    <option value="ORIC" <?php if($department==="ORIC") echo "selected";?>>ORIC
                                    </option>
                                    <option value="Other" <?php if($department==="Other") echo "selected";?>>Other
                                    </option>
                                    
                                </select>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                
                                </div>
                                <!-- end row -->
                                 <!-- begin row -->
                                <div class="row">
                                
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                <label for="phone">Phone *</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo $phone;?>" placeholder="0300-1234678" class="form-control" data-parsley-group="wizard-step-1" required/>
                                </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                    <div class="form-group block1">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" name="email" value="<?php echo $email;?>" placeholder="ntrc@ntu.edu.com" class="form-control" data-parsley-group="wizard-step-1" required/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                    <div class="form-group block1">
                                        <label for="topic_of_study">Topic of Study</label>
                                        <input type="text" id="topic_of_study" name="topic_of_study" value="" placeholder="Composite Material Behaviour Analysis etc." class="form-control" data-parsley-group="wizard-step-1"/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                
                                </div>
                                <!-- end row -->
                                <!--begin row-->
                                <div class="row">
                                  <!-- begin col-10 -->
                                   <div class="col-md-10">

                                    <div class="checkbox checkbox-info checkbox-circle">
                                        <input type="checkbox" id="send_email" name="send_email" checked>
                                        <label for="send_email">
                                            Send an email to customer
                                        </label>
                                    </div>
                                   </div>
                                   <!-- end col-10 -->
                                   <!-- begin col-2 -->
                                   <div class="col-md-2">
                                       <div class="row">
                                           <div class="col-md-12">
                                               <div class="pull-right">
                                               <small><a href="add_academic_sample.php?prev_record=1">Insert previous record</a></small>
                                               </div>
                                            </div>
                                       </div>
                                   </div>
                                   <!-- end col-2 -->
                                </div>
                                <!--end row -->
                            </fieldset>
                        </div>
                        <!-- end wizard step-1 -->
                        <!-- begin wizard step-2 -->
                        <div class="wizard-step-2">
                            <fieldset>
                                <legend class="pull-left width-full">Sample Information</legend>
                            <!-- begin row -->
                            <div class="row">
                            <!-- begin col-4 -->
                            <div class="col-md-4">
                                <div class="form-group block1">
                                    <label for="order_type">Order type *</label>
                                     <select id="order_type" name="order_type" class="form-control" data-parsley-group="wizard-step-2" required>
                                    <option value="">Choose...</option>
                                    <option value="academic" selected>Academic
                                    </option>
                                    </select>
                                </div>
                            </div>
                            <!-- end col-4 -->
                            <!-- begin col-4 -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sample_category">Sample Category *</label>
                                     <select id="sample_category" name="sample_category" class="form-control" data-parsley-group="wizard-step-2" required>
                                    <option selected value="">Choose...</option>
                                    <option value="Physical">Physical</option>
                                    <option value="Chemical">Chemical</option>
                                    <option value="Product dev">Product development</option>
                                    <option value="Analytical">Analytical
                                    </option>
                                    </select>
                                </div>
                            </div>
                            <!-- end col-4 -->
                            <!-- begin col-4 -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sample_type">Sample Type *</label>
                                     <select id="sample_type" name="sample_type" class="form-control" data-parsley-group="wizard-step-2" required>
                                    <option selected value="">Choose...</option>
                                    <option value="Fabric" selected>Fabric
                                    </option>
                                    <option value="Fiber">Fiber
                                    </option>
                                    <option value="Film">Film
                                    </option>
                                    <option value="Liquid">Liquid
                                    </option>
                                    <option value="Powder">Powder
                                    </option>
                                    <option value="Non-wooven">Non-wooven
                                    </option>
                                    <option value="Nano-Fibres">Nano-Fibers
                                    </option>
                                    <option value="Yarn">Yarn
                                    </option>
                                    <option value="Coating">Coating
                                    </option>
                                    <option value="Comfort">Comfort</option>
                                    <option value="Garments">Garments
                                    </option>
                                    <option value="Protective Textile">Protective Textile</option>
                                    <option value="Hazardous Material">Hazardous Material</option>
                                    <option value="Miscelleneous" >Miscelleneous</option>
                                    </select>
                                </div>
                            </div>
                            <!-- end col-4 -->
                            </div>
                            <!-- end row -->
                            <!-- begin row -->
                                <div class="row">
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                    <div class="form-group block1">
                                        <label for="sample_color">Sample Color *</label>
                                        <input type="text" id="sample_color" name="sample_color" placeholder="Yellow" class="form-control" data-parsley-group="wizard-step-2" required/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sample_style">Sample Style</label>
                                        <input type="text" id="sample_style" name="sample_style" placeholder="shape or style etc"
                                        class="form-control" data-parsley-group="wizard-step-2"/>
                                    </div>
                                </div>
                                <!-- end col-4 -->
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group">
                                <label for="sample_weight">Sample Weight</label>
                                    <input type="text" id="sample_weight" name="sample_weight" placeholder="10g" class="form-control" data-parsley-group="wizard-step-2"/>
                                </div>
                                </div>
                                <!-- end col-4 -->
                                </div>
                                <!-- end row -->
                                <!-- begin row -->
                                <div class="row">
                                <!-- begin col-6 -->
                                <div class="col-md-6">
                                    <div class="form-group block1">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>">
                                        <label for="sample_image">Sample Image</label>
                                        <input type="file" id="sample_image" name="sample_image" class="form-control" data-parsley-group="wizard-step-2"/>
                                    </div>
                                </div>
                                <!-- end col-6 -->
                                
                                </div>
                                <!-- end row -->
                            </fieldset>
                        </div>
                        <!-- end wizard step-2 -->
                        <!-- begin wizard step-3 -->
                        <div class="wizard-step-3">
                            <fieldset>
                                <legend class="pull-left width-full">Tests Description</legend>
                                <!-- begin row -->
                                <div class="row">
                                    <!-- begin col-4 -->
                                    <div class="col-md-4">
                                        <div class="form-group block1">
                                         <label for="lab">Lab *</label>
                                         <select id="lab" name="lab" class="form-control" data-parsley-group="wizard-step-3" required>
                                         <option value="" selected>Select Lab</option>
                                          <option value="reception" >Reception</option>
                                          <option value="mechanical_lab" >Mechanical Lab</option>
                                          <option value="spectroscopy_lab" >Spectroscopy Lab</option>
                                          <option value="comfort_lab" >Comfort Lab</option>
                                          <option value="anti_microbial_lab" >Antimicrobial Testing Lab</option>
                                          <option value="applied_chemistry_lab" >Applied Chemistry Lab</option>
                                          <option value="chemistry_lab" >Chemistry Lab</option>
                                          <option value="coating_lab" >Coating Lab</option>
                                          <option value="composite_characterization_lab" >Composite Characterization Lab</option>
                                          <option value="composite_manufacturing_lab" >Composite Manufacturing Lab</option>
                                          <option value="eco_textiles_lab" >Eco Textiles Lab</option>
                                          <option value="garments_dept_lab" >Garments Department Lab</option>
                                          <option value="garments_manufacturing_lab" >Garments Manufacturing Lab</option>
                                          <option value="knitting_lab" >Knitting Lab</option>
                                          <option value="materials_and_testing_lab" >Materials and Testing Lab</option>
                                          <option value="nano_materials1_lab" >Nano materials Lab 1</option>
                                          <option value="nano_materials2_lab" >Nano materials Lab 2</option>
                                          <option value="non_wooven_lab" >Non Wooven Lab</option>
                                          <option value="organic_chemistry_lab" >Organic Chemistry Lab</option>
                                          <option value="physical_chemistry_lab" >Physical Chemistry Lab</option>
                                          <option value="plasma_coating_lab" >Plasma Coating Lab</option>
                                          <option value="polymer_dept_lab" >Polymer Department Lab</option>
                                          <option value="sem_lab" >Scanning Electron Microscopy Lab</option>
                                          <option value="spinning_lab" >Spinning Lab</option>
                                          <option value="tpcl_lab" >TPCL Lab</option>
                                          <option value="weaving_lab" >Weaving Lab</option>
                                          <option value="wet_processing_lab" >Wet Processing Lab</option>
                                          <option value="xray_diffraction_lab" >X-Ray Diffraction Lab</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- end col-4 -->
                                    <!-- begin col-8 -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="tests">Tests *</label>
                                            <select id="tests" class="multiple-select2 form-control" style="width: 100%" name="tests[]" multiple="multiple" data-parsley-group="wizard-step-3" required>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- end col-8 -->
                                </div>
                                <!-- end row -->
                                <!-- begin row -->
                                <div class="row">
                                    <!-- begin col-12 -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Sample Tests Description</label>
                                            <textarea class="textarea form-control" id="summernote" name="content" placeholder="Enter content ..." rows="12"></textarea>
                                        </div>
                                    </div>
                                    <!-- end col-12 -->
                                </div>
                                <!-- end row -->
                                <!-- begin row -->
                                <div class="row">
                               
                                <!-- begin col-4 -->
                                <div class="col-md-4">
                                <div class="form-group block1">
                                <label for="expected_date">Expected DateTime *</label>
                                <div class="input-group date" id="datetimepicker1">
                                        <input type="text" id="expected_date" name="expected_date" class="form-control" placeholder="08/15/2018 3:30 PM" data-parsley-group="wizard-step-3" required/>
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                                </div>
                                </div>
                                <!-- end col-4 -->
                                </div>
                                <!-- end row -->
                            </fieldset>
                        </div>
                        <!-- end wizard step-3 -->
                        <!-- begin wizard step-4 -->
                        <div>
                            <div class="jumbotron m-b-0 text-center">
                               
                                <button class="btn btn-info" name="submit" type="submit">Submit Sample Record</button>
                                
                            </div>
                        </div>
                        <!-- end wizard step-4 -->
                    </div>
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
<script src="../assets/plugins/jquery/jquery-1.9.1.min.js"></script>
<script src="../assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="../assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="../assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="../assets/js/reception_dashboard/reception_pages.js"></script>
<script src="../assets/plugins/parsley/dist/parsley.js"></script>
<script src="../assets/plugins/bootstrap-wizard/js/bwizard.js"></script>
<script src="../assets/js/sample_insertion_form_wizard.js"></script>
<script src="../assets/plugins/bootstrap-daterangepicker/moment.js"></script>
<script src="../assets/plugins/bootstrap-eonasdan-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="../assets/plugins/jquery-tag-it/js/tag-it.min.js"></script>
<script src="../assets/plugins/summernote-latest/summernote.js"></script>
<script src="../assets/plugins/select2/dist/js/select2.min.js"></script>
<!--custom javascript file-->
<script src="../assets/js/academic_sample_insertion_demo.js"></script>
<script src="../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        SampleInsertionFormWizard.init();
        AcademicSampleInsertion.init();
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