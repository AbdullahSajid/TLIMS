<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php require_once("../../../../includes/validation_functions.php"); ?>
<?php
      $lab = access_lab_manager_from_subfolders();
      $beautify_lab = beautify_fieldname($lab);
      $user = get_user_info($lab);
       $table = $lab . '_' . 'receiving_box';
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