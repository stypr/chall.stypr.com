<?php
	/*
	function shutDownFunction() {
	    $error = error_get_last();
	    // fatal error, E_ERROR === 1
	    if ($error['type'] === E_ERROR) {
			echo "OK!";
			var_dump($error);
			exit;
	    }
	}
	register_shutdown_function('shutDownFunction');
	//*/
	/* Index page */

	require("lib/query.php");
	require("lib/init.php");
	require("lib/model.php");
	require("lib/helper.php");
	require("lib/controller.php");

	// index.php?module=user&action=login
	$module_list = ["user", "challenge", "status"];

	try{
		$controller_val = (string)$_GET['controller'];
		$action_val = (string)$_GET['action'];

		$controller = ucfirst($controller_val) . "Controller";
		$action = ucfirst($action_val). "Action";

		if(in_array($controller_val, $module_list, true)){
			$controller = new $controller;
			$controller->$action();
		}else{
			goto err;
		}
	}catch(Exception $e){ goto err; }
	exit;

err:
	die("wtf");

?>