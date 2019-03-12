<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php 
    if(!logged_in()){
        redirect_to("index.php");
    }
    // set status 1 to 0 to show user as offline
    $status = 0;
    $privileges = isset($_SESSION["privileges"]) ? $_SESSION["privileges"] : $_COOKIE["privileges"]; 
    updateOnlineStatus($privileges,$status);

	$_SESSION["user_id"] = null;
	$_SESSION["username"] = null;
    $_SESSION["privileges"] = null;
    setcookie("user_id","",time()-60*60*24);
    setcookie("username","",time()-60*60*24);
    setcookie("privileges","",time()-60*60*24);

    session_destroy();

	redirect_to("index.php?success=". urlencode("Signed Out Successfully!"));
?>