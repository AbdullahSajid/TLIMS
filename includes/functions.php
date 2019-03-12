<?php
    function redirect_to($new_location) {
	  header("Location: " . $new_location);
	  exit;
	}

	function mysql_prep($string) {
		global $connection;
        $string = trim($string);
		$escaped_string = mysqli_real_escape_string($connection, $string);
		return $escaped_string;
	}
	
	function confirm_query($result_set) {
		if (!$result_set) {
			die("Database query failed.");
		}
	}

    function beautify_fieldname($fieldname) {
      $fieldname = str_replace("_", " ", $fieldname);
      // make first letters of each word uppercase
      $fieldname = ucwords($fieldname);
      return $fieldname;
    }
    function uglify_fieldname($fieldname) {
      $fieldname = strtolower($fieldname);
      //  make all strring lowercase
      $fieldname = str_replace(" ", "_", $fieldname);
      return $fieldname;
    }
    function set_dashboard_lab_title($lab) {
      $lab_title_length = strlen($lab);
      $lab_title = "";
      if($lab_title_length>20){   
          $lab_title .= "<span style=\"font-size: 0.9em;\">";
          $lab_title .= $lab;
          $lab_title .= "</span>";
      }
      elseif($lab_title_length>18){
          $lab_title .= "<span style=\"font-size: 0.9em;\">";
          $lab_title .= $lab;
          $lab_title .= "</span>";
          $lab_title .= "<small>NTRC</small>"; 
      }
      else{
          $lab_title .= $lab;
          $lab_title .= "<small>NTRC</small>"; 
      }    
      return $lab_title;
    }
    
    function send_message($sender,$receiver,$subject,$content,$table,$is_draft,$is_trash) {
        global $connection;

        //    status attribute of every table will be by default 0
        $query  = "INSERT INTO {$table} (";
        $query .= " sender, receiver, subject, content, is_draft,is_trash";
        $query .= ") VALUES (";
        $query .= " '{$sender}','{$receiver}', '{$subject}', '{$content}', 
        {$is_draft}, {$is_trash}";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
    }

    function count_draft_messages($table) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM {$table} ";
		$query .= "WHERE is_draft = 1 AND is_trash = 0 ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_rows = mysqli_fetch_array($result)[0];
        return $total_rows;
		
    }
    function get_draft_messages($table,$offset,$no_of_records_per_page) {
            global $connection;
            $query  = "SELECT id,sender,receiver,subject,"; 
            $query .= "is_draft,is_trash,timestamp ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_draft = 1 AND is_trash = 0 Order by id DESC ";
            $query .= "LIMIT {$offset}, {$no_of_records_per_page}";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function get_draft_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);
            $query  = "SELECT * ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$id} AND is_draft = 1 ";
            $query .= "LIMIT 1";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function delete_all_draft_messages($table) {
            global $connection;

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_draft = 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function delete_draft_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$safe_id} AND is_draft = 1 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function update_draft_status($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);

            $query  = "UPDATE {$table} ";
            $query .= "SET is_draft = 0 ";
            $query .= "WHERE id={$safe_id} ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function count_sent_messages($table) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM {$table} ";
		$query .= "WHERE is_draft = 0 AND is_trash = 0 ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_rows = mysqli_fetch_array($result)[0];
        return $total_rows;
		
    }
    function get_sent_messages($table,$offset,$no_of_records_per_page) {
            global $connection;
            $query  = "SELECT id,sender,receiver,subject,"; 
            $query .= "is_draft,is_trash,timestamp ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_draft = 0 AND is_trash = 0 Order by id DESC ";
            $query .= "LIMIT {$offset}, {$no_of_records_per_page}";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function get_sent_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);
            $query  = "SELECT * ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$id} AND is_draft = 0 ";
            $query .= "LIMIT 1";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function delete_all_sent_messages($table) {
            global $connection;

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_draft = 0";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function delete_sent_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$safe_id} AND is_draft = 0 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function count_trash_messages($table) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM {$table} ";
		$query .= "WHERE is_trash = 1";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_rows = mysqli_fetch_array($result)[0];
        return $total_rows;
		
    }
    function get_trash_messages($table,$offset,$no_of_records_per_page) {
            global $connection;
            $query  = "SELECT id,receiver,sender,subject,"; 
            $query .= "status,is_draft,is_trash,timestamp ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_trash = 1 Order by id DESC ";
            $query .= "LIMIT {$offset}, {$no_of_records_per_page}";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function get_trash_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);
            $query  = "SELECT * ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$id} AND is_trash = 1 ";
            $query .= "LIMIT 1";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function delete_all_trash_messages($table) {
            global $connection;

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_trash = 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function delete_trash_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$safe_id} AND is_trash = 1 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function count_inbox_messages($table) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM {$table} ";
		$query .= "WHERE is_trash = 0";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_rows = mysqli_fetch_array($result)[0];
        return $total_rows;
		
    }
    function get_inbox_messages($table,$offset,$no_of_records_per_page) {
            global $connection;
            $query  = "SELECT id,receiver,sender,subject,"; 
            $query .= "status,is_draft,is_trash,timestamp ";
            $query .= "FROM {$table} ";
            $query .= "WHERE is_trash = 0 Order by id DESC ";
            $query .= "LIMIT {$offset}, {$no_of_records_per_page}";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }
    function delete_all_inbox_messages($table) {
            global $connection;

            $query  = "UPDATE {$table} ";
            $query .= "SET is_trash = 1 ";
            $query .= "WHERE is_trash = 0";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function delete_inbox_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);

            $query  = "UPDATE {$table} ";
            $query .= "SET is_trash = 1 ";
            $query .= "WHERE id={$safe_id} AND is_trash = 0 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function get_inbox_message($id,$table) {
            global $connection;
            $safe_id = mysqli_real_escape_string($connection, $id);
            $query  = "SELECT * ";
            $query .= "FROM {$table} ";
            $query .= "WHERE id={$id} AND is_trash = 0 ";
            $query .= "LIMIT 1";
            $result_set = mysqli_query($connection, $query);
            confirm_query($result_set);
            return $result_set;

    }

    function find_user_by_id($id) {
     	global $connection;
		
		$safe_user_id = mysqli_real_escape_string($connection, $id);
		
		$query  = "SELECT * ";
		$query .= "FROM ntrc_management ";
//        we are only examining visible users
		$query .= "WHERE id = {$safe_user_id} AND visible = 1 ";
		$query .= "LIMIT 1";
		$result_set = mysqli_query($connection, $query);
		confirm_query($result_set);
		if($user = mysqli_fetch_assoc($result_set)) {
			return $user;
		} else {
			return null;
		}
    }

    function find_all_ntrc_users() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM ntrc_management ";
        $query .= "WHERE visible=1 ";
		$query .= "ORDER BY privileges ASC";
		$users_set = mysqli_query($connection, $query);
		confirm_query($users_set);
		return $users_set;
	}
    function find_all_tests_prices() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM test_charges ";
		$query .= "ORDER BY lab ASC";
		$tests_set = mysqli_query($connection, $query);
		confirm_query($tests_set);
		return $tests_set;
	}
    function find_tests_by_lab($lab) {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM test_charges ";
		$query .= "WHERE lab='{$lab}'";
		$tests_set = mysqli_query($connection, $query);
		confirm_query($tests_set);
		return $tests_set;
	}
    function find_test_by_id($id) {
     	global $connection;
		
		$safe_test_id = mysqli_real_escape_string($connection, $id);
		
		$query  = "SELECT * ";
		$query .= "FROM test_charges ";
		$query .= "WHERE id = {$safe_test_id} ";
		$query .= "LIMIT 1";
		$result = mysqli_query($connection, $query);
		confirm_query($result);
		if($test = mysqli_fetch_assoc($result)) {
			return $test;
		} else {
			return null;
		}
    }
    function add_new_test($nature_of_test,$lab,$sample_type,$test_standards,$particulars_of_test,$price) {
     	global $connection;
        
        mysqli_autocommit($connection, false);
        $flag = true;
        
        $query  = "INSERT INTO test_charges (";
        $query .= " nature_of_test, lab, sample_type, test_method, particulars_of_test, price ";
        $query .= ") VALUES (";
        $query .= " '{$nature_of_test}','{$lab}', '{$sample_type}', '{$test_standards}', '{$particulars_of_test}', {$price}";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        
        if (!($result && mysqli_affected_rows($connection) == 1)) {
              $flag = false;
              $errors[]= mysqli_error($connection);
        }
        $lab_table = $lab . '_' . 'samples';
        $test_table = $nature_of_test . '_' . 'test';
        // if lab table does not exist then create
        $query = "CREATE TABLE IF NOT EXISTS {$lab_table} (
               id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
               sample_id varchar(20) NOT NULL UNIQUE KEY,
               timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
               sample_category enum('Physical','Chemical','Product dev','Analytical') NOT NULL,
               sample_type enum('Fabric','Yarn','Fiber','Garments','Film','Liquid','Powder','Non-wooven','Nano-Fibres','Coating','Comfort','Protective Textile','Hazardous Material','Miscelleneous') NOT NULL,
               sample_color varchar(250) DEFAULT NULL,
               sample_style varchar(250) DEFAULT NULL,
               sample_weight varchar(250) DEFAULT NULL,
               sample_test_detail text,
               no_of_tests tinyint(4) NOT NULL,
               sample_image varchar(100) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        /*adding test attribute after attribute 'no_of_test' to standardize table structure*/
        $query = "ALTER TABLE {$lab_table} ADD {$nature_of_test} TINYINT(1) NOT NULL DEFAULT '0' AFTER no_of_tests;";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        if (!$result) {
              $flag = false;
              $errors[]= mysqli_error($connection);
        }
        // create child table
        $foreign_key_constraint_name = $nature_of_test . '_' . 'fk_1';
        $query = "CREATE TABLE {$test_table} (
               id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
               sample_id varchar(20) NOT NULL UNIQUE KEY,
               status enum('submitted','pending','completed','finished') NOT NULL,
               creation_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
               sample_description varchar(2000) DEFAULT NULL,
               test_standard varchar(500) DEFAULT NULL,
               temperature varchar(50) DEFAULT NULL,
               humidity varchar(50) DEFAULT NULL,
               first_cv varchar(100) DEFAULT NULL,
               sec_cv varchar(100) DEFAULT NULL,
               third_cv varchar(100) DEFAULT NULL,
               fourth_cv varchar(100) DEFAULT NULL,
               test_conditions text,
               test_result text,
               test_file varchar(255) DEFAULT NULL,
               finished_date datetime DEFAULT NULL,
               CONSTRAINT {$foreign_key_constraint_name}
               FOREIGN KEY (sample_id) 
               REFERENCES {$lab_table}(sample_id) 
               ON DELETE CASCADE 
               ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        if (!$result) {
              $flag = false;
              $errors[]= mysqli_error($connection);
        }
        if($flag){
            mysqli_commit($connection);
            return true;
        }
        else{
            mysqli_rollback($connection);
            return $errors;
        }
		
    }
    function update_test_detail($id,$sample_type,$test_standards,$particulars_of_test,$price) {
        global $connection;
        
        $safe_test_id = mysqli_real_escape_string($connection, $id);

        $query  = "UPDATE test_charges ";
        $query .= "SET sample_type='{$sample_type}',"; 
        if(empty($test_standards)){
           $query .= "test_method=NULL,"; 
        }
        else{
            $query .= "test_method='{$test_standards}',"; 
        }
        
        $query .= "particulars_of_test='{$particulars_of_test}', price={$price} ";
        $query .= "WHERE id='{$safe_test_id}' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        if ($result && mysqli_affected_rows($connection) == 1){
           return $result;  
        }
        return false;
    }

    function get_admin_info() {
            global $connection;

            $query  = "SELECT * ";
            $query .= "FROM ntrc_management ";
            $query .= "WHERE privileges = 'admin' AND visible=0 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            $admin = mysqli_fetch_assoc($result);
            return $admin;
        }
    function get_user_info($privileges) {
            global $connection;

            $query  = "SELECT * ";
            $query .= "FROM ntrc_management ";
            $query .= "WHERE privileges = '{$privileges}' AND visible=1 ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            $user = mysqli_fetch_assoc($result);
            return $user;
    }
	function form_errors($errors=array()) {
		$output = "";
		if (!empty($errors)) {
          $output .= "<div class=\"alert alert-danger fade in\">";
		  $output .= "<span class=\"close\" data-dismiss=\"alert\">×</span>";
		  $output .= "<i class=\"fa fa-times fa-lg pull-left\"></i>";
          $output .= "<div class=\"error\">";
		  $output .= "<b>Please fix the following errors:</b>";
		  $output .= "<ul>";
		  foreach ($errors as $key => $error) {
		        $output .= "<li style=\"text-align: left;\">";
				$output .= htmlentities($error);
				$output .= "</li>";
		  }
		  $output .= "</ul>";
		  $output .= "</div>";
          $output .= "</div>";
		}
		return $output;
    }

    function query_status($query_success) {
		$output = "";
		if(isset($_SESSION["message"])&&$query_success===TRUE) {
            
		  $output .= "<div class=\"alert alert-success fade in\">";
		  $output .= "<span class=\"close\" data-dismiss=\"alert\">×</span>";
		  $output .= "<i class=\"fa fa-check fa-lg pull-left\"></i>";
          $output .= message();
		  $output .= "</div>";
		}
        if(isset($_SESSION["message"])&&$query_success===FALSE){
            
          $output .= "<div class=\"alert alert-danger fade in\">";
		  $output .= "<span class=\"close\" data-dismiss=\"alert\">×</span>";
		  $output .= "<i class=\"fa fa-times fa-lg pull-left\"></i>";
          $output .= message();
		  $output .= "</div>";
        }
		return $output;
    }

    function update_user($id, $full_name,$username,$password,$email,$privileges) {
        global $connection;
        
        $safe_user_id = mysqli_real_escape_string($connection, $id);

        $query  = "UPDATE ntrc_management ";
        $query .= "SET name='{$full_name}', username='{$username}', password='{$password}', email='{$email}',privileges='{$privileges}' ";
        $query .= " WHERE id='{$safe_user_id}' AND visible = 1 ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_affected_rows($connection) == 1){
           return $result;  
        }
        return false;
    }
    
    function add_new_user($full_name,$username,$password,$email,$privileges) {
        global $connection;

        $query  = "INSERT INTO ntrc_management (";
        $query .= " name, username, password, email, privileges";
        $query .= ") VALUES (";
        $query .= " '{$full_name}','{$username}', '{$password}', '{$email}','{$privileges}'";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
    }
    function exists_old_user($privileges) {
        global $connection;

        $query  = "SELECT * ";  
        $query .= "FROM ntrc_management ";
        $query .= "WHERE privileges='{$privileges}' ";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        if($user = mysqli_fetch_assoc($result))
            return true;
        else 
            return false;
    }
    function get_user_picture_path($privileges,$from='admin_or_reception') {
        global $connection;
        
        if($from=='admin_or_reception')
            $path = '../assets/img/users_pics/';
        else
            $path = '../../assets/img/users_pics/';
        switch($privileges){
            case 'admin':
                $folder_name = 'admin';break;
            case 'anti_microbial_lab':
                $folder_name = 'anti_microbial_lab';break;
            case 'applied_chemistry_lab':
                $folder_name = 'applied_chemistry_lab';break;
            case 'chemistry_lab':
                $folder_name = 'chemistry_lab';break;
            case 'coating_lab':
                $folder_name = 'coating_lab';break;
            case 'comfort_lab':
                $folder_name = 'comfort_lab';break;
            case 'composite_characterization_lab':
                $folder_name = 'composite_characterization_lab';break;
            case 'composite_manufacturing_lab':
                $folder_name = 'composite_manufacturing_lab';break;
            case 'eco_textiles_lab':
                $folder_name = 'eco_textiles_lab';break;
            case 'garments_dept_lab':
                $folder_name = 'garments_dept_lab';break;
            case 'garments_manufacturing_lab':
                $folder_name = 'garments_manufacturing_lab';break;
            case 'knitting_lab':
                $folder_name = 'knitting_lab';break;
            case 'materials_and_testing_lab':
                $folder_name = 'materials_and_testing_lab';break;
            case 'mechanical_lab':
                $folder_name = 'mechanical_lab';break;
            case 'nano_materials1_lab':
                $folder_name = 'nano_materials1_lab';break;
            case 'nano_materials2_lab':
                $folder_name = 'nano_materials2_lab';break;
            case 'non_wooven_lab':
                $folder_name = 'non_wooven_lab';break;
            case 'organic_chemistry_lab':
                $folder_name = 'organic_chemistry_lab';break;
            case 'physical_chemistry_lab':
                $folder_name = 'physical_chemistry_lab';break;
            case 'plasma_coating_lab':
                $folder_name = 'plasma_coating_lab';break;
            case 'polymer_dept_lab':
                $folder_name = 'polymer_dept_lab';break;
            case 'reception':
                $folder_name = 'reception';break;
            case 'sem_lab':
                $folder_name = 'sem_lab';break;
            case 'spectroscopy_lab':
                $folder_name = 'spectroscopy_lab';break;
            case 'spinning_lab':
                $folder_name = 'spinning_lab';break;
            case 'tpcl_lab':
                $folder_name = 'tpcl_lab';break;
            case 'weaving_lab':
                $folder_name = 'weaving_lab';break;
            case 'wet_processing_lab':
                $folder_name = 'wet_processing_lab';break;
            case 'xray_diffraction_lab':
                $folder_name = 'xray_diffraction_lab';break;
        }
        $path = $path . $folder_name . '/';    

        $query  = "SELECT * ";
        $query .= "FROM ntrc_management ";
        $query .= "where privileges = '{$privileges}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        $user = mysqli_fetch_assoc($result);
        if(!$user){
//            return null;
//            or return default ntrc picture path
            if($from=='admin_or_reception')
                return "../assets/img/ntrc.png";
            else
                return "../../assets/img/ntrc.png";
        }
        else{
            $path = $path . $user['display_picture'];
            return $path;
        }
        
    }
    function update_users_pic($privileges,$profile_pic_path) {
        global $connection;

        $query  = "UPDATE ntrc_management ";
        $query .= "SET display_picture ='{$profile_pic_path}' ";
        $query .= "where privileges = '{$privileges}' LIMIT 1 ";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_affected_rows($connection) == 1){
           return $result;
        }
        return false;
    }
    function get_customer_by_type($customer_id,$type) {
        global $connection;
        $safe_customer_id = mysqli_real_escape_string($connection, $customer_id);
        if($type=='commercial' || $type=='academic commercial'){
           $query  = "Select * from commercial_customers "; 
        }
        else{
           $query  = "Select * from academic_students "; 
        }
        $query .= "where customer_id='{$safe_customer_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        if ($customer = mysqli_fetch_assoc($result)){
           return $customer;  
        }
        return null;
    }
    function get_student_by_id($customer_id) {
        global $connection;
        $safe_customer_id = mysqli_real_escape_string($connection, $customer_id);
        $query  = "Select * from academic_students "; 
        $query .= "where customer_id='{$safe_customer_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        if ($customer = mysqli_fetch_assoc($result)){
           return $customer;  
        }
        return null;
    }
    function find_price_by_test_name($nameOfTest) {
        global $connection;

        $query  = "SELECT * FROM test_charges ";
        $query .= "WHERE nature_of_test ='{$nameOfTest}' ";
        $query .= "LIMIT 1 ";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        $test = mysqli_fetch_assoc($result);
        if(isset($test)){
            return $test['price'];
        }
        else
            return null;
    }
    function unique_sample_id($sample_id){
        global $connection;
        $query  = "SELECT * ";
        $query .= "FROM orders ";
        $query .= "WHERE sample_id= '{$sample_id}' ";
        $query .= "LIMIT 1";
        $sample_set = mysqli_query($connection, $query);
        confirm_query($sample_set);
        if($sample = mysqli_fetch_assoc($sample_set)) {
            return false;
        } else {
            return true;
        }
    }
    function generate_sample_id($length){
          $chars = "1234567890";
          $clen   = strlen( $chars )-1;
          $id  = '';

          for ($i = 0; $i < $length; $i++) {
                  $id .= $chars[mt_rand(0,$clen)];
          }
       
          return ($id);
    }
    function get_sample_id(){
          do{
            $sample_id  = date ("m");
            $sample_id .= generate_sample_id(8);
            $sample_id .= date ("y");
          }while(!unique_sample_id($sample_id));
        return $sample_id;
        
    }
    function unique_customer_id($customer_id){
        global $connection;
        $query  = "SELECT * ";
        $query .= "FROM commercial_customers ";
        $query .= "WHERE customer_id='{$customer_id}' ";
        $query .= "LIMIT 1";
        $customer_set = mysqli_query($connection, $query);
        confirm_query($customer_set);
        if($customer = mysqli_fetch_assoc($customer_set)) {
            return false;
        }
        $query  = "SELECT * ";
        $query .= "FROM academic_students ";
        $query .= "WHERE customer_id='{$customer_id}' ";
        $query .= "LIMIT 1";
        $student_set = mysqli_query($connection, $query);
        confirm_query($student_set);
        if($student = mysqli_fetch_assoc($student_set)) {
            return false;
        }
        else {
            return true;
        }

    }
    function generate_customer_id($length){
          $chars = "1234506789";
          $clen   = strlen( $chars )-1;
          $id  = '';

          for ($i = 0; $i < $length; $i++) {
                  $id .= $chars[random_int(0,$clen)];
          }

          return ($id);
      }
	 function get_customer_id(){
        do {
            $customer_id = date("d");
            $customer_id .= date("m");
            $customer_id .= generate_customer_id(8);
        }while(!unique_customer_id($customer_id));
        return $customer_id;
        
    }
    function add_commercial_customer( $customer_id,$customer_reference,$c_name,$city,$designation,$organization,$phone,$email,$address){
        global $connection;
        
        $query  = "INSERT INTO commercial_customers (";
        $query .= " customer_id, name, city, designation, organization, customer_ref, phone, email, address ";
        $query .= ") VALUES (";
        $query .= "  '{$customer_id}', '{$c_name}', '{$city}', '{$designation}', '{$organization}', '{$customer_reference}', '{$phone}', '{$email}', '{$address}'";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function add_academic_customer( $customer_id,$c_name,$city,$designation,$department,$institute,$reg_no,$topic_of_study,$phone,$email){
        global $connection;
        
        $query  = "INSERT INTO academic_students (";
        $query .= " customer_id, name, city, designation, department, institute, reg_no, topic_of_study, phone, email ";
        $query .= ") VALUES (";
        $query .= "  '{$customer_id}', '{$c_name}', '{$city}', '{$designation}', '{$department}', '{$institute}', '{$reg_no}','{$topic_of_study}', '{$phone}', '{$email}' ";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function insert_sample($sample_id,$sample_category,$sample_type,$sample_color,$sample_style,$sample_weight,$sample_test_description,$no_of_tests,$sample_image_name,$lab,$all_tests){
         global $connection;
         $table = $lab . '_' .'samples';
         $tests_values = [];
          foreach ($all_tests as $test)
            {
                $tests[]=uglify_fieldname($test);
                $tests_values[] = 1;
            }
         $tests_query_string = implode(",",$tests);
         $tests_query_values = implode(",",$tests_values);
        
        $query  = "INSERT INTO {$table} (";
        $query .= " sample_id, sample_category, sample_type, sample_color, sample_style, sample_weight, sample_test_detail, no_of_tests, sample_image,{$tests_query_string} ";
        $query .= ") VALUES (";
        $query .= " '{$sample_id}', '{$sample_category}', '{$sample_type}', '{$sample_color}', '{$sample_style}', '{$sample_weight}', '{$sample_test_description}', '{$no_of_tests}', '{$sample_image_name}',{$tests_query_values} ";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
        
    }
    function place_order($customer_id,$sample_id,$expected_date,$lab,$order_type,$payment,$payment_received,$payment_pending,$status){
        global $connection;
        
        $query  = "INSERT INTO orders (";
        $query .= " customer_id, sample_id, expected_date, lab, type, payment, payment_received, payment_pending, status ";
        $query .= ") VALUES (";
        $query .= "  '{$customer_id}', '{$sample_id}', '{$expected_date}', '{$lab}', '{$order_type}', '{$payment}', '{$payment_received}', '{$payment_pending}', '{$status}'";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
    }
    function get_recent_customer(){
        global $connection;

        $query  = "SELECT * ";
        $query .= "FROM commercial_customers ";
        $query .= "WHERE id = (SELECT MAX(id) FROM commercial_customers);";
        $customer_set = mysqli_query($connection, $query);
        confirm_query($customer_set);
        if($customer = mysqli_fetch_assoc($customer_set)) {
            return $customer;
        } else {
            return null;
        }
    }
    function get_recent_student(){
        global $connection;

        $query  = "SELECT * ";
        $query .= "FROM academic_students ";
        $query .= "WHERE id = (SELECT MAX(id) FROM academic_students);";
        $student_set = mysqli_query($connection, $query);
        confirm_query($student_set);
        if($student = mysqli_fetch_assoc($student_set)) {
            return $student;
        } else {
            return null;
        }
    }
    function get_customer_order_by_id($customer_id) {
		global $connection;
		
		$safe_customer_id = mysqli_real_escape_string($connection, $customer_id);
		
		$query  = "SELECT * ";
		$query .= "FROM commercial_customers, orders ";
		$query .= "WHERE commercial_customers.customer_id=orders.customer_id ";
        $query .= "AND commercial_customers.customer_id='{$safe_customer_id}' ";
		$query .= "LIMIT 1";
		$customer_set = mysqli_query($connection, $query);
		confirm_query($customer_set);
		if($customer = mysqli_fetch_assoc($customer_set)) {
			return $customer;
		} else {
			return null;
		}
	}
    function get_student_order_by_id($customer_id) {
		global $connection;
		
		$safe_customer_id = mysqli_real_escape_string($connection, $customer_id);
		
		$query  = "SELECT * ";
		$query .= "FROM academic_students, orders ";
		$query .= "WHERE academic_students.customer_id=orders.customer_id ";
        $query .= "AND academic_students.customer_id='{$safe_customer_id}' ";
		$query .= "LIMIT 1";
		$customer_set = mysqli_query($connection, $query);
		confirm_query($customer_set);
		if($customer = mysqli_fetch_assoc($customer_set)) {
			return $customer;
		} else {
			return null;
		}
	}
    function find_test_names_of_sample($sample_id,$lab) {
		global $connection;
		
		$safe_sample_id = mysqli_real_escape_string($connection, $sample_id);
        $lab_sample = get_lab_sample($sample_id,$lab);
		
		$query  = "SELECT nature_of_test ";
		$query .= "FROM test_charges ";
		$query .= "WHERE lab='{$lab}';";
		$tests_set = mysqli_query($connection, $query);
		confirm_query($tests_set);
        $test_names = [];
		while($test = mysqli_fetch_assoc($tests_set)) {
            $test_attribute = uglify_fieldname($test['nature_of_test']);
            if($lab_sample[$test_attribute]==1){
               $test_names[] = $test['nature_of_test']; 
            }	
		}
        return $test_names;
	}
    function get_lab_sample($sample_id,$lab) {
		global $connection;
		
		$safe_sample_id = mysqli_real_escape_string($connection, $sample_id);
        $table = $lab . '_' . 'samples';
		
		$query  = "SELECT * ";
		$query .= "FROM {$table} ";
		$query .= "WHERE sample_id='{$safe_sample_id}' LIMIT 1";
		$sample_set = mysqli_query($connection, $query);
		confirm_query($sample_set);
		if($sample = mysqli_fetch_assoc($sample_set)) {
            return $sample;
		}
        else{
            return null;
        }
	}
    function delete_customer_sample_order($customer_id,$table) {
            global $connection;
            $safe_customer_id = mysqli_real_escape_string($connection, $customer_id);

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE customer_id={$safe_customer_id} ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function delete_sample_test_record($sample_id,$table) {
            global $connection;
            $safe_sample_id = mysqli_real_escape_string($connection, $sample_id);

            $query  = "DELETE ";
            $query .= "FROM {$table} ";
            $query .= "WHERE sample_id={$safe_sample_id} ";
            $query .= "LIMIT 1";
            $result = mysqli_query($connection, $query);
            confirm_query($result);
            return $result;

    }
    function insert_lab_tests_by_test_names($sample_id,$test_name){
        global $connection;
        
        $test_table = uglify_fieldname($test_name) . "_" . "test";
        $status = 'submitted';
        
        $query  = "INSERT INTO {$test_table} (";
        $query .= " sample_id, status ";
        $query .= ") VALUES (";
        $query .= "  '{$sample_id}', '{$status}' ";
        $query .= ")";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function get_test_names_by_lab($lab){
        global $connection;
		
		$query  = "SELECT nature_of_test ";
		$query .= "FROM test_charges ";
		$query .= "WHERE lab='{$lab}';";
		$tests_set = mysqli_query($connection, $query);
		confirm_query($tests_set);
        $test_names = [];
		while($test = mysqli_fetch_assoc($tests_set)) {
               $test_names[] = $test['nature_of_test']; 
        }	
        return $test_names;
  
    }
    function update_orders_status($sample_id,$status){
        global $connection;
        
        $query  = "UPDATE orders ";
        $query .= "SET status='{$status}' ";
        $query .= "WHERE sample_id='{$sample_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function update_orders_status_and_finished_date($sample_id,$status,$finished_date,$pending_payment,$payment_received){
        global $connection;
        
        $query  = "UPDATE orders ";
        $query .= "SET status='{$status}', ";
        $query .= "payment_pending='{$pending_payment}', ";
        $query .= "payment_received='{$payment_received}', ";
        $query .= "finished_date='{$finished_date}' ";
        $query .= "WHERE sample_id='{$sample_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function update_orders_status_and_completion_date($sample_id,$status,$completion_date){
        global $connection;
        
        $query  = "UPDATE orders ";
        $query .= "SET status='{$status}', ";
        $query .= "completion_date='{$completion_date}' ";
        $query .= "WHERE sample_id='{$sample_id}' LIMIT 1";
        $result = mysqli_query($connection, $query);
        return $result;
  
    }
    function get_lab_names($order){
        global $connection;
        
        $query  = "SELECT lab ";
        $query .= "FROM test_charges ";
        $query .= "GROUP BY lab ";
        $query .= "ORDER by lab {$order}";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
  
    }
    function get_delayed_samples_and_count_by_status($status,$lab='') {
        global $connection;
        
        $query  = "SELECT * ";
		$query .= "FROM orders ";
		$query .= "WHERE status='{$status}' ";
        if($lab!=='')
            $query .= "AND lab='{$lab}' ";
        $query .= "AND expected_date <= NOW();";
		$delayed_samples = mysqli_query($connection, $query);
        $total_delays_count = mysqli_num_rows($delayed_samples);
        confirm_query($delayed_samples);
        return array($delayed_samples,$total_delays_count);	
    }
    function get_delayed_time_count_label($time,$expected_time){
        date_default_timezone_set('Asia/Karachi');
        if(($time-strtotime($expected_time))>604800){
          $label = ' <span class="label label-danger">&gt; 1 wk</span>';
        }
        elseif(($time-strtotime($expected_time))>518400){
          $label = ' <span class="label label-danger">&gt; 6 d</span>';
        }
        elseif(($time-strtotime($expected_time))>432000){
          $label = ' <span class="label label-danger">&gt; 5 d</span>';
        }
        elseif(($time-strtotime($expected_time))>345600){
          $label = ' <span class="label label-danger">&gt; 4 d</span>';
        }
        elseif(($time-strtotime($expected_time))>259200){
          $label = ' <span class="label label-danger">&gt; 3 d</span>';
        }
        elseif(($time-strtotime($expected_time))>172800){
          $label = ' <span class="label label-danger">&gt; 2 d</span>';
        }
        elseif(($time-strtotime($expected_time))>86400){
          $label = ' <span class="label label-danger">&gt; 1 d</span>';
        }
        elseif(($time-strtotime($expected_time))>43200){
          $label = ' <span class="label label-danger">&gt; 12 h</span>';
        }
        elseif(($time-strtotime($expected_time))>36000){
          $label = ' <span class="label label-danger">&gt; 10 h</span>';
        }
        elseif(($time-strtotime($expected_time))>25200){
          $label = ' <span class="label label-danger">&gt; 7 h</span>';
        }                                            
        elseif(($time-strtotime($expected_time))>18000){
          $label = ' <span class="label label-danger">&gt; 5 h</span>';
        }
        elseif(($time-strtotime($expected_time))>10800){
          $label = ' <span class="label label-danger">&gt; 3 h</span>';
        }
        elseif(($time-strtotime($expected_time))>3600){
          $label = ' <span class="label label-danger">&gt; 1 h</span>';
        }
        else{
         $label = '';   
        }
        return $label;
    }
    function get_short_time_diff_from_now($past_time){
        date_default_timezone_set('Asia/Karachi');
        $today = time();
        $past_time = strtotime($past_time);
        $seconds = $today - $past_time;
        
        $days = floor($seconds / 86400);
        $seconds %= 86400;

        $hours = floor($seconds / 3600);
        $seconds %= 3600;

        $minutes = floor($seconds / 60);
        $seconds %= 60;
       
        $time_string = ""; 

        if($days>=1){
           $symbol = ($days>1) ? 's':''; 
           $time_string = "{$days} day{$symbol} ago";
        }
        elseif($hours>=1){
            $symbol = ($hours>1) ? 's':''; 
            $time_string = "{$hours} hr{$symbol} ago";
        }
        elseif($minutes>=1){
            $symbol = ($minutes>1) ? 's':''; 
            $time_string = "{$minutes} min{$symbol} ago";
        }
        else{
           $symbol = ($seconds>1) ? 's':''; 
           $time_string = "{$seconds} sec{$symbol} ago";
        }

        $label = " <span class=\"label label-info\"> {$time_string}</span>";
        return $label;
    }
    function get_time_diff_from_now($past_time){
        date_default_timezone_set('Asia/Karachi');
        $current_time = time();
        $past_time = strtotime($past_time);
        $seconds = $current_time - $past_time;
        $days = floor($seconds / 86400);
        $seconds %= 86400;

        $hours = floor($seconds / 3600);
        $seconds %= 3600;

        $minutes = floor($seconds / 60);
        $seconds %= 60;
        // message date string
        $time_string = ""; 

        if($days>=1){
            if($days==1){
                $date = date('d-m-Y',$past_time);
                $yesterday = date('d-m-Y',strtotime('yesterday'));
                if($date==$yesterday){
                    $time = date('g:i A',$past_time);
                    $time_string = "Yesterday at {$time}";
                }
                else{
                    $time = date('M j \a\t g:i A',$past_time);
                    $time_string = $time;
                }  
            }
            elseif($days<365){
                $time = date('M j \a\t g:i A',$past_time);
                $time_string = $time;
            }
            else{
                $time = date('M j,Y \a\t g:i A',$past_time);
                $time_string = $time;
            }
        }
        elseif($hours>=1){
            $symbol = ($minutes>1) ? 's':'';
            if($hours==1)
                $time_string = "{$hours} hr {$minutes} min{$symbol} ago";
            else
                $time_string = "{$hours} hrs {$minutes} min{$symbol} ago";
        }
        elseif($minutes>=1){
            if($minutes==1)
                $time_string = "{$minutes} min ago";
            else
                $time_string = "{$minutes} mins ago"; 
        }
        else{
            $time_string = "{$seconds} sec ago";
        }
        return $time_string;
    }
    function get_deadline_alert_label_from_now($expected_time){
        date_default_timezone_set('Asia/Karachi');
        $current_time = time();
        $expected_time = strtotime($expected_time);
        if($expected_time<=$current_time){
            $label = ' <span class="label label-danger">Passed</span>';
        }
        else{
            $seconds = $expected_time - $current_time;
            
            $hours = floor($seconds / 3600);
            $next_hour = $hours + 1;
            if($hours<1){
              $label = ' <span class="label label-danger">&lt; '. $next_hour . ' hr</span>';
            }
            elseif($hours<3){
              $label = ' <span class="label label-danger">&lt; '. $next_hour . ' hrs</span>';
            }
            elseif($hours<12){
              $label = ' <span class="label label-warning">&lt; '. $next_hour . ' hrs</span>';
            }
            elseif($hours<24){
               $label = ' <span class="label label-info">&lt; '. $next_hour . ' hrs</span>';
            }
            else{
                $label = '';
            }
        }
        return $label;
    }
    function get_samples_count_by_status($status) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM orders ";
		$query .= "WHERE status='{$status}' ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_samples = mysqli_fetch_array($result)[0];
        return $total_samples;
		
    }
    function get_samples_by_status($status) {
        global $connection;
        $query  = "SELECT * ";
		$query .= "FROM orders ";
		$query .= "WHERE status='{$status}' ORDER BY id ASC ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
		
    }
    function get_samples_by_status_descending_order($status) {
        global $connection;
        $query  = "SELECT * ";
		$query .= "FROM orders ";
		$query .= "WHERE status='{$status}' ORDER BY id DESC ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
		
    }
    function find_order_by_customer_id($customer_id) {
        global $connection;
        $safe_customer_id = mysqli_real_escape_string($connection, $customer_id);
        $query  = "SELECT * ";
		$query .= "FROM orders ";
		$query .= "WHERE customer_id='{$safe_customer_id}' LIMIT 1 ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        if($order = mysqli_fetch_assoc($result)){
          return $order;  
        }
        return null;
    }
    function get_samples_count_by_lab_and_status($lab,$status) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM orders ";
		$query .= "WHERE lab='{$lab}' AND status='{$status}' ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_samples = mysqli_fetch_array($result)[0];
        return $total_samples;
		
    }
    function get_finalized_and_finished_samples_count_by_lab($lab) {
        global $connection;
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM orders ";
		$query .= "WHERE (status='finalized' OR status='finished') AND ";
        $query .= "lab='{$lab}' ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $total_samples = mysqli_fetch_array($result)[0];
        return $total_samples;
		
    }
    function get_lab_samples_by_lab_and_status($lab,$status) {
        global $connection;
        $lab_table = $lab . '_' . 'samples';
        $query  = "SELECT * ";
		$query .= "FROM orders,{$lab_table} ";
		$query .= "WHERE orders.sample_id={$lab_table}.sample_id ";
        $query .= "AND orders.status='{$status}' ORDER BY orders.id ASC;";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
		
    }
    function get_finalized_and_finished_samples_by_lab($lab) {
        global $connection;
        
        $lab_table = $lab . '_' . 'samples';
        $query  = "SELECT * ";
		$query .= "FROM orders,{$lab_table} ";
		$query .= "WHERE orders.sample_id={$lab_table}.sample_id ";
        $query .= "AND (orders.status='finalized' OR orders.status='finished') ORDER BY orders.id DESC"; 
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
		
    }
     function find_test_names_of_sample_by_lab_sample($lab_sample,$lab) {
		global $connection;
		
		$query  = "SELECT nature_of_test ";
		$query .= "FROM test_charges ";
		$query .= "WHERE lab='{$lab}';";
		$tests_set = mysqli_query($connection, $query);
		confirm_query($tests_set);
        $test_names = [];
		while($test = mysqli_fetch_assoc($tests_set)) {
            $test_attribute = uglify_fieldname($test['nature_of_test']);
            if($lab_sample[$test_attribute]==1){
               $test_names[] = $test['nature_of_test']; 
            }	
		}
        return $test_names;
	}
    function get_test($sample_id,$test_table) {
        global $connection;
        
        $query  = "SELECT * ";
		$query .= "FROM {$test_table} ";
		$query .= "WHERE sample_id='{$sample_id}' ";
        $query .= "LIMIT 1";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $test = mysqli_fetch_assoc($result);
        return $test;
		
    }
    function search_customer_id_by_ajax($key) {
        global $connection;
        
        $query  = "SELECT customer_id ";
		$query .= "FROM orders ";
		$query .= "WHERE customer_id LIKE '%{$key}%' ";
        $query .= "ORDER BY id DESC ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
		
    }
    function update_users_settings($full_name,$user_name,$email,$privileges,$display_pic) {
        global $connection;
        
        $query  = "UPDATE ntrc_management ";
		$query .= "SET name='{$full_name}',username='{$user_name}', ";
        $query .= "email='{$email}' ";
        if(!(empty($display_pic)&&$display_pic=="")){
            $query .= ",display_picture='{$display_pic}' ";
        }
		$query .= "WHERE privileges='{$privileges}' ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        if($result&&mysqli_affected_rows($connection) == 1){
           return $result; 
        }
        return null;	
    }
    function submit_sample_test_result($test_table,$sample_id,$status,$test_standard,$temperature,$humidity,$test_results,$test_conditions,$sample_test_file_name,$finished_date){
        global $connection;
        
        $query  = "UPDATE {$test_table} ";
		$query .= "SET status='{$status}',test_standard='{$test_standard}', ";
        $query .= "temperature='{$temperature}',humidity='{$humidity}', ";
        $query .= "test_result='{$test_results}',";
        $query .= "test_conditions='{$test_conditions}',";
        $query .= "finished_date='{$finished_date}', ";
        $query .= "test_file='{$sample_test_file_name}' ";
        $query .= "WHERE sample_id='{$sample_id}' ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function get_todays_customers_count(){
        global $connection;
        // todays_customers actually same as todays samples
        $today_date = date('Y-m-d');
        
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM orders ";
        $query .= "WHERE timestamp BETWEEN '$today_date' AND '$today_date' + INTERVAL 1 DAY ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $todays_customers = mysqli_fetch_array($result)[0];
        return $todays_customers;
    }
    function get_todays_labwise_samples(){
        global $connection;
        
        $today_date = date('Y-m-d');
        
        $query  = "SELECT lab,COUNT(*) as count,SUM(payment) as sum_payment ";
		$query .= "FROM orders ";
        $query .= "WHERE timestamp BETWEEN '$today_date' AND '$today_date' + INTERVAL 1 DAY ";
        $query .= "GROUP BY lab ORDER BY count DESC ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function get_yesterday_labwise_samples(){
        global $connection;
        //$yesterday_date = date('Y-m-d', strtotime("-1 day"));
        $yesterday_date = date('Y-m-d', strtotime("yesterday"));
        
        $query  = "SELECT lab,COUNT(*) as count,SUM(payment) as sum_payment ";
		$query .= "FROM orders ";
        $query .= "WHERE timestamp BETWEEN '$yesterday_date' AND '$yesterday_date' + INTERVAL 1 DAY ";
        $query .= "GROUP BY lab ORDER BY count DESC ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function get_last_seven_days_samples_count_by_lab($lab){
        global $connection;

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $third_day = date('Y-m-d', strtotime("-2 day"));
        $fourth_day = date('Y-m-d', strtotime("-3 day"));
        $fifth_day = date('Y-m-d', strtotime("-4 day"));
        $sixth_day = date('Y-m-d', strtotime("-5 day"));
        $seventh_day = date('Y-m-d', strtotime("-6 day"));
        
        $last_seven_days = array($today,$yesterday,$third_day, $fourth_day,$fifth_day,$sixth_day,$seventh_day);
        
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM orders ";
        $query .= "WHERE lab='{$lab}' ";
        foreach ($last_seven_days as $day){
            $time_period = "AND timestamp BETWEEN '$day' AND '$day' + INTERVAL 1 DAY ";
            $full_query = $query . $time_period;
            $result = mysqli_query($connection, $full_query);
            confirm_query($result);
            $day_samples_count = mysqli_fetch_array($result)[0];
            $last_seven_days_counts[] = $day_samples_count;
        }
        return $last_seven_days_counts;
    }
    function get_online_users($status,$visible='all'){
        global $connection;
        
        $query  = "SELECT * ";
		$query .= "FROM ntrc_management ";
        $query .= "WHERE status={$status} ";
        if($visible!=='all')
            $query .= "AND visible={$visible}";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function get_unseen_messages_count($table){
        global $connection;
        
        $query  = "SELECT COUNT(*) ";
		$query .= "FROM {$table} ";
        $query .= "WHERE status=0";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        $unseen_messages_count = mysqli_fetch_array($result)[0];
        return $unseen_messages_count;
    }
    function get_recent_messages($no_of_messages,$table){
        global $connection;
        
        $query  = "SELECT id,sender,subject,timestamp ";
		$query .= "FROM {$table} ";
        $query .= "ORDER BY id DESC ";
        $query .= "LIMIT {$no_of_messages}";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function update_unseen_messages_status($table){
        global $connection;
        
        $query  = "UPDATE {$table} ";
		$query .= "SET status=1 ";
        $query .= "WHERE status=0 ";
		$result = mysqli_query($connection, $query);
        confirm_query($result);
        return $result;
    }
    function authenticate_admin_old_password($old_password){
        global $connection;
   
        $query  = "SELECT * FROM ntrc_management ";
        $query .= "WHERE password = '{$old_password}' ";
        $query .= "AND privileges = 'admin'  AND visible=0 ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        $admin = mysqli_fetch_assoc($result);
        return $admin;
    }

    function edit_admin_password($new_password){
        global $connection;
   
        $query  = "UPDATE ntrc_management ";
        $query .= "SET password = '{$new_password}' ";
        $query .= "WHERE privileges = 'admin'  AND visible=0 ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        return $result;
    }

	function password_check($password, $submitted_password) {
	  if($password===$submitted_password){
          return true;
      }
        else{
            return false;
        }
	}
    function updateOnlineStatus($privileges,$status) {
	    global $connection;
   
        $query  = "UPDATE ntrc_management ";
        $query .= "SET status = {$status} ";
        $query .= "WHERE privileges = '{$privileges}' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
	}
	function attempt_login($username, $password) {
        global $connection;
        
        $query  = "SELECT * FROM ntrc_management ";
        $query .= "WHERE username = '{$username}' ";
        $query .= "AND password = '{$password}' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($connection, $query);
        confirm_query($result);
        $user = mysqli_fetch_assoc($result);
		if ($user) {
			// found user
            return $user;
			
		} else {
			// user not found
			return false;
		}
	}

	function logged_in() {
//		return isset($_SESSION['admin_id']);
        if((isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['privileges'])) || (isset($_COOKIE['user_id'])
                && isset($_COOKIE['username']) && isset($_COOKIE['privileges'])))
        return true;
    else
        return false;
    
}
	
    function access_admin(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             // give access
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
              redirect_to("../reception/index.php");
          }
          else{
              redirect_to("../labs/general/index.php");
          }
      }
      else{
          redirect_to("../index.php");
      }

    }
	function access_receptionist(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             redirect_to("../admin/index.php");
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
              // give access
          }
          else{
               redirect_to("../labs/general/index.php");
          }
      }
      else{
          redirect_to("../index.php");
      }

    }
    function access_lab_manager(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             redirect_to("../../admin/index.php");
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
             redirect_to("../../reception/index.php");
          }
          else{
              if(isset($_SESSION['privileges'])){
                  return $_SESSION['privileges'];
              }
              else{
                  return $_COOKIE['privileges'];
              }
             
          }
      }
      else{
          redirect_to("../../index.php");
      }

    }
    function access_admin_from_subfolders(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             // give access
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
              redirect_to("../../reception/index.php");
          }
          else{
              redirect_to("../../labs/general/index.php");
          }
      }
      else{
          redirect_to("../../index.php");
      }

    }
    function access_receptionist_from_subfolders(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             redirect_to("../../admin/index.php");
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
              // give access
          }
          else{
               redirect_to("../../labs/general/index.php");
          }
      }
      else{
          redirect_to("../../index.php");
      }

    }
    function access_lab_manager_from_subfolders(){
      if(logged_in()){
          if((isset($_SESSION['privileges']) && $_SESSION['privileges']==='admin') || $_COOKIE['privileges']==='admin'){
             redirect_to("../../../admin/index.php");
          }
          elseif((isset($_SESSION['privileges']) && $_SESSION['privileges']==='reception') || $_COOKIE['privileges']==='reception'){
             redirect_to("../../../reception/index.php");
          }
          else{
              if(isset($_SESSION['privileges'])){
                  return $_SESSION['privileges'];
              }
              else{
                  return $_COOKIE['privileges'];
              }
             
          }
      }
      else{
          redirect_to("../../../index.php");
      }

    }
?>