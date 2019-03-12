<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php 
access_receptionist_from_subfolders();
if(isset($_GET['customer_id'])){
   
   $get_customer_id = $_GET['customer_id'];
   
   $customer =get_customer_order_by_id($get_customer_id);
   if(!$customer){
       // now checking for record in academic_students table
       $customer =get_student_order_by_id($get_customer_id);
       if(!$customer) {
           $_SESSION["message"] = "Customer ID isn't valid.";
           redirect_to("../view_customer_sample_record.php");
       }
   }
   // now gather the name of tests of this sample in string
   if(isset($customer)){
     if($customer['status']=='submitted'){
         $test_names = find_test_names_of_sample( $customer['sample_id'],$customer['lab']);
         
         mysqli_autocommit($connection, false);
         $flag = true;
         foreach($test_names as $test_name){
          $result=insert_lab_tests_by_test_names($customer['sample_id'],$test_name);
             if (!($result && mysqli_affected_rows($connection) == 1)) {
                  $flag = false;
                  $errors[]= mysqli_error($connection);
             }
         }
        $customer_id = $customer['customer_id'];
         if ($flag) {
             $status = 'pending';
             update_orders_status($customer['sample_id'],$status);
             if ($result && mysqli_affected_rows($connection) == 1) {
                  mysqli_commit($connection);
                  $_SESSION["message"] = "Sample Record Confirmed Successfully";
                   redirect_to("../view_customer_sample_record.php?customer_id={$customer_id}&success=1");
             }
             else{
               mysqli_rollback($connection);
               $_SESSION["message"] = "Sample Record Confirmation Failed";
               redirect_to("../view_customer_sample_record.php?customer_id=$customer_id"); 
            }
             
          }
         else{
           mysqli_rollback($connection);
           $customer_id = $customer['customer_id'];
           $_SESSION["message"] = "Sample Record Confirmation Failed";
           redirect_to("../view_customer_sample_record.php?customer_id=$customer_id"); 
         }
         
     }
       else{
           $_SESSION["message"] = "Sample Record is already confirmed and cannot be deleted now.";
           redirect_to("../view_customer_sample_record.php");
       }
   }
}
else{
    $_SESSION["message"] = "Customer ID doesn't exist.";
    redirect_to("../view_customer_sample_record.php");
}
?>