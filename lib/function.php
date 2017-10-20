<?php

/* lib/function.php
Global helper function */

function redirect($page) {
	// Redirect function: use at your own risk!
	header( "Location: /" . $page );
	exit;
}

function secure_hash(string $str): string {
	// (Hopefully) Secure hash
	return sha1( sha1( md5( $str ) ) . __HASH_SALT__ );
}

function generate_random_string(int $len = 40): string {
	// Generate CSPRNG random string
	$code = '';
	$_table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWYZabcdefghijklmnopqrstuvwxyz';
	$_table_len = strlen( $_table ) - 1;
	for ( $i=0; $i<$len; $i++ ) {
		$code .= $_table[random_int( 0,$_table_len )];
	}
	return $code;
}

function update_wechall(string $nickname = ""){
	// Remote update WeChall Profile
	global $query;
	$site = urlencode( __SITE_NAME__ );
	$nick = ($nickname) ? $query->filter($nickname, "sql") : $_SESSION['nickname'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.wechall.net/remoteupdate.php?sitename=$site&username=$nick");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function is_now_after( int $date ){
	// Used for CTF Mode
	return (time() >= date);
}

?>