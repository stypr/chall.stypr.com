<?php
	/* Index page */

/*
<!doctype html>
<html prefix="og: http://ogp.me/ns#" lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="google" content="notranslate">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">
		<meta name="robots" content="index, nofollow">
		<meta name="keywords" content="stypr, chall.stypr.com, Stereotyped Challenges, Wargame, Exploitation, CyberSec, Security">
		<meta property="og:url" content="//chall.stypr.com">
		<meta property="og:title" content="Stereotyped Challenges">
		<meta property="og:description" content="Redefine your web hacking techniques today!">
		<meta property="og:image" content="//avatars3.githubusercontent.com/u/6625978?v=4&s=200">
		<title>Stereotyped Challenges</title>
		<link rel="icon" type="image/x-icon" href="favicon.ico">
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/octicons/4.4.0/font/octicons.css">
		<link rel="stylesheet" type="text/css" href="//unpkg.com/primer-css@9.4.0/build/build.css">
		<link rel="stylesheet" type="text/css" href="./static/stylesheet/main.css">
	</head>
	<body>
		<div id="container" class="container-xl clearfix px-3 pt-3 pb-4 mt-4">
			<div id="language" class="col-12 selector-language mb-0">
			<div class="right">
					<span class="octicon octicon-globe"></span>
					<select id="language-select">
						<option>en</option>
						<option>ko</option>
					</select>
				</div>
			</div>
			<div id="sidebar" class="col-3 float-left pr-3"></div>
			<div id="content" class="col-9 float-left pl-2"></div>
		</div>
		<div class="footer container-xl mb-4 ">Since 2014. Made with &hearts; by <a href="//harold.kim/">stypr</a>.</div>
	</div>
	<!-- TBD: Loader -->
	<script src="//unpkg.com/jquery@3.2.1/dist/jquery.js"></script>
	<script src="./static/javascript/lang.js"></script>
	<script src="./static/javascript/main.js"></script>
	</body>
</html>*/

init:

	require_once("lib/template.php");
	require_once("lib/query.php");
	require_once("lib/init.php");
	if(__INSTALL__ !== true) die("The service is not yet installed. Please read instructions.");
	require_once("vendor/autoload.php");
	require_once("lib/model.php");
	require_once("lib/helper.php");
	require_once("lib/controller.php");
	
loader:
	// index.php?module=user&action=login
	$module_list = ["user", "challenge", "status", "wechall", "default"];

	try{
		// type def and set default
		$controller_val = isset($_GET['controller']) ? (string)$_GET['controller'] : "default";
		$action_val = isset($_GET['action']) ? (string)$_GET['action'] : "default";

		// load class and method
		$controller = ucfirst($controller_val) . "Controller";
		$action = ucfirst($action_val). "Action";
		if(in_array($controller_val, $module_list, true)){
			$controller = new $controller;
			$controller->$action();
			exit;
		}
	}catch(Exception $e){ }

err:
	return_error();
?>
