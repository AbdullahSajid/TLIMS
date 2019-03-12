<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php 
access_admin_from_subfolders();
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
         if($customer['type']=='academic')
             $customers_table = 'academic_students';
         else
             $customers_table = 'commercial_customers';
         mysqli_autocommit($connection, false);
         $flag = true;
         $result = delete_customer_sample_order($customer['customer_id'],$customers_table);
         if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['first_query']= mysqli_error($connection);
        }
         $table = 'orders';
         $result = delete_customer_sample_order($customer['customer_id'],$table);
         if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['second_query']= mysqli_error($connection);
        }
         $sample_table = $customer['lab'] . '_' . 'samples';
         $result = delete_sample_test_record($customer['sample_id'],$sample_table);
         if (!($result && mysqli_affected_rows($connection) == 1)) {
          $flag = false;
          $errors['third_query']= mysqli_error($connection);
        }
         if ($flag) {
          mysqli_commit($connection);
//          $_SESSION["message"] = "Sample Record Deleted Successfully";
             redirect_to("../index.php");
          }
         else{
           $customer_id = $customer['customer_id'];
           $_SESSION["message"] = "Sample Record Deletion Failed";
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