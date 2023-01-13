<?php


// Just to make sure that this challenge becomes much harder.. (Fixed Unintended Solution)
// Fixed it back to normal, the intended way didn't work because of PHP fixes I guess..
// Intended Solution in 2016: https://github.com/Qwaz/solved-hacking-problem/blob/master/sciencewar/2016/web_easy/index.php

ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

function error($s){
	return "<font color=red>$s</font>";
}

function ClassLoaderSandbox($c, $p1, $p2){
	$c = strtolower($c);
	$cl  = strlen(explode('"',$c)[0]);
	$p1l = strlen(explode('"',$p1)[0]);
	$p2l = strlen(explode('"',$p2)[0]);
	$classLoader = 'O:8:"stdClass":%size:{s:1:"c";s:'.$cl.':"'.$c.'";s:2:"p1";s:'.$p1l.':"'.$p1.'";s:2:"p2";s:'.$p2l.':"'.$p2.'";}';
	$sz = explode('{', $classLoader)[1];
	$sz = round((count(explode('"', $sz)) - 1) / 4);
	$classLoader = str_replace('%size', $sz, $classLoader);
	var_dump($classLoader);
	$classLoader = unserialize($classLoader);
	// For security reason, I'm going to enable reading only
	$classLoader->c = "finfo";
	$vulnerable = new $classLoader->c($classLoader->p1, $classLoader->p2);
	return $vulnerable;
}

?>
<!--
	This challenge is really similar to the challenge in websec.fr
	You might want to try the similar challenge in https://websec.fr/level12/index.php
-->
<!doctype html>
<html>
<head>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Exo+2:400,300">
	<title>RMT Sandbox</title>
	<style>body { font-family: "Exo 2", sans-serif; } </style>
</head>
<body>
<div class="container">
	<br><br>
	<img src="https://cdn.clien.net/web/api/file/F01/5359553/09ad98e5d1e4482e8cc.PNG?thumb=true" width=100%>
	<h1>Do you know RMT?</h1>
	<br>
	<hr>
	<pre>
<?php
	if (isset ($_POST['c']) && isset ($_POST['p1'])  && isset ($_POST['p2'])) {
		$result = ClassLoaderSandbox($_POST['c'], $_POST['p1'], $_POST['p2']);
		echo $result;
	}
?>
	</pre>
	<form method=POST>
	<hr>
		<pre>

&lt;?php
$vulnerable = new <input type='text' name='c' value='finfo' readonly="readonly"> ( <input type='text' name='p1'>, <input type='text' name='p2'> );
print($vulnerable);
?&gt;
		</pre>
		<br>
		<center>
			<button onclick="this.submit();" style="font-size:15px; text-decoration: underline; font-weight:bold; letter-spacing: 0.1px;">execute</button>
			Currently blocked classes for security reasons.. you can only use <code>finfo</code> class.
		</center>
	</form>
	<hr>
	<pre>
<?php highlight_file(__FILE__); ?>
	</pre>
</body>
</html>
