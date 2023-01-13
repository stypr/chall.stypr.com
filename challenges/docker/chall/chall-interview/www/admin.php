<?php

// I'm php noob, how dare you to pwn me!!
$flag = "flag{054c93fd8b3449e2e705a1a01a21141b}";
$username = $_POST['username'];
$password = $_POST['password'];
if($username == sha1(rand(10000,100000).rand(100,10000)."**SALT**".time()) && $password == md5("milkyway")){
	//die($flag);
	die('ok you were right... but, you cant see the flag!');
}else{
	die('pwn me');
}


?>
<form method=POST>
	<input type=text name=username placeholder=username>
	<input type=password name=password placeholder=password>
	<input type=submit value=login>
</form>
