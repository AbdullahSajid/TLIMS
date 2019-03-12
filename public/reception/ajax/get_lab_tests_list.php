<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
access_receptionist_from_subfolders();
//this page is used by send_message.php
if (isset($_POST)) {

    $lab = ($_POST['lab']);
    $test_names = get_test_names_by_lab($lab);

    echo json_encode($test_names); 
  
  }
 else {
  // This is probably a GET request
   echo "Access Denied";
  
} // end: if (isset($_POST['submit']))

?>