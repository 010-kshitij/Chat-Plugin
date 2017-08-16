<?php
include_once 'db_connection.php';

$receiver_id = $_POST['receiver_id'];
$receiver_type = $_POST['receiver_type'];
$sender_id = $_POST['sender_id'];
$sender_type = $_POST['sender_type'];

$query = "SELECT * FROM ".$table_name." WHERE read_status = '0' AND receiver_id = '".$receiver_id."' AND receiver_type = '".$receiver_type."' AND sender_id='".$sender_id."' AND sender_type='".$sender_type."'";
$result = mysqli_query($link, $query);
$chat = array();

if($result != false) {
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $chat[] = $row;
        }
    }
}

die(json_encode($chat));