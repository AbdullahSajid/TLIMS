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
        // get delayed samples
        $status = 'pending';
        list($delayed_samples,$delayed_samples_count) = get_delayed_samples_and_count_by_status($status);
        // get todays customers
        $todays_customers_count = get_todays_customers_count();
        // get today lab wise samples
        $today_labwise_samples = get_todays_labwise_samples();
        $today_samples_count = mysqli_num_rows($today_labwise_samples);
        if($today_samples_count==0){
          $todays_lab_samples = "<p class=\"text-center\">No Lab Samples Exist</p>";
        }
        else{
            $total_sales = 0;
            $todays_lab_samples = "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th>Lab</th><th style=\"text-align:center;\">Sample Orders</th><th style=\"text-align:center;\">Sales (Rs.)</th></tr></thead><tbody>";
            
            foreach($today_labwise_samples as $samples){ 
                $todays_lab_samples .= "<tr><td><a style=\"color:black;\" href=\"lab_pending_samples.php?lab={$samples['lab']}\">" . beautify_fieldname($samples['lab']) . "</a></td>";
                $todays_lab_samples .= "<td align=\"center\"><span class=\"badge badge-warning\">{$samples['count']}</span></td>";
                $sales=number_format($samples['sum_payment']);               //   Outputs -> 1,500 of 1500.00
                $todays_lab_samples .= "<td align=\"center\"><span class=\"label label-info\">Rs. {$sales}</span></td></tr>";
                $total_sales += $samples['sum_payment'];
            }
            $total_sales = number_format($total_sales); 
            // last row will show total combineed sales of all labs samples
            $todays_lab_samples .= "<tr><td colspan=\"2\" align=\"center\"><b>Total Sales</b></td><td align=\"center\"><span class=\"label label-inverse\">Rs. {$total_sales}</span></td></tr>";
            $todays_lab_samples .= "</tbody></table></div>";
        }
    
        // get yesterday lab wise samples
        $yesterday_labwise_samples = get_yesterday_labwise_samples();
        $yesterday_samples_count = mysqli_num_rows($yesterday_labwise_samples);
        if($yesterday_samples_count==0){
          $yesterdays_lab_samples = "<p class=\"text-center\">No Lab Samples Exist</p>";
        }
        else{
            $total_sales = 0;
            $yesterdays_lab_samples = "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th>Lab</th><th style=\"text-align:center;\">Sample Orders</th><th style=\"text-align:center;\">Sales (Rs.)</th></tr></thead><tbody>";
            
            foreach($yesterday_labwise_samples as $samples){ 
                $yesterdays_lab_samples .= "<tr><td><a style=\"color:black;\" href=\"lab_pending_samples.php?lab={$samples['lab']}\">" . beautify_fieldname($samples['lab']) . "</a></td>";
                $yesterdays_lab_samples .= "<td align=\"center\"><span class=\"badge badge-warning\">{$samples['count']}</span></td>";
                $sales=number_format($samples['sum_payment']);               //   Outputs -> 1,500 of 1500.00
                $yesterdays_lab_samples .= "<td align=\"center\"><span class=\"label label-info\">Rs. {$sales}</span></td></tr>";
                $total_sales += $samples['sum_payment'];
            }
            $total_sales = number_format($total_sales); 
            // last row will show total combineed sales of all labs samples
            $yesterdays_lab_samples .= "<tr><td colspan=\"2\" align=\"center\"><b>Total Sales</b></td><td align=\"center\"><span class=\"label label-inverse\">Rs. {$total_sales}</span></td></tr>";
            $yesterdays_lab_samples .= "<tr><td colspan=\"2\" align=\"center\"><b>Total Sample Orders</b></td><td align=\"center\"><span class=\"label label-inverse\">{$yesterday_samples_count}</span></td></tr>";
            $yesterdays_lab_samples .= "</tbody></table></div>";
        }
        $status = 1;   /*includes all online users that may or may not be on system*/
        $visible = 1;  // means admin row should not be return beacuse it's 0
        $get_online_users = get_online_users($status,$visible);
        $online_users_count = mysqli_num_rows($get_online_users);
        if($online_users_count==0){
          $online_users = "<p class=\"text-center\">No Online User Available</p>";
        }
        /*recent login time property in database will also update on updating user credentials i.e. display picture, username updation and on login/logout. if a user deletes browser history being online in this sytem, then there is no way to change it's status to offline, so we are checkin whether the recent login time is 24 hours back, if it is then it means we should not considering that user as online */
        else{
            $online_users_count = 0;
            $online_users = "";
            foreach($get_online_users as $user){ 
                $recent_login_time = strtotime($user['recent_login_time']);
                $current_time = time();
                if($current_time - $recent_login_time < 86400){
                    $online_users_count += 1;
                    $online_users .= "<li><a href=\"javascript:;\">";
                    $online_users .= "<img src=\"../assets/img/users_pics/{$user['privileges']}/{$user['display_picture']}\" alt=\"user profile pic\"/></a>";
                    $online_users .= "<h4 class=\"username text-ellipsis\">";
                    $online_users .= beautify_fieldname($user['name']);
                    $online_users .= " <img src=\"../assets/img/online.png\" width=\"12px\" alt=\"online\"/>";
                    $online_users .= "<small>" . beautify_fieldname($user['privileges']) . "</small>";
                    $online_users .= "</h4></li>";
                }
            }
            if($online_users_count==0){
                $online_users = "<p class=\"text-center\">No Online User Available</p>";
            }
        }
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
        
        // close connection
        if (isset($connection)) {
	       mysqli_close($connection);
	     }
        // data to be return
        $data = array('pending_samples_count' => $pending_samples_count,       'pending_reports_count' => $pending_reports_count, 'delayed_samples_count' => $delayed_samples_count,
        'todays_customers_count' => $todays_customers_count,
        'todays_lab_samples' => $todays_lab_samples,
        'yesterdays_lab_samples' => $yesterdays_lab_samples,
        'online_users_count' => $online_users_count,
        'online_users' => $online_users,
        'unseen_messages_count' => $unseen_messages_count,
        'recent_messages_list' => $recent_messages_list);
        echo json_encode($data);
}
?>