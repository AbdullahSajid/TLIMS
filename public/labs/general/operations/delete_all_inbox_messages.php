<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php
      $lab = access_lab_manager_from_subfolders();
      $beautify_lab = beautify_fieldname($lab);
      $table = $lab . '_' . 'receiving_box';
      $result= delete_all_inbox_messages($table);
      if ($result && mysqli_affected_rows($connection) >= 1) {
            $_SESSION["message"] = "Inbox is now empty";
            redirect_to("../message_inbox.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Deleting Inbox Messages.";
          redirect_to("../message_inbox.php?success=0");
        }

?>