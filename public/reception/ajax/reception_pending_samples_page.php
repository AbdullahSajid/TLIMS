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
        // pending samples table
        $alphabetical_order = 'ASC';
        $lab_names = get_lab_names($alphabetical_order);
        $pending_samples_tbody = "";
        while($lab_name = mysqli_fetch_assoc($lab_names)){
            $pending_samples_tbody .= "<tr>";
            for($i=0;$i<=1;$i++) { 
            if($i==1){
                if($lab_name = mysqli_fetch_assoc($lab_names)){
                  // complete this loop iteration
                }else{
                  // skip this loop iteration
                    continue;
                }
            }
            // end if
            $pending_samples_tbody .= "<td>"; 
            $pending_samples_tbody .= beautify_fieldname($lab_name['lab']);
            $pending_samples_tbody .= "</td>"; 
            $lab = $lab_name['lab']; 
            $status = 'pending';
            $sample_count=get_samples_count_by_lab_and_status($lab,$status);
             if($sample_count>=10){
                 $badge_color = 'danger';
             }
             elseif($sample_count>=5){
                  $badge_color = 'warning';
             }                                               
             elseif($sample_count>=1){
                  $badge_color = 'primary';
             }
             else{
                 $badge_color = 'default'; 
             }
             $pending_samples_tbody .= "<td><a href=\"lab_pending_samples.php?lab={$lab}\"><span class=\"badge badge-{$badge_color}\">{$sample_count}</span></a></td>";  
        } // end for
            $pending_samples_tbody .= "</tr>";
        } // end while
    
        // get delayed samples
        $status = 'pending';
        list($delayed_samples,$delayed_samples_count) = get_delayed_samples_and_count_by_status($status);
        
        // get unprocessed samples
        $status = 'submitted';
        $unprocessed_samples_count = get_samples_count_by_status($status);
        
        // close connection
        if (isset($connection)) {
	       mysqli_close($connection);
	     }
        // data to be return
        $data = array('pending_samples_count' => $pending_samples_count,       'pending_reports_count' => $pending_reports_count, 
        'unseen_messages_count' => $unseen_messages_count,
        'recent_messages_list' => $recent_messages_list,
        'pending_samples_tbody' => $pending_samples_tbody,
        'delayed_samples_count' => $delayed_samples_count,
        'unprocessed_samples_count' => $unprocessed_samples_count);
        echo json_encode($data);
}
?>