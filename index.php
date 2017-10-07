<?php
	/* Index page */

init:
	require_once("lib/template.php");
	require_once("lib/query.php");
	require_once("lib/init.php");
	require_once("lib/helper.php");
	// check installation
	if(__INSTALL__ !== true) die("The service is not installed yet.");
	if(!file_exists("vendor/autoload.php")) die("You need to install composer dependencies.");
	require_once("vendor/autoload.php");
	require_once("lib/model.php");
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
