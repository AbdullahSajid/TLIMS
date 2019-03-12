<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/validation_functions.php"); ?>
<?php
access_admin_from_subfolders();
if(isset($_GET['id'])){
// instead of sending in trash,  deleting completely to save storage
      $table = 'admin_sending_box';
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
           $sender = 'admin';
           $is_draft = 0;
           $is_trash = 0;

      for($count=0;$count<count($all_receivers);$count++){
          $receiver = $all_receivers[$count];
         
          if($receiver=='anti_microbial_lab')
              $table = 'anti_microbial_lab_receiving_box';
          elseif($receiver=='applied_chemistry_lab')
              $table = 'applied_chemistry_lab_receiving_box';
          elseif($receiver=='chemistry_lab')
              $table = 'chemistry_lab_receiving_box';
          elseif($receiver=='coating_lab')
             $table = 'coating_lab_receiving_box';
          elseif($receiver=='comfort_lab')
             $table = 'comfort_lab_receiving_box';
          elseif($receiver=='composite_characterization_lab')
             $table = 'composite_characterization_lab_receiving_box';
          elseif($receiver=='composite_manufacturing_lab')
             $table = 'composite_manufacturing_lab_receiving_box';
          elseif($receiver=='eco_textiles_lab')
             $table = 'eco_textiles_lab_receiving_box';
          elseif($receiver=='garments_dept_lab')
             $table = 'garments_dept_lab_receiving_box';
          elseif($receiver=='garments_manufacturing_lab')
             $table = 'garments_manufacturing_lab_receiving_box';
          elseif($receiver=='knitting_lab')
             $table = 'knitting_lab_receiving_box';
          elseif($receiver=='materials_and_testing_lab')
             $table = 'materials_and_testing_lab_receiving_box';
          elseif($receiver=='mechanical_lab')
             $table = 'mechanical_lab_receiving_box';
          elseif($receiver=='nano_materials1_lab')
             $table = 'nano_materials1_lab_receiving_box';
          elseif($receiver=='nano_materials2_lab')
             $table = 'nano_materials2_lab_receiving_box';
          elseif($receiver=='non_wooven_lab')
             $table = 'non_wooven_lab_receiving_box';
          elseif($receiver=='organic_chemistry_lab')
             $table = 'organic_chemistry_lab_receiving_box';
          elseif($receiver=='physical_chemistry_lab')
             $table = 'physical_chemistry_lab_receiving_box';
          elseif($receiver=='plasma_coating_lab')
             $table = 'plasma_coating_lab_receiving_box';
          elseif($receiver=='polymer_dept_lab')
             $table = 'polymer_dept_lab_receiving_box';
          elseif($receiver=='sem_lab')
             $table = 'sem_lab_receiving_box';
          elseif($receiver=='spectroscopy_lab')
             $table = 'spectroscopy_lab_receiving_box';
          elseif($receiver=='spinning_lab')
             $table = 'spinning_lab_receiving_box';
          elseif($receiver=='tpcl_lab')
             $table = 'tpcl_lab_receiving_box';
          elseif($receiver=='weaving_lab')
             $table = 'weaving_lab_receiving_box';
          elseif($receiver=='wet_processing_lab')
             $table = 'wet_processing_lab_receiving_box';
          elseif($receiver=='xray_diffraction_lab')
             $table = 'xray_diffraction_lab_receiving_box';
          elseif($receiver=='reception')
             $table = 'reception_receiving_box';
          elseif($receiver=='admin')
             $table = 'admin';
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
          $table = 'admin_sending_box';
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