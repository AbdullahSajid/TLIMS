<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php require_once("../../../../includes/validation_functions.php"); ?>
<?php
$lab = access_lab_manager_from_subfolders();
$beautify_lab = beautify_fieldname($lab);
$user = get_user_info($lab);
//this page is used by send_message.php
if (isset($_POST)) {
  // Process the form
    
  $fields_with_max_lengths = array("subject"=>255);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    // Perform Create
    
    $subject = mysql_prep($_POST["subject"]);
    $content = mysql_prep($_POST["content"]);
    $receivers = $_POST['receivers'];

     if ($receivers){
        foreach ($receivers as $receiver)
        {
            $all_receivers[] = $receiver;
        }
    }
      
          $join_receivers = implode("@",$all_receivers);
          $sender = $lab;
          $is_draft = 1;
          $is_trash = 0;
          $table = $lab . '_' . 'sending_box';
          $result= send_message($sender,$join_receivers,$subject,$content,
                                $table,$is_draft,$is_trash);
      if ($result && mysqli_affected_rows($connection) == 1) {
          echo "success"; 
         
      }
        else {
            $error[]=mysqli_error($connection);
            echo "failed";
//          $_SESSION["message"] = "Error Saving Draft.";
//          redirect_to("send_message.php");
        }
  
  }
} else {
  // This is probably a GET request
   echo "Access Denied";
  
} // end: if (isset($_POST['submit']))

?>