<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php require_once("../../../includes/config.php"); ?>
<?php 
access_receptionist_from_subfolders();
use Twilio\Rest\Client;
if(isset($_GET['customer_id'])){
   
   $get_customer_id = $_GET['customer_id'];
   
   $customer_order =find_order_by_customer_id($get_customer_id);
   if(!$customer_order){
       
           $_SESSION["message"] = "Customer ID isn't valid.";
           redirect_to("../pending_reports.php");
       
   }
   // now gather the name of tests of this sample in string
   if(isset($customer_order)){
     if($customer_order['status']=='finalized'){
         $status = 'finished';
         $finished_date = date('Y-m-d H:i:s');
         $pending_payment = 0.00;
         $payment_received = $customer_order['payment'];
        $result=update_orders_status_and_finished_date($customer_order['sample_id'],$status,$finished_date,$pending_payment,$payment_received);
         if($result&&mysqli_affected_rows($connection) == 1){
             
           $_SESSION["message"] = "Sample Test Report Confirmed Successfully";
           redirect_to("../pending_reports.php?success=1");  
        }
         else{
             $_SESSION["message"] = "Sample Test Report Confirmation Failed";
             redirect_to("../pending_reports.php");
         }
         
     }
     elseif($customer_order['status']=='finished'){
           $_SESSION["message"] = "Sample Test Report is already confirmed";
           redirect_to("../completed_reports.php");
             
     }
    elseif($customer_order['status']=='submitted'){
           $customer_id = $customer_order['customer_id'];
           $_SESSION["message"] = "Sample Test is not submitted to lab yet";
           redirect_to("../view_customer_sample_record.php?customer_id={$customer_id}");    
     }
    elseif($customer_order['status']=='pending'){
           $customer_id = $customer_order['customer_id'];
           $_SESSION["message"] = "Sample Test Report isn't generated yet";
           redirect_to("../pending_reports.php");  
     }
    else{
        
    }
               
   }
}
else{
    $_SESSION["message"] = "Customer ID doesn't exist.";
    redirect_to("../pending_reports.php");
}
?>