<?php
	include 'header.php';
	include 'dbconn.php';
	if(isset($_GET['p']))
	{
		include 'newfilter.php';
		if(newLfiFilter($_GET['p']))
		{
			if(file_exists($_GET['p'].".php"))
				include $_GET['p'].".php";
			else
				include $_GET['p'];

		}
		else
			die("Attack Detected");
	}
	else
		include 'home.php';
	include 'footer.php';
	unset($_GET)
?>
