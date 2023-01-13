<?php
ini_set("display_errors", "on");
error_reporting(7);
@session_start();
define('__DIR__', dirname(__FILE__));
define('__SECRET_DIR__', 'th1s_pag3_i5_the_secr3t123ayyaahsyeahswagajkasd');

require_once __DIR__.'/config.php';
require_once __DIR__.'/'.__SECRET_DIR__.'/secret.php';

require_once __DIR__.'/security.php';
require_once __DIR__.'/mysql-class.php';

$db = new MySQL(
	$config[ 'user' ],
	$config[ 'pass'],
	$config[ 'db' ],
	$config[ 'host' ]
);

if (isset($_GET[ 'action' ]) &&
	($_GET[ 'action' ] == 'logout') &&
	(!isset($_POST[ 'username' ], $_POST[ 'password' ]))) {
	session_destroy();
	die('<script>location.href = "./";</script>');
}

foreach ($_POST as $_POST1 => $_POST2)
	$_POST[ $_POST1 ] = secureContent($_POST2);

require_once __DIR__.'/head.php';

$pagesOK = array('forum', 'write', 'view', 'contact', 'login', 'account');

if (isset($_GET[ 'page' ]) && in_array($_GET[ 'page' ], $pagesOK))
	include_once __DIR__.'/'.__SECRET_DIR__.'/'.$_GET[ 'page' ].'.php';
else {
	include_once __DIR__.'/'.__SECRET_DIR__.'/main.php';
}

require_once __DIR__.'/footer.php';
?>
