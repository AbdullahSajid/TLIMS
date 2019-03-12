<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php
      access_admin_from_subfolders();
// instead of sending in trash,  deleting completely to save storage
      $table = 'admin_sending_box';
      $result= delete_all_sent_messages($table);
      if ($result && mysqli_affected_rows($connection) >= 1) {
            $_SESSION["message"] = "All Sent Messages Deleted Successfully";
            redirect_to("../message_outbox.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Deleting Sent Messages.";
          redirect_to("../message_outbox.php?success=0");
        }

?>