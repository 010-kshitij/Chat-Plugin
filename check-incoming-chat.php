<?php

include_once 'db_connection.php';

$receiver_id = $_POST['receiver_id'];
$receiver_type = $_POST['receiver_type'];

$query = "SELECT * FROM ".$table_name." WHERE read_status = '0' AND receiver_id = '".$receiver_id."' AND receiver_type='".$receiver_type."'";

$result = mysqli_query($link, $query);
$chat = array();

if($result != false) {
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if($row['sender_type'] == "Student") {
                $query = "SELECT firstName, lastName FROM student WHERE sno = '".$row['sender_id']."'";
                $name_result = mysqli_query($link, $query);
                $name_result = mysqli_fetch_assoc($name_result);
                $row['name'] = $name_result['firstName']." ".$name_result['lastName'];
            } else if($row['sender_type'] == "Teacher") {
                $query = "SELECT f_name, l_name FROM t_account WHERE id = '".$row['sender_id']."'";
                $name_result = mysqli_query($link, $query);
                $name_result = mysqli_fetch_assoc($name_result);
                $row['name'] = $name_result['f_name']." ".$name_result['l_name'];
            }
            $chat[] = $row;
        }
    }
}

die(json_encode($chat));
