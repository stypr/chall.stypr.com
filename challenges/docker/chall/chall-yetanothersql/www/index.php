<?php
error_reporting(0);
?><html>
	<head>
		<meta charset="utf-8"/>
		<title>YetAnotherSQL</title>
	</head>
	<body>
		<center>
<?php
    $user = $_GET['username'];
    $pass = $_GET['password'];
    if(isset($user) && isset($pass)){
        if(preg_match("/procedure|md5|char|group|benchmark|reverse|space|when|rand|end|else|null|'|if|pad|insert|like|case|ascii|ord|substr|mid|union|left|mid|right|information|sleep|table|column/i", $user) || preg_match("/procedure|group|rand|md5|char|benchmark|reverse|space|when|end|else|null|'|if|pad|insert|like|case|ascii|ord|substr|mid|union|left|mid|right|information|sleep|table|column/i", $pass)){
            die("bad query");
        }
        $con = @mysql_connect("10.1.0.137", "yetanothersql", "yetanothersql");
        @mysql_select_db("yetanothersql", $con);
        $query = "SELECT * FROM user WHERE dkfHDdladkPWfde='{$pass}' AND dkfHDdladkID='{$user}';";
        $R = @mysql_fetch_array(mysql_query($query));
        if(mysql_error()){
            echo mysql_error();
        }else{
            if($R['dkfHDdladkPWfde'] == "SexyQutieLove"){
                echo "Yes!! you're stypr..";
            }else{
				echo "No.. you are not stypr..";
            }
        }
    }
?>
			<form action="?p=Login" method="GET">
				ID : <input type="text" name="username" maxlength="20"><br/>
				PW : <input type="password" name="password" maxlength="20"><br/>
				<input type="submit" value="Login" style="width:210">
			</form>
			Login please...
            <br/>
            <pre style="display:none;">
                ./index.php.bak
                ** thanks to adm1nkyj! **
                The flag solely consists of uppercase and lowercase alphbets.
            </pre>
		</center>
	</body>
</html>
