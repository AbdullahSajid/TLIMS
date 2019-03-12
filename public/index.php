<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php
//Check whether admin already logged in or not
if(logged_in()){
    if((isset($_SESSION['privileges']) && $_SESSION["privileges"]==='admin') || $_COOKIE['privileges']==='admin'){
        redirect_to("admin/index.php");
    }
    elseif((isset($_SESSION['privileges']) && $_SESSION["privileges"]==='reception') || $_COOKIE['privileges']==='reception'){
        redirect_to("reception/index.php");
    }
    else{
       
        redirect_to("labs/general/index.php");
    }
    
}
$username = "";

if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("username", "password");
  validate_presences($required_fields);
  
  if (empty($errors)) {
    // Attempt Login

		$username = mysql_prep($_POST["username"]);
		$password = mysql_prep($_POST["password"]);
		
		$found_user = attempt_login($username, $password);

    if ($found_user) {
            // Success
            // set status 0 to 1 to show user as online
            $status = 1;
            updateOnlineStatus($found_user["privileges"],$status);
			// Mark user as logged in and giving relevant access
        	$_SESSION["user_id"] = $found_user["id"];
			$_SESSION["username"] = $found_user["username"];
            $_SESSION["privileges"] = $found_user["privileges"];
            // store cookies for one day
            if(isset($_POST["chk"])){
               setcookie("user_id",$found_user["id"],time()+60*60*24);
               setcookie("username",$found_user["username"],time()+60*60*24);
               setcookie("privileges",$found_user["privileges"],time()+60*60*24);
            }
            if($found_user["privileges"]==='admin'){
                redirect_to("admin/index.php");
            }
            elseif($found_user["privileges"]==='reception'){
                redirect_to("reception/index.php");
            }
           else{
                 redirect_to("labs/general/index.php");
           }
    } else {
      // Failure
      $_SESSION["message"] = "Username/password not found.";
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
    <meta charset="UTF-8"/>
    <title>NTRC Login</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta name="description" content="NTRC Labs Information Management System"/>
    <meta name="author" content="Abdullah Sajid, Phone# 03012745906"/>

       <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link href="assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet"/>
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <link href="assets/css/style.css" rel="stylesheet"/>
    <link href="assets/css/style-responsive.css" rel="stylesheet"/>
    <link href="assets/css/theme/default.css" rel="stylesheet" id="theme"/>
    <link href="assets/css/essential.css" rel="stylesheet"/>
    <!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="assets/plugins/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top bg-white">
<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->

<!-- begin #page-container -->
<div id="page-container" class="fade">
    <!-- begin login -->
    <div class="login login-with-news-feed">
        <!-- begin news-feed -->
        <div class="news-feed">
            <div class="news-image">
                <img src="assets/img/gallery/gallery-10.jpg" data-id="login-cover-image" alt=""/>
            </div>
            <div class="news-caption">
                 <h4 class="caption-title"><i class="fa fa-diamond text-success"></i> TLIMS</h4>
                <p>
                   Textile Labs Information Management System for National Textile Research Center, NTU
                </p>
            </div>
        </div>
        <!-- end news-feed -->
        <!-- begin right-content -->
        <div class="right-content">
         <!-- begin navigation -->
          <div class="login_navigation">
           <p class="text-center" style="font-size:13px;margin-top:5px;">
            <a style="margin-right:20px;color:grey;" href="home.html">Home</a>
            <a style="margin-right:20px;color:black;" href="index.php">Login</a> 
            <a style="margin-right:20px;color:grey;" href="faq.html">FAQ</a>
           </p>
          </div>
           <!-- end navigation -->
            <!-- begin login-header -->
            <div class="login-header">
                <div class="brand">
                    <img src="assets/img/ntrc-48x48" alt="logo"> TLIMS
                    <small>Textile Labs Information Management System</small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
            <!-- end login-header -->
            
            <!-- begin login-content -->
            <div class="login-content">
                <!-- start notification  -->
                    <?php 
//                  display query errors or query success if any
                    if(isset($query_success)&&$query_success===TRUE)
                        echo query_status(TRUE);
                    else
                        echo query_status(FALSE);
                    ?>
               <!-- end notification -->
                <form action="index.php" method="POST" class="margin-bottom-0">
                    <div class="form-group m-b-15">
                        <input type="text" class="form-control input-lg" name="username" value="<?php echo htmlentities($username); ?>" placeholder="Username" required/>
                    </div>
                    <div class="form-group m-b-15">
                        <input type="password" name="password" class="form-control input-lg" placeholder="Password" required/>
                    </div>
                    <div class="checkbox m-b-30">
                        <label for="checkbox">
                            <input type="checkbox" checked="checked" id="checkbox" name="chk"/> Remember Me
                        </label>
                    </div>
                    <div class="login-buttons">
                        <button type="submit" name="submit" class="btn btn-success btn-block btn-lg">Sign me in</button>
                    </div>
                    <hr/>
                    <p class="text-center">
                                &copy; <?php echo date("Y"); ?> NTRC TLIMS, All Rights Reserved.</p>

                    </p>
                </form>
            </div>
            <!-- end login-content -->
        </div>
        <!-- end right-container -->
    </div>
    <!-- end login -->

   
</div>
<!-- end page container -->

<!-- ================== BEGIN BASE JS ================== -->
<script src="assets/plugins/jquery/jquery-1.9.1.min.js"></script>
<script src="assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="assets/crossbrowserjs/html5shiv.js"></script>
<script src="assets/crossbrowserjs/respond.min.js"></script>
<script src="assets/crossbrowserjs/excanvas.min.js"></script>
<![endif]-->
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/jquery-cookie/jquery.cookie.js"></script>
<script src="assets/plugins/jquery-cookie/js.cookie.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="assets/js/app.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();
    });
</script>

</body>

</html>