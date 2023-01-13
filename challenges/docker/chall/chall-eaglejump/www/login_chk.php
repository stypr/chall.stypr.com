<?php
	session_start();
	include 'dbconn.php';
	if(!isset($_SESSION['uniq']))
	{
		$id = addslashes($_POST['id']);
		$pw = addslashes($_POST['pw']);
		$q = "SELECT * FROM users where user1d='{$id}' and pa5sw0rd=sha1('{$pw}')";
		$res = mysqli_query($dbconn,$q);
		$row = mysqli_fetch_assoc($res);
		if($row['user1d'])
		{
			include 'global.php';
			$_SESSION['uniq'] = randstr(8);
			echo "<script>alert('Login success'); location.replace('./?p=home');</script>";

		}
		else
		{
			echo "<script>alert('Login Failed'); location.replace('./?p=login');</script>";
		}
		unset($q);
		unset($res);
		unset($row);
		unset($SQL);
	}
	else
		echo "<script>alert('already login'); history.go(-1);</script>";
?>

