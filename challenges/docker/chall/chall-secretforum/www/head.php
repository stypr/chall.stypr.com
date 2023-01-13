<?php
if (!defined('__DIR__')) die('');
?>
<!doctype html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title>HackSpace</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/earlyaccess/nanumgothic.css" />
		<link rel="stylesheet" type="text/css" href="./css/common.css">
		<link rel="stylesheet" type="text/css" href="./css/forum.read.css">
		<link rel="stylesheet" type="text/css" href="./css/table.css">
		<link rel="stylesheet" type="text/css" href="./css/style.css" />
	</head>
	<body>
		<div id="header">
			<div id="logo" class="font">
				<a href="/">HackSpace</a>
			</div>
			<div id="menu">
				<a href="./"<?php if (!$_GET[ 'page' ] || !in_array($_GET[ 'page' ], array('forum', 'write', 'view', 'contact', 'login', 'account'))) echo ' class="current_page_item'; ?>">Main</a><a href="javascript:alert('hack the site!');">ReadMe</a><a href="./?page=forum"<?php if (in_array($_GET[ 'page' ], array('forum', 'write', 'view'))) echo ' class="current_page_item"'; ?>>Forum</a><a href="javascript:alert('under construction..');">Join</a><a href="./?<?php echo $_SESSION[ 'id' ] ? 'action=logout' : 'page=login'; ?>"><?php echo $_SESSION[ 'id' ] ? 'Logout' : 'Login'; ?></a>
			</div>
			<div class="bound"></div>
		</div>