<?php
if (!defined('__DIR__')) die('');

if (!$_SESSION[ 'id' ]) die('<script>alert("login plz!");location.href = "./?page=login";</script>');
if ($_POST[ 'title' ] && $_POST[ 'content' ]) {
	$get_num = $db->numRows($db->dbQuery("SELECT * FROM `simple_board`;"));
	$get_num = $get_num + 1;

	$db->dbQuery("INSERT INTO `simple_board` VALUES('".$get_num."', '".$_SESSION[ 'id' ]."','".$_SERVER[ 'HTTP_CF_CONNECTING_IP' ]."', '".$_POST[ 'title' ]."', '".$_POST[ 'content' ]."', '0', '', '0', now(), '0');");
	die('<script type=\'text/javascript\'>location.href = \'./?page=view&no='.$get_num.'\';</script>');
}

require_once __DIR__.'/write.html';
?>
