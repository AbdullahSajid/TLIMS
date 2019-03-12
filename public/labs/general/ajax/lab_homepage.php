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
        // get delayed samples
        $status = 'pending';
        list($delayed_samples,$delayed_samples_count) = get_delayed_samples_and_count_by_status($status,$lab);
        
        // last 7 days samples orders by lab
        $last_seven_days_stats = get_last_seven_days_samples_count_by_lab($lab);
        // get todays samples of lab
        $todays_customers_count = $last_seven_days_stats[0];
    
        $all_count = 0;
        foreach($last_seven_days_stats as $day_count){
            $all_count += $day_count;
        }
        $today = date('l');
        $yesterday = date('l', strtotime("-1 day"));
        $third_day = date('l', strtotime("-2 day"));
        $fourth_day = date('l', strtotime("-3 day"));
        $fifth_day = date('l', strtotime("-4 day"));
        $sixth_day = date('l', strtotime("-5 day"));
        $seventh_day = date('l', strtotime("-6 day"));
        
        $last_seven_days = array($today,$yesterday,$third_day, $fourth_day,$fifth_day,$sixth_day,$seventh_day);
       
        if($all_count==0){
          $last_seven_days_samples_stats = "<p class=\"text-center\">No Lab Samples Exist</p>";
        }
        else{
            $last_seven_days_samples_stats = "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th>Week Day</th><th style=\"text-align:center;\">Sample Orders</th></tr></thead><tbody>";
            $day_index = 0;
            foreach($last_seven_days_stats as $stats){ 
                $last_seven_days_samples_stats .= "<tr><td>{$last_seven_days[$day_index]}</td>";
                if($stats==0)
                    $label = 'default';
                else
                    $label = 'inverse';
                $last_seven_days_samples_stats .= "<td align=\"center\"><span class=\"badge badge-{$label}\">{$stats}</span></td></tr>";
                $day_index += 1;
            } 
            // last row will show total combineed sum of all sample orders
            $last_seven_days_samples_stats .= "<tr><td><b>Total Sample Orders</b></td><td align=\"center\"><span class=\"badge badge-primary\">{$all_count}</span></td></tr>";
            $last_seven_days_samples_stats .= "</tbody></table></div>";
        }
    
        $status = 1;   /*includes all online users that may or may not be on system*/
        $get_online_users = get_online_users($status);
        $online_users_count = mysqli_num_rows($get_online_users);
        if($online_users_count==0){
          $online_users = "<p class=\"text-center\">No Online User Available</p>";
        }
        /*recent login time property in database will also update on updating user credentials i.e. display picture, username updation and on login/logout. if a user deletes browser history being online in this sytem, then there is no way to change it's status to offline, so we are checkin whether the recent login time is 24 hours back, if it is then it means we should not considering that user as online */
        else{
            $online_users_count = 0;
            $online_users = "";
            foreach($get_online_users as $user){
                if($user['privileges']=='admin' || $user['privileges']=='reception'){
                    $recent_login_time = strtotime($user['recent_login_time']);
                    $current_time = time();
                    if($current_time - $recent_login_time < 86400){
                        $online_users_count += 1;
                        $online_users .= "<li><a href=\"javascript:;\">";
                        $online_users .= "<img src=\"../../assets/img/users_pics/{$user['privileges']}/{$user['display_picture']}\" alt=\"user profile pic\"/></a>";
                        $online_users .= "<h4 class=\"username\">";
                        $online_users .= beautify_fieldname($user['name']);
                        $online_users .= " <img src=\"../../assets/img/online.png\" width=\"12px\" alt=\"online\"/>";
                        $online_users .= "<small>" . beautify_fieldname($user['privileges']) . "</small>";
                        $online_users .= "</h4></li>";
                    }
                }
            }
            if($online_users_count==0){
                $online_users = "<p class=\"text-center\">No Online User Available</p>";
            }
        }
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
        
        // close connection
        if (isset($connection)) {
	       mysqli_close($connection);
	     }
        // data to be return
        $data = array('pending_samples_count' => $pending_samples_count,       'delayed_samples_count' => $delayed_samples_count,
        'todays_customers_count' => $todays_customers_count,
        'last_seven_days_samples_stats' => $last_seven_days_samples_stats,
        'online_users_count' => $online_users_count,
        'online_users' => $online_users,
        'unseen_messages_count' => $unseen_messages_count,
        'recent_messages_list' => $recent_messages_list);
        echo json_encode($data);
}
?>