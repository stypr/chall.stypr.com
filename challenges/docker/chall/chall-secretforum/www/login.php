<?php
@session_start();

$username = $_POST[ 'username' ];
$password = $_POST[ 'password' ];

if (isset($username, $password)) {
	$allowed_list = array(
		"admin" => md5(chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255))), 
		"root" => md5(chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255))), 
		"hacker" => md5(chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255)))
	); 
	if (!preg_match('/^[a-z]{1,15}$/', $username)) die('<script>alert("login failed..");location.href = "./?page=login";</script>');
	if (!isset($password)) die('<script>alert("login failed...");location.href = ./?page=login";</script>');
	if ($allowed_list[ $username ] == $password) {
		$_SESSION[ 'id' ] = $username;
		die('<script>alert("login ok");location.href = "./";</script>');
	}
	else die('<script>alert("login failed..");location.href = "./?page=login";</script>');
}
?>