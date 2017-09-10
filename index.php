<?php

	require("lib/init.php");
	require("lib/model.php");
	require("lib/helper.php");
	require("lib/controller.php");

	// index.php?module=user&action=login
	$module_list = ["user", "challenge", "status"];


	try{
		$controller_val = (string)$_GET['controller'];
		$action_val = (string)$_GET['action'];

		$controller = ucfirst($action_val) . "Controller";
		$action = ucfirst($action_val). "Action";

		if in_array($controller_val, $module_list, true){
			$c = new $controller;
			try{
				$controller->$action();
			}catch($e){
				// todo
				die("wtf?");
			}
		}
?>