<?php
	$db_name = 'newg4me';
	$db_user = 'eaglejump';
	$db_pass = 'eaglejump@@jump@@stepbystep@@';
	$dbconn = mysqli_connect('10.1.0.137',$db_user,$db_pass,$db_name);

	$q = "SELECT table_name FROM information_schema.tables where table_type='base table' limit 0,1";
	$table = mysqli_fetch_array(mysqli_query($dbconn,$q))[0];
	$q = "SELECT * FROM {$table}";
	$master_key = mysqli_fetch_array(mysqli_query($dbconn,$q))[0];
?>
