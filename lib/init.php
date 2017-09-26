<?php

	/* Basic Initialization
	This file MUST be initialized from the very beginning of every pages. */

	// hide errors
	ini_set("display_errors", "off");
	error_reporting(0);

	// load config, once.
	require_once("exclude/config.php");

	// thx to madbat2
	session_set_cookie_params(3600 * 48);
	session_cache_expire(3600 * 48);
	ini_set("session.gc_maxlifetime", 3600 * 48);
	session_name("chall");
	session_start();
	assert(strlen(__HASH_SALT__) >= 60) || die("Your service is insecure!");

	// init query
	$query = new Query();
	$query->connect(__DB_HOST__, __DB_USER__, __DB_PASS__, __DB_BASE__);
	if($query->check() == false) die("SQL server's down :(");

	// basic stuff..
	function redirect($page){
		header("Location: /".$page);
		exit;
	}
	function secure_hash(string $str): string {
		return sha1(sha1(md5($str)) . __HASH_SALT__);
	}

	// simple method to return error.
	function return_error() {
		http_response_code(404);
		$template = new Template();
		$template->include("error");
	}

	// fatal catcher
	function shutdown_function(){
		global $query;
		$error = error_get_last();
		unset($query);
		// fatal error, E_ERROR === 1
		if ($error['type'] === E_ERROR){
			//var_dump($error);
			return_error();
		}
    }
    register_shutdown_function('shutdown_function');

?>