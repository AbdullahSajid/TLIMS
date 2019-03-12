<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php
$lab = access_lab_manager_from_subfolders();
if(isset($_GET['id'])){
      $beautify_lab = beautify_fieldname($lab);
      $table = $lab . '_' . 'receiving_box';
      $id = $_GET['id'];
      $result= delete_inbox_message($id,$table);
      if ($result && mysqli_affected_rows($connection) == 1) {
            $_SESSION["message"] = "Inbox Message Sent To Trash Successfully";
            redirect_to("../message_inbox.php?success=1");  
      }
        else {
          $_SESSION["message"] = "Error Sending Inbox Message To Trash";
          redirect_to("../message_inbox.php?success=0");
        }
}
else{
     redirect_to("../message_inbox.php");
}
?>