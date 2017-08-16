<?php
    include_once 'db_connection.php';

	$message = $_POST['message'];
    $message = filter_var ( $message, FILTER_SANITIZE_STRING);
	$sender_id = $_POST['sender_id'];
	$sender_type = $_POST['sender_type'];
	$receiver_id = $_POST['receiver_id'];
	$receiver_type = $_POST['receiver_type'];
	
	$query = "INSERT INTO ".$table_name."(sender_id, sender_type, receiver_id, receiver_type, message) VALUES('".$sender_id."', '".$sender_type."', '".$receiver_id."', '".$receiver_type."', '".$message."')";
	$result = mysqli_query($link, $query);

	die("".$message);
	