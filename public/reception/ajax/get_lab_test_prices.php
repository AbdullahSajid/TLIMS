<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
access_receptionist_from_subfolders();
//this page is used by send_message.php
if (isset($_POST)) {

    $nameOfTests = json_decode($_POST['nameOfTests']);
    
    $total_payment = 0.00;
    $error = false; // error refers to case where no resulting row is obtained // from the database
    for($i=0;$i<count($nameOfTests);$i++){
        
        $test_price = find_price_by_test_name($nameOfTests[$i]);
        if(!isset($test_price)){
            $error = true;
            break;
        }
        else{
           $total_payment += $test_price;
        }
        
    }
    
      if ($error===false) {
          echo $total_payment; 
         
      }
        else {
            echo "failed";

        }
  
  }
 else {
  // This is probably a GET request
   echo "Access Denied";
  
} // end: if (isset($_POST['submit']))

?>