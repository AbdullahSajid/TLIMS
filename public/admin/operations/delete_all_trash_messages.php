<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php
      access_admin_from_subfolders();
      $table = 'admin_receiving_box';
      $result= delete_all_trash_messages($table);
      if ($result && mysqli_affected_rows($connection) >= 1) {
            $_SESSION["message"] = "Trash is empty now";
            redirect_to("../message_trash.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Deleting Trash Messages.";
          redirect_to("../message_trash.php?success=0");
        }

?>