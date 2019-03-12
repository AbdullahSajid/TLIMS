<?php require_once("../../../includes/session.php"); ?>
<?php require_once("../../../includes/db_connection.php"); ?>
<?php require_once("../../../includes/functions.php"); ?>
<?php
    access_admin_from_subfolders();
    $key=$_GET['key'];
    $array = array();
    $query=search_customer_id_by_ajax($key);
    while($row=mysqli_fetch_assoc($query))
    {
      $array[] = $row['customer_id'];
    }
    echo json_encode($array);
    mysqli_close($connection);
?>