<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
access_admin_from_subfolders();
  if(isset($_GET["id"])){
      $user = find_user_by_id($_GET["id"]);
      if (!$user) {
        // user ID was missing or invalid or 
        // user couldn't be found in database
           redirect_to("../manage_users.php");
        }
    }
    else{
    redirect_to("../manage_users.php");
    }
  
  $id = $user["id"];
  $query = "DELETE FROM ntrc_management WHERE id = {$id} AND visible=1 LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] = "User deleted Successfully.";
    redirect_to("../manage_users.php?success=1");
  } else {
    // Failure
    $_SESSION["message"] = "User deletion failed.";
    redirect_to("../manage_users.php?success=0");
  }
  
?>
