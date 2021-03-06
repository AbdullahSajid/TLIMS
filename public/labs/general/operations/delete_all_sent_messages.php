<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php require_once("../../../../includes/validation_functions.php"); ?>
<?php
      $lab = access_lab_manager_from_subfolders();
      $beautify_lab = beautify_fieldname($lab);
      $user = get_user_info($lab);
// instead of sending in trash,  deleting completely to save storage
      $table = $lab . '_' . 'sending_box';
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