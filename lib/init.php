<?php

/* lib/init.php
Init/Load configurations, estabilish sql connection */

// Hide Errors: debug.php for error output //
ini_set( "display_errors", "off" );
error_reporting( 0 );

// Session Timeout (thx to madbat2) //
$sess_allowed_sec = 3600 * 48; // 2 days
ini_set( "session.gc_maxlifetime", $sess_allowed_sec );
session_set_cookie_params( $sess_allowed_sec );
session_cache_expire( $sess_allowed_sec );
session_name( "chall" );
session_start();

// Load Configuration and Test //
include_once( "exclude/config.php" );
if ( __INSTALL__ !== true ) die( "Not installed" );
assert( strlen( __HASH_SALT__ ) >= 60 ) || die( "Insecure Salt" );

// Initialize Query //
require_once( "query.php" );
$query = new Query();
$query->connect( __DB_HOST__, __DB_USER__, __DB_PASS__, __DB_BASE__ );
if($query->check() == false) die("MySQL Down");

?>