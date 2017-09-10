<?php

	/* Basic Initialization
	This file MUST be initialized from the very beginning of every pages. */

	ini_set("display_errors", "off");
	error_reporting(0);
	require("exclude/config.php");

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
?>