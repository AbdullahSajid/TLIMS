<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php require_once("../../../../includes/validation_functions.php"); ?>
<?php
 $lab = access_lab_manager_from_subfolders();
 $beautify_lab = beautify_fieldname($lab);
 $user = get_user_info($lab);
if(isset($_GET['id'])){
   
// instead of sending in trash,  deleting completely to save storage
      $table = $lab . '_' . 'sending_box';
      $id = $_GET['id'];
      $result= get_draft_message($id,$table);
    
      if ($draft_message = mysqli_fetch_assoc($result)) {
          
            $id = $draft_message["id"];
            $subject = $draft_message["subject"];
            $content = $draft_message["content"];
            $receivers = $draft_message['receiver'];
 
            $all_receivers = explode('@',$receivers);
          
      
          /*  start mysqli transaction for data_insertion. follow link  http://www.phpknowhow.com/mysql/transactions/ for help */
          mysqli_autocommit($connection, false);
          $flag = true;

    //     initializing sending attributes
           $sender = $lab;
           $is_draft = 0;
           $is_trash = 0;

      for($count=0;$count<count($all_receivers);$count++){
          $receiver = $all_receivers[$count];
         
         
          if($receiver=='reception')
             $table = 'reception_receiving_box';
          elseif($receiver=='admin')
             $table = 'admin_receiving_box';
          else
              die("Recepient does not exist");
          // sending to receipients
          $result= send_message($sender,$receiver,$subject,$content,
                                $table,$is_draft,$is_trash);
          if (!($result && mysqli_affected_rows($connection) == 1)) {
              $flag = false;
              $error[$count]=mysqli_error($connection);
          }
    
      } // end for loop
        // update is_draft from 1 to 0
          $table = $lab . '_' . 'sending_box';
          $new_count = count($all_receivers);
          $result = update_draft_status($id,$table);
          if (!($result && mysqli_affected_rows($connection) == 1)) {
              $flag = false;
              $error[$new_count]=mysqli_error($connection);
          }
          if($flag){
            mysqli_commit($connection);
            $_SESSION["message"] = "Draft Successfully Sent To Recepients.";
            redirect_to("../message_draft.php?success=1");  
          }
          else{
              mysqli_rollback($connection);
              $_SESSION["message"] = "Message Sending failed.";
          }
      }
        else {
          $_SESSION["message"] = "Error Draft Not Found.";
          redirect_to("../message_draft.php?success=0");
        }
}
else{
     redirect_to("../message_draft.php");
}
?>