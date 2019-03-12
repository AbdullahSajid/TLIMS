<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php 
$lab = access_lab_manager();
$beautify_lab = beautify_fieldname($lab);
$user = get_user_info($lab);
    if (isset($_GET['success'])) {
        if($_GET['success']==1){
            $query_success=TRUE;
        }
        else{
            $query_success=FALSE;
        }
    }
    if (isset($_GET['pageno'])) {
            $pageno = $_GET['pageno'];
        } else {
            $pageno = 1;
        }
        $no_of_records_per_page = 10;
        $offset = ($pageno-1) * $no_of_records_per_page;

        $table = $lab . '_' . 'receiving_box';
//   we are using trash feature just for receiving box in the whole project
        $total_rows = count_inbox_messages($table);
        $total_pages = ceil($total_rows / $no_of_records_per_page);

        $inbox_messages = get_inbox_messages($table,$offset,
                                             $no_of_records_per_page);
    
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
    <title>Inbox Messages</title>
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
                        <li><a href="send_message.php">Compose</a></li>
                        <li class="active"><a href="message_inbox.php">Inbox</a></li>
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
                        <li class="active"><a href="message_inbox.php"><i class="fa fa-inbox fa-fw m-r-5"></i> Inbox <span class="badge pull-right unseen_inbox_messages_count"></span></a></li>
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
                      <div class="email-btn-row hidden-xs">
                       
                        <a id="deleteDrafts" href="operations/delete_all_inbox_messages.php" class="btn btn-sm btn-danger <?php if($total_rows==0) echo "disabled"; ?>" data-toggle="tooltip" title="Send all inbox messages to trash"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete All</a>
                        
                    </div>
                    </div>
                    <!-- end btn-toolbar -->
                </div>
                <!-- end wrapper -->
                <!-- start notification  -->
                    <?php 
//                 redirecting from 'delete_all_trash_messages.php'
//                 display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
                <!-- end notification --> 
                <!-- begin list-messages -->
               
                <ul class="list-group list-group-lg no-radius list-email">
                    <?php  
                        while($message = mysqli_fetch_assoc($inbox_messages)){
                            $time_string = get_time_diff_from_now($message['timestamp']);
                            $sender= $message['sender'];
                            $receiver= $message['receiver'];
                           
                            ?>
                            
                    <li class="list-group-item <?php if($sender=='reception'){echo 'primary';} else{echo 'inverse';} ?>">
                        
                        <a href="view_inbox_message_detail.php?id=<?php echo $message['id']; ?>" class="email-user">
                           <?php 
                            $from = 'lab'; 
                            $path = get_user_picture_path($sender,$from);
                            ?>
                            <img src="<?php echo $path; ?>" alt="sender picture"/>
                        </a>
                        <div class="email-info">
                            
                             <a href="operations/delete_inbox_message.php?id=<?php echo $message['id']; ?>" class="email-time m-r-2" data-toggle="tooltip" title="Send inbox message to trash"><i class="fa fa-trash fa-lg"></i></a>
                             
                            <a href="send_message.php?reply=<?php echo $sender; ?>" class="email-time m-r-2" data-toggle="tooltip" title="Reply to sender"><i class="fa fa-reply fa-lg"></i></a>
                             
                             <a href="view_inbox_message_detail.php?id=<?php echo $message['id']; ?>" class="email-time" data-toggle="tooltip" title="Click to view the inbox message"><i class="fa fa-eye fa-lg"></i></a> 
                             
                            <h5 class="email-title">
                                <a href="view_inbox_message_detail.php?id=<?php echo $message['id']; ?>"><?php echo ucfirst($message['subject']); ?></a>
                                <span class="label label-<?php if($sender=='reception'){echo 'primary';} else{echo 'inverse';} ?> f-s-10"><?php echo beautify_fieldname($sender);?></span>
                            </h5>
                            <p class="email-desc">
                               <?php
                                /*$content = strip_tags($message['content']);
                                if(strlen($content)<80){
                                    echo ucfirst($content);
                                }
                                elseif(strlen($content)<130){
                                    echo ucfirst(substr($content,0,80)) . '...';
                                }
                                else{
                                    echo "Click the message to see detailed content";
                                }*/
                            
                               ?>
                               <span><?php echo $time_string;?></span>
                            </p>
                        </div>
                    </li>
                   <?php } ?>
                </ul>
                 
                <!-- end list-messages -->
                <!-- begin wrapper -->
                 <div class="email-footer clearfix">
                            <?php echo $total_rows; 
                             echo ($total_rows==1)?" message": " messages";
                            ?>
                            <ul class="pagination pagination-sm m-t-0 m-b-0 pull-right">
                                <li><a href="?pageno=1"><i class="fa fa-angle-double-left"></i></a>
                                </li>
                                <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>"><a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><i class="fa fa-angle-left"></i></a></li>
                                <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>"><a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>"><i class="fa fa-angle-right"></i></a></li>
                                <li class="<?php if($total_pages == 0){ echo 'disabled'; } ?>"><a href="<?php if($total_pages == 0){ echo '#'; } else { echo "?pageno=" . $total_pages;} ?>"><i class="fa fa-angle-double-right"></i></a></li>
                            </ul>
                        </div>
                <!-- end wrapper -->
            </div>
            <!-- end vertical-box-column -->
        </div>
        <!-- end vertical-box -->
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
<script src="../../assets/js/lab_dashboard/lab_inbox_page.js"></script>
<script src="../../assets/js/message_draft_demo.js"></script>
<script src="../../assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
        PageAjax.init();
        Draft.init();
    });
</script>
</body>
</html>
<?php
	if (isset($connection)) {
	  mysqli_close($connection);
	}
?>