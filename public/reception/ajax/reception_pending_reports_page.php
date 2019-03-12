<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
access_receptionist_from_subfolders();
if(isset($_POST['view'])){
         // if dropdown clicked then change status of unseen messages
         if($_POST["view"] == 'yes'){
            $table = 'reception_receiving_box';
            update_unseen_messages_status($table);
        }
        // get pending samples
        $status = "pending";
        $pending_samples_count = get_samples_count_by_status($status);
        // get pending reports
        $status = "finalized";
        $pending_reports_count = get_samples_count_by_status($status);
        
        // get unseen messages count
         $table = 'reception_receiving_box';
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
                $pic_path = get_user_picture_path($messages['sender']);
                $recent_messages_list .= "<li class=\"media\"><a href=\"view_inbox_message_detail.php?id={$messages['id']}\">";
                $recent_messages_list .= "<div class=\"media-left\"><img src=\"{$pic_path}\" class=\"media-object\" alt=\"profile pic\"/></div>";
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
        // pending reports
        $status = 'finalized';
        $samples = get_samples_by_status($status);
        
        $counter = 0;
        $pending_reports = "";
        if($pending_reports_count==0){
            $pending_reports .= "<p class=\"text-center\">No Pending Reports</p>";
        }
        else{
            while($sample = mysqli_fetch_assoc($samples)){
                $counter += 1;
                // confirm modal
                $pending_reports .="<div class=\"modal fade\" id=\"modal_confirmation_{$counter}\"><div class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button><h4 class=\"modal-title\">Confirm Report</h4></div><div class=\"modal-body\"><div class=\"alert alert-warning m-b-0\"><h4>Do you want to confirm this sample test report?</h4><p>Warning!! Please make sure that all the pending payment has been received from customer. If you confirm it, then there will be no pending payment from customer</p></div></div><div class=\"modal-footer\"><a href=\"javascript:;\" class=\"btn btn-sm btn-white\" data-dismiss=\"modal\">Close</a><a href=\"operations/confirm_sample_report.php?customer_id={$sample["customer_id"]}\" class=\"btn btn-sm btn-info\">Confirm</a></div></div></div></div>";
                
                // tables
                $pending_reports .= "<div class=\"table-responsive\">";
                $pending_reports .= "<table class=\"table table-condensed\">";
                $pending_reports .= "<tbody>";
                $pending_reports .= "<tr class=\"info\"><th>Customer ID</th><td>{$sample['customer_id']}</td><th>Sample ID</th><td>{$sample['sample_id']}</td></tr>";
                
                $customer = get_customer_by_type($sample['customer_id'],$sample['type']);
                $order_type = ucwords($sample['type']);
                $pending_reports .= "<tr class=\"info\"><th>Customer Name</th><td>{$customer['name']}</td><th>Order Type</th><td>{$order_type}</td></tr>";
                
                date_default_timezone_set('Asia/Karachi');
                $arrival_time = date('d-m-Y h:i:s A',strtotime($sample['timestamp']));
                $expected_time = date('d-m-Y h:i:s A',strtotime($sample['expected_date']));
                $label = get_deadline_alert_label_from_now($sample['expected_date']);
                $pending_reports .= "<tr class=\"info\"><th>Arrival Time</th><td>{$arrival_time}</td><th>Expected Time{$label}</th><td>{$expected_time}</td></tr>";
                
                $lab = beautify_fieldname($sample['lab']);
                $completion_time = date('d-m-Y h:i:s A',strtotime($sample['completion_date']));
                $label=get_short_time_diff_from_now($sample['completion_date']);
                $pending_reports .= "<tr class=\"info\"><th>Lab</th><td>{$lab}</td><th>Completion Time{$label}</th><td>{$completion_time}</td></tr>";
                
                $pending_reports .= "<tr class=\"info\"><th>Total Payment (Rs.)</th><td>{$sample['payment']}</td><th>Pending Payment (Rs.)</th><td>{$sample['payment_pending']}</td></tr>";
                
                $pending_reports .= "<tr class=\"active\"><th colspan=\"3\">Tests</th><th colspan=\"1\">Status</th></tr>";
                
                $lab = $sample['lab'];
                $lab_sample=get_lab_sample($sample['sample_id'],$lab);                                  
                $test_names=find_test_names_of_sample_by_lab_sample($lab_sample,$lab);
                foreach($test_names as $test){
                     $test_table = uglify_fieldname($test) . '_' . 'test';
                    $sample_test=get_test($sample['sample_id'],$test_table);
                     if($sample_test['status']=='submitted'){
                         $test_status = "Pending";
                     }
                     elseif($sample_test['status']=='finished'){
                         $test_status = "Completed";
                     }
                     else{
                         $test_status = "";
                     }
                     $pending_reports .= "<tr><td colspan=\"3\">{$test}</td><td colspan=\"1\">{$test_status}</td></tr>";
                }
                $pending_reports .= "</tbody></table>";
                $pending_reports .= "<div class=\"text-center\"><a href=\"view_customer_sample_record.php?customer_id={$sample['customer_id']}\" class=\"btn btn-info btn-xs\">View Customer</a> <a href=\"view_sample_report.php?customer_id={$sample['customer_id']}&lab={$lab}\" class=\"btn btn-primary btn-xs\">View Report</a> <a href=\"print_sample_report.php?customer_id={$sample['customer_id']}&lab={$lab}\" target=\"_blank\" class=\"btn btn-success btn-xs\">Print Report</a> <a href=\"#modal_confirmation_{$counter}\" data-toggle=\"modal\" class=\"btn btn-inverse btn-xs\">Confirm Report</a></div>";
                $pending_reports .= "</div><hr/>";
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
        'pending_reports' => $pending_reports);
        echo json_encode($data);
}
?>