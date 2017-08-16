<?php
    include_once 'db_connection.php';
	
	$sno = $_POST['sno'];

	$query = "UPDATE ".$table_name." SET read_status = '1' WHERE sno = '".$sno."'";
	$result = mysqli_query($link, $query);
	