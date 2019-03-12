<?php require_once("../../../../includes/session.php"); ?>
<?php require_once("../../../../includes/db_connection.php"); ?>
<?php require_once("../../../../includes/functions.php"); ?>
<?php
$lab = access_lab_manager_from_subfolders();
if(isset($_POST['view'])){
         // if dropdown clicked then change status of unseen messages
         if($_POST["view"] == 'yes'){
            $table = $lab . '_' . 'receiving_box';
            update_unseen_messages_status($table);
        }
        // get pending samples
        $status = "pending";
        $pending_samples_count = get_samples_count_by_lab_and_status($lab,$status);
        // get unseen messages count
         $table = $lab . '_' . 'receiving_box';
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
                $pic_path = get_user_picture_path($messages['sender'],$lab);
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
        // lab pending samples count
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
                date_default_timezone_set('Asia/Karachi');
                $arrival_time = date('d-m-Y h:i:s A',strtotime($sample['timestamp']));
                $expected_time = date('d-m-Y h:i:s A',strtotime($sample['expected_date']));
                $label = get_deadline_alert_label_from_now($sample['expected_date']);
                $lab_sample = get_lab_sample($sample['sample_id'],$lab);
                $sample_description = [];
                if($lab_sample['sample_color'])
                    $sample_description[]=ucwords($sample['sample_color']);
                   
                 if($lab_sample['sample_style'])
                     $sample_description[]=ucwords($sample['sample_style']);
                 
                 if($lab_sample['sample_weight'])
                     $sample_description[]=ucwords($sample['sample_weight']);
                 $sample_physical_description = implode(', ',$sample_description);
                
                $lab_pending_samples .= "<tr class=\"success\"><th>Sample ID</th><td>{$sample['sample_id']}</td><th>Sample Type</th><td>{$lab_sample['sample_type']}</td></tr>";
                
                $lab_pending_samples .= "<tr class=\"info\"><th>Physical Description</th><td>{$sample_physical_description}</td><th>Sample Category</th><td>{$lab_sample['sample_category']}</td></tr>";
                
                $lab_pending_samples .= "<tr class=\"warning\"><th>Arrival Time</th><td>{$arrival_time}</td><th>Expected Time{$label}</th><td>{$expected_time}</td></tr>";
                $lab_pending_samples .= "<tr class=\"active\"><th colspan=\"3\">Tests</th><th colspan=\"1\">Status</th></tr>";
                $test_names=find_test_names_of_sample_by_lab_sample($sample,$lab);
                 foreach($test_names as $test){
                     $test_table = uglify_fieldname($test) . '_' . 'test';
                     $sample_test=get_test($sample['sample_id'],$test_table);
                     if($sample_test['status']=='submitted'){
                         $button = "<a href=\"submit_test_result.php?sample_id={$sample['sample_id']}&test={$test_table}\" class=\"btn btn-primary btn-xs\">Submit</a>";
                     }
                     elseif($sample_test['status']=='finished'){
                         $button = "<a href=\"view_sample_test_result.php?sample_id={$sample['sample_id']}&test={$test_table}\" class=\"btn btn-white btn-xs\">View</a>";
                     }
                     else{
                          $button = "";
                     }
                     $lab_pending_samples .= "<tr><td style=\"font-size:1.1em;\" colspan=\"3\">{$test}</td><td colspan=\"1\">{$button}</td></tr>";
                 }
                $lab_pending_samples .= "</tbody></table>";
                $lab_pending_samples .= "</div><hr/>";
            }
        }
    
        // close connection
        if (isset($connection)) {
	       mysqli_close($connection);
	     }
        // data to be return
        $data = array('pending_samples_count' => $pending_samples_count,
        'unseen_messages_count' => $unseen_messages_count,
        'recent_messages_list' => $recent_messages_list,
        'lab_pending_samples' => $lab_pending_samples,
        'lab_pending_samples_count' => $lab_pending_samples_count);
        echo json_encode($data);
}
?>