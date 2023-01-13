<?php
	session_start();

	if(!isset($_SESSION['uniq']))
		die("<script> alert('Login first.'); location.href='./?p=login'</script>");
	if(!newSQLFilter($_GET['no']))
		die("Attack detected. - filter");
	$q = "/*ID:{$_SESSION['uniq']}*/SELECT * FROM board where no={$_GET['no']}";

	$queryhash = sha1($q);
	if(!preg_match("/[a-zA-Z]/",$_GET['no']))
		setcookie("integrity",$queryhash);
	else
	{
		if($queryhash !== $_COOKIE['integrity'])
			die("Attack detected. ");
	}

	$q = addslashes($q);
	$res = mysqli_query($dbconn,$q);
	$row = mysqli_fetch_array($res);
	echo "<table class='table'>
		<thead>
			<tr>
				<th width=100%><font size=6><b>{$row['title']}</b></font></th>
			</tr>
			<tr>
				<td>{$row['body']}</td>
			</tr>
		</thead>
	<tbody>
	</table>";
	unset($q);
	unset($res);
	unset($row);
	unset($queryhash);
?>
