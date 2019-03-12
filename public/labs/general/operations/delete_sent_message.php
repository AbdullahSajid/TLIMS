<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php require_once("../../../../includes/validation_functions.php"); ?>
<?php
$lab = access_lab_manager_from_subfolders();
$beautify_lab = beautify_fieldname($lab);
if(isset($_GET['id'])){
// instead of sending in trash,  deleting completely to save storage
      $table = $lab . '_' . 'sending_box';
      $id = $_GET['id'];
      $result= delete_sent_message($id,$table);
      if ($result && mysqli_affected_rows($connection) == 1) {
            $_SESSION["message"] = "Sent Message Deleted Successfully";
            redirect_to("../message_outbox.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Deleting Sent Message.";
          redirect_to("../message_outbox.php?success=0");
        }
}
else{
     redirect_to("../message_outbox.php");
}
?>