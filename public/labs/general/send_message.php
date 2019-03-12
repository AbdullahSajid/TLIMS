<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php 
$lab = access_lab_manager();
$beautify_lab = beautify_fieldname($lab);
$user = get_user_info($lab);
// redirecting from message_inbox for reply with reply attribute
if(isset($_GET['reply'])){
    $reply_to = $_GET['reply'];
}
if (isset($_POST['send'])) {
  // Process the form
    
  $fields_with_max_lengths = array("subject"=>255);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    // Perform Create
    
    $subject = mysql_prep($_POST["subject"]);
    $content = mysql_prep($_POST["content"]);
    $receivers = $_POST['receivers'];

     if ($receivers){
        foreach ($receivers as $receiver)
        {
            $all_receivers[] = $receiver;
        }
    }
      
      /*  start mysqli transaction for data_insertion. follow link  http://www.phpknowhow.com/mysql/transactions/ for help */
      mysqli_autocommit($connection, false);
      $flag = true;
      
//      initializing sending attributes
       $sender = $lab;
       $is_draft = 0;
       $is_trash = 0;
      
      for($count=0;$count<count($all_receivers);$count++){
          $receiver = $all_receivers[$count];
         
         $table = $receiver . '_' . 'receiving_box';
          
          // sending to receipients
          $result= send_message($sender,$receiver,$subject,$content,
                                $table,$is_draft,$is_trash);
          if (!($result && mysqli_affected_rows($connection) == 1)) {
              $flag = false;
              $error[$count]=mysqli_error($connection);
          }
    
      } // end for loop
      $table = $lab . '_' . 'sending_box';
      $new_count = count($all_receivers);
      $join_receivers = implode("@",$all_receivers);
      if ($flag) {
          // saving sender message
          $result= send_message($sender,$join_receivers,$subject,$content,
                                $table,$is_draft,$is_trash);
          if (!($result && mysqli_affected_rows($connection) == 1)) {
              $flag = false;
              $error[$new_count]=mysqli_error($connection);
          }
          
          if($flag){
              mysqli_commit($connection);
              $query_success = TRUE;
              $_SESSION["message"] = "Message Sent successfully.";
          }
          else{
              mysqli_rollback($connection);
              $_SESSION["message"] = "Message Sending failed.";
          }
      }
        else {
          mysqli_rollback($connection);
          $_SESSION["message"] = "Message Sending failed.";
        }
  
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8"/>
    <title>Compose Message</title>
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
    <link href="../../assets/plugins/jquery-tag-it/css/jquery.tagit.css" rel="stylesheet"/>
    <link href="../../assets/plugins/summernote-latest/summernote.css" rel="stylesheet">
    <link href="../../assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet"/>

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
                <li class="has-sub active">
                    <a href="javascript:;">
                        <span class="caret pull-right"></span>
                        <i class="fa fa-inbox"></i>
                        <span class="messages_indicator">Messages</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="active"><a href="send_message.php">Compose</a></li>
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
    <div id="content" class="content content-full-width">
        <!-- begin vertical-box -->
        <div class="vertical-box">
            <!-- begin vertical-box-column -->
            <div class="vertical-box-column width-250">
                <!-- begin wrapper -->
                <div class="wrapper bg-silver text-center">
                    <a href="send_message.php" class="btn btn-success p-l-40 p-r-40 btn-sm">
                        Compose
                    </a>
                </div>
                <!-- end wrapper -->
                <!-- begin wrapper -->
                <div class="wrapper">
                    <p><b>FOLDERS</b></p>
                    <ul class="nav nav-pills nav-stacked nav-sm">
                        <li><a href="message_inbox.php"><i class="fa fa-inbox fa-fw m-r-5"></i> Inbox <span class="badge pull-right unseen_inbox_messages_count"></span></a></li>
                        <li><a href="message_outbox.php"><i class="fa fa-send fa-fw m-r-5"></i> Sent</a></li>
                        <li><a href="message_draft.php"><i class="fa fa-pencil fa-fw m-r-5"></i> Drafts</a></li>
                        <li><a href="message_trash.php"><i class="fa fa-trash fa-fw m-r-5"></i> Trash</a></li>
                       
                    </ul>
                </div>
                <!-- end wrapper -->
            </div>
            <!-- end vertical-box-column -->
            <!-- begin vertical-box-column -->
            <div class="vertical-box-column">
                <!-- begin wrapper -->
                <div class="wrapper bg-silver-lighter">
                    <!-- begin btn-toolbar -->
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <a id="editLink" href="#" class="btn btn-white btn-sm p-l-20 p-r-20" data-toggle="tooltip" title="Click to edit the message content"><i
                                    class="fa fa-edit"></i></a>
                            <a id="saveDraft" href="#" class="btn btn-white btn-sm p-l-20 p-r-20" data-toggle="tooltip" title="Click to save the message as draft"><i
                                    class="fa fa-save"></i></a>
                            <a id="resetLink" href="#" class="btn btn-white btn-sm p-l-20 p-r-20" data-toggle="tooltip" title="Click to erase the message content"><i class="fa fa-eraser"></i></a>
                        </div>
                    </div>
                    <!-- end btn-toolbar -->
                </div>
                <!-- end wrapper -->
                <!-- begin wrapper -->
                <div class="wrapper">    
                    <!-- start notification  -->
                    <?php 
//                 display form errors if any
                    echo form_errors($errors);
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                    <!-- end notification --> 
                    <div class="p-30 bg-white">
                        <!-- begin email form -->
                        <form id="message_form" action="send_message.php" method="POST" name="email_to_form" onsubmit="return postForm();">
                            <!-- begin email to -->
            
                                <label class="control-label">To:</label>
                                
                                   <div class="m-b-15">
                                    <select id="receivers" class="multiple-select2 form-control" style="width: 100%" name="receivers[]" multiple="multiple" required>
                                        <optgroup label="Labs">
                                         <?php if($lab!=='admin'){?>
                                          <option value="admin" >Admin</option>
                                          <?php } ?>
                                          <?php if($lab!=='reception'){?>
                                          <option value="reception" >Reception</option>
                                          <?php } ?>
                                          
                                        </optgroup>
                                       
                                    </select>
                            
                                </div>
                            <!-- end email to -->
                            <!-- begin email subject -->
                            <label class="control-label">Subject:</label>
                            <div class="m-b-15">
                                <input id="subject" name="subject" type="text" class="form-control" required/>
                            </div>
                            <!-- end email subject -->
                            <!-- begin email content -->
                            <label class="control-label">Content:</label>
                            <div class="m-b-15">
                                <textarea class="textarea form-control" id="summernote" name="content" placeholder="Enter content ..." rows="12"></textarea>
                            </div>
                            <!-- end email content -->
                            <button type="submit" name="send" class="btn btn-primary p-l-40 p-r-40">Send</button>
                        </form>
                        <!-- end email form -->
                    </div>
                </div>
                <!-- end wrapper -->
            </div>
            <!-- end vertical-box-column -->
        </div>
        <!-- end vertical-box -->
        <!-- begin hidden button for warning message -->
                 <a href="#" data-click="swal-warning" class="btn btn-warning hidden">Warning</a>
        <!-- end hidden button for warning message -->
        <!-- begin hidden button for success message -->
                 <a href="#" data-click="swal-success" class="btn btn-success hidden">Success for Message Sent</a>
                 <a href="#" data-click="swal-success-draft" class="btn btn-success hidden">Success for Saving Draft</a>
        <!-- end hidden button for success message -->
    </div>
    <!-- end #content -->
    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i
            class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
    
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
<script src="../../assets/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
<script src="../../assets/plugins/jquery-tag-it/js/tag-it.min.js"></script>
<script src="../../assets/plugins/summernote-latest/summernote.js"></script>
<script src="../../assets/plugins/select2/dist/js/select2.min.js"></script>
<!--custom javascript file-->
<script src="../../assets/js/message_compose_demo.js"></script>
<script src="../../assets/js/search_select_demo.js"></script>
<script src="../../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->
<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
        MessageCompose.init();
        FormPlugins.init();
    });
     $('[data-click="swal-warning"]').on("click", function () {
            swal({
                title             : "Content box should not be empty",
                text              : "",
                type              : "warning",
                showCancelButton  : !0,
                confirmButtonClass: "btn-warning",
                confirmButtonText : "Okay!"
            })
    });
    
     $('[data-click="swal-success-draft"]').on("click", function () {
            swal({
                title             : "Draft Saved Successfully",
                text              : "",
                type              : "success",
                showCancelButton  : !0,
                confirmButtonClass: "btn-success",
                confirmButtonText : "Okay!"
            })
    });
    $('[data-click="swal-success"]').on("click", function () {
            swal({
                title             : "Message Sent Successfully",
                text              : "",
                type              : "success",
                showCancelButton  : !0,
                confirmButtonClass: "btn-success",
                confirmButtonText : "Okay!"
            })
    });
    
    <?php if(isset($query_success)&&$query_success===TRUE){ ?>      
      $('[data-click="swal-success"]').click();
    <?php } ?>
    <?php if(isset($reply_to)){ ?>
       $("#receivers").val('<?php echo $reply_to; ?>'); // Select the option // with a value of receiver
       $("#receivers").trigger('change'); // Notify any JS components that the // value changed
 
    <?php } ?>
    
    var postForm = function() {
        
	if ($('#summernote').summernote('isEmpty')) {
       
       $('[data-click="swal-warning"]').click();
        return false;
        
    }
}   
</script>
</body>
</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>