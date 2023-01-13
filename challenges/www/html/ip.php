<?php
    if($_SERVER['HTTP_X_REAL_IP'])
        die($_SERVER['HTTP_X_REAL_IP']);
	die($_SERVER['REMOTE_ADDR']);
?>
