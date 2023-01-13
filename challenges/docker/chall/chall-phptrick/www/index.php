<?php
	session_start();
	$ID = $_SESSION['eagle_id'];
	$PW = $_SESSION['eagle_pw'];

	include("./config.php"); // config.php file is null
	include("./function.php");

	$query = urldecode($_SERVER['QUERY_STRING']);
	if(preg_match("/ |_/i", $query)) exit("no hack plz");
	$page = $_GET['page'];
	$board_idx = $_GET['_adm1nkyj_board_idx'];

	if(isset($page))
	{
		if($page === "main")
		{
			if(isset($ID) && isset($PW)) exit("<script>location.href=('./?page=board');</script>");
			
			echo "<center><a href='./?page=login'><b>login</b></a>&nbsp;&nbsp;<a href='./?page=join'><b>join</b></a></center>";
		}
		else if($page === "login")
		{
			if(isset($ID) || isset($PW)) exit("<script>alert('already login');location.href=('./?page=board');</script>");
			
			if(isset($_POST['id']) && isset($_POST['pw']))
			{
				$lid = filter($_POST['id']); // filter is blind
				$lpw = $_POST['pw'];

				$query = @mysql_fetch_array(mysql_query("SELECT * FROM user WHERE ID='{$lid}';"));
				
				if(!$query['ID'])
				{
					exit("<script>alert('not found id');history.go(-1);</script>");
				}
				else
				{
					
					if(adminpasswordcheck($lpw) == true) exit("no hack"); // admmin password and x, b, e string filtering
					if($query['PW'] == $lpw)
					{
						if($query['ID'] == "admin")
						{
							echo __FLAG__;
						}
						else
						{
							$_SESSION['eagle_id'] = addslashes($query['ID']);
							$_SESSION['eagle_pw'] = addslashes($query['PW']);
							echo "<script>alert('login!!');location.href=('./?page=board');</script>";
						}
					}
					else
					{
						exit("<script>alert('wrong password!');history.go(-1);</script>");
					}
				}

		

			}
			echo "
				<center>
					<form action='./?page=login' method='post' name='login'>
						id : <input type='text' name='id'><br/>
						pw : <input type='password' name='pw'><br/>
						<input type='submit' style='width:200px' value='login'>
					</form>
				</center>
				";
		}
		else if($page === "join")
		{
			if(isset($ID) || isset($PW)) exit("<script>alert('already login');location.href=('./?page=board');</script>");
			
			if(isset($_POST['id']) && isset($_POST['pw']))
			{
				$jid = addslashes(trim(substr($_POST['id'],0, 100)));
				$jpw = addslashes($_POST['pw']);
				
				$query = mysql_fetch_array(mysql_query("SELECT ID FROM user WHERE ID='{$jid}';"));
				if($query['ID']) exit("<script>alert('exist user id');history.go(-1);</script>");
				
				$query = mysql_query("INSERT INTO user (ID, PW) VALUES ('{$jid}', '{$jpw}');");
				if($query)
				{
					exit("<script>alert('join!!!');location.href=('./?page=main');</script>");
				}
				else
				{
					exit("<script>alert('fail..');history.go(-1);</script>");
				}
				
			}
			echo "
				<center>
					<form action='./?page=join' method='post' name='join'>
						id : <input type='text' name='id'><br/>
						pw : <input type='password' name='pw'><br/>
						<input type='submit' style='width:200px' value='login'>
					</form>
				</center>
				";

		}
		else if($page === "board")
		{
			if(!isset($ID) || !isset($PW)) exit("<script>alert('plz login');location.href=('./?page=main');</script>");
			if(isset($board_idx))
			{
				$baord_idx = addslashes($board_idx);

				$query = @mysql_fetch_array(mysql_query("SELECT * FROM board WHERE idx={$board_idx};"));
				
				echo "title : ".$query['title']."<br/>body : ".$query['body']."<br/>user : ".$query['user'];
			}
			else
			{
				$query = mysql_query("SELECT * FROM board WHERE 1 ORDER BY idx;");
				echo 
				"
					<center>
						<a href='./?page=logout'><b>logout</b></a><br/><br/>
						<table align='center' border=1>
							<tr>
								<td align='center'>idx</td>
								<td align='center'>title</td>
								<td align='center'>user</td>
							</tr>	
				";
				while($row = mysql_fetch_array($query))
				{
					echo 
					"
						<tr>
							<td align='center'><a href='./?page=board&_adm1nkyj_board_idx={$row['idx']}'>{$row['idx']}</a></td>
							<td align='center'>{$row['title']}</td>
							<td align='center'>{$row['user']}</td>
						</tr>
					";
				}
				echo "
						</table>
					</center>
					";
			}
		}
		else if($page === "logout")
		{
			if(!isset($ID) || !isset($PW)) exit("<script>alert('plz login');location.href=('./?page=main');</script>");
			unset($_SESSION['eagle_id']);
			unset($_SESSION['eagle_pw']);
			echo "<script>alert('logout!');location.href=('./');</script>";
		}
		else if($page === "hint")
		{
			exit(highlight_file(__FILE__));
		}
		else
		{
			exit("<script>location.href=('./?page=main');</script>");
		}
	}
	else
	{
		exit("<script>location.href=('./?page=main');</script>");
	}

?>
