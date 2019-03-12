<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php
access_admin_from_subfolders();
if(isset($_GET['id'])){
    
      $table = 'admin_receiving_box';
      $id = $_GET['id'];
      $result= delete_trash_message($id,$table);
      if ($result && mysqli_affected_rows($connection) == 1) {
            $_SESSION["message"] = "Trash Message Deleted Successfully";
            redirect_to("../message_trash.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Deleting Trash Message.";
          redirect_to("../message_trash.php?success=0");
        }
}
else{
     redirect_to("../message_trash.php");
}
?>