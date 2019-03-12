<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
access_admin_from_subfolders();
if(isset($_POST['view'])){
         // if dropdown clicked then change status of unseen messages
         if($_POST["view"] == 'yes'){
            $table = 'admin_receiving_box';
            update_unseen_messages_status($table);
        }
        // get pending samples
        $status = "pending";
        $pending_samples_count = get_samples_count_by_status($status);
        // get pending reports
        $status = "finalized";
        $pending_reports_count = get_samples_count_by_status($status);
        
        // get unseen messages count
         $table = 'admin_receiving_box';
         $unseen_messages_count = get_unseen_messages_count($table);
        // get 3 recent messages
         $no_of_messages = 3;
         $recent_messages = get_recent_messages($no_of_messages,$table);
         $count_recent_messages = mysqli_num_rows($recent_messages);
         $recent_messages_list = "<li class=\"dropdown-header\">Notifications</li>";
        if($count_recent_messages==0){
          $recent_messages_list .= "<li class=\"dropdown-footer text-center\"><a href=\"message_inbox.php\">No Notifications Found</a></li>";
        }
        else{
            foreach($recent_messages as $messages){
                $user = get_user_info($messages['sender']);
                $recent_messages_list .= "<li class=\"media\"><a href=\"view_inbox_message_detail.php?id={$messages['id']}\">";
                $recent_messages_list .= "<div class=\"media-left\"><img src=\"../assets/img/users_pics/{$messages['sender']}/{$user['display_picture']}\" class=\"media-object\" alt=\"profile pic\"/></div>";
                $recent_messages_list .= "<div class=\"media-body\">";
                $recent_messages_list .= "<h6 class=\"media-heading\">";
                $recent_messages_list .= beautify_fieldname($messages['sender']);
                $recent_messages_list .= "</h6><p>";
                $recent_messages_list .= $messages['subject'];
                $time = get_time_diff_from_now($messages['timestamp']);
                $recent_messages_list .= "</p><div class=\"text-muted f-s-11\">{$time}";
                $recent_messages_list .= "</div></div></a></li>";
            }
            $recent_messages_list .= "<li class=\"dropdown-footer text-center\"><a href=\"message_inbox.php\">View more</a></li>";
        }
        // lab pending samples count
        $lab = $_POST['lab'];
        $status = 'pending';
        $lab_pending_samples_count=get_samples_count_by_lab_and_status($lab,$status);
        $samples = get_lab_samples_by_lab_and_status($lab,$status);
        
        // lab pending samples
        $lab_pending_samples = "";
        if($lab_pending_samples_count==0){
            $lab_pending_samples .= "<p class=\"text-center\">No Pending Samples</p>";
        }
        else{
            while($sample = mysqli_fetch_assoc($samples)){
                $lab_pending_samples .= "<div class=\"table-responsive\">";
                $lab_pending_samples .= "<table class=\"table table-condensed\">";
                $lab_pending_samples .= "<tbody>";
                $lab_pending_samples .= "<tr class=\"success\"><th>Customer ID</th><td>{$sample['customer_id']}</td><th>Sample ID</th><td>{$sample['sample_id']}</td></tr>";
                
                $customer = get_customer_by_type($sample['customer_id'],$sample['type']);
                $order_type = ucwords($sample['type']);
                $lab_pending_samples .= "<tr class=\"info\"><th>Customer Name</th><td>{$customer['name']}</td><th>Order Type</th><td>{$order_type}</td></tr>";
                
                date_default_timezone_set('Asia/Karachi');
                $arrival_time = date('d-m-Y h:i:s A',strtotime($sample['timestamp']));
                $expected_time = date('d-m-Y h:i:s A',strtotime($sample['expected_date']));
                $label = get_deadline_alert_label_from_now($sample['expected_date']);
                $lab_pending_samples .= "<tr class=\"warning\"><th>Arrival Time</th><td>{$arrival_time}</td><th>Expected Time{$label}</th><td>{$expected_time}</td></tr>";
                $lab_pending_samples .= "<tr class=\"active\"><th colspan=\"3\">Tests</th><th colspan=\"1\">Status</th></tr>";
                $test_names=find_test_names_of_sample_by_lab_sample($sample,$lab);
                 foreach($test_names as $test){
                     $test_table = uglify_fieldname($test) . '_' . 'test';
                     $sample_test=get_test($sample['sample_id'],$test_table);
                     if($sample_test['status']=='submitted'||$sample_test['status']=='pending'){
                         $test_status = "Pending";
                     }
                     else{
                         $test_status = "Completed";
                     }
                     $lab_pending_samples .= "<tr><td colspan=\"3\">{$test}</td><td colspan=\"1\">{$test_status}</td></tr>";
                 }
                $lab_pending_samples .= "</tbody></table>";
                $lab_pending_samples .= "<div class=\"text-center\"><a href=\"view_customer_sample_record.php?customer_id={$sample['customer_id']}\" class=\"btn btn-info btn-xs\">View Customer</a></div>";
                $lab_pending_samples .= "</div><hr/>";
            }
        }
    
        // close connection
        if (isset($connection)) {
	       mysqli_close($connection);
	     }
        // data to be return
        $data = array('pending_samples_count' => $pending_samples_count,       'pending_reports_count' => $pending_reports_count, 
        'unseen_messages_count' => $unseen_messages_count,
        'recent_messages_list' => $recent_messages_list,
        'lab_pending_samples' => $lab_pending_samples,
        'lab_pending_samples_count' => $lab_pending_samples_count);
        echo json_encode($data);
}
?>