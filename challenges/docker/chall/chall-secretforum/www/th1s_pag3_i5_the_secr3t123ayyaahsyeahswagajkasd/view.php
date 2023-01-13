<?php
if (!defined('__DIR__')) die('');

$_GET[ 'no' ] = secureInt($_GET[ 'no' ]);

if (!$_SESSION[ 'id' ]) die('<script>alert("login plz!");location.href = "./?page=forum";</script>');
$get_inf = mysql_fetch_array($db->dbQuery("SELECT `username`, `ip` FROM `simple_board` WHERE `idx` = '".$_GET[ 'no' ]."';"));
if ($_SERVER[ 'HTTP_CF_CONNECTING_IP' ] != $get_inf[ 'ip' ]) die('<script>alert("wrong ip!");location.href = "./?page=forum";</script>');

if ($_POST[ 'comm' ]) {
	$comment = mysql_fetch_array($db->dbQuery("SELECT `comment` FROM `simple_board` WHERE `idx` = '".$_GET[ 'no' ]."';"));
	$comment = $comment[ 0 ];

	$comment = $comment.sprintf('<div class="part"><a class="name">%s</a><small><time>%s</time></small><div class="content">%s</div></div>', $_SESSION[ 'id' ], date('Y-m-d H:i:s', time()), $_POST[ 'comm' ]);

	for ($i = 32; $i < 64; $i += 1) {
		echo chr($i);
	}
	$db->dbQuery("UPDATE `simple_board` SET `etc` = NOW(), `comment` = '".$comment."', `commenct_c` = `commenct_c` + 1 WHERE `idx` = '".$_GET[ 'no' ]."';");
}

$result = mysql_fetch_array($db->dbQuery("SELECT * FROM `simple_board` WHERE `idx` = '".$_GET[ 'no' ]."';"));
$result ? $db->dbQuery("UPDATE `simple_board` SET `hit` = `hit` + 1 WHERE `idx` = '".$_GET[ 'no' ]."';") : '';

require_once __DIR__.'/view.html';
?>
