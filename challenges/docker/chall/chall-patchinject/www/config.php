<?php

define("__FLAG__", "flag{4b533ca14fe527e223e5b85a962451c1}");

$db = @mysql_connect('10.1.0.137', 'patchinject', 'patchinject');
if(!$db) die("Server Down :(");
@mysql_select_db("patchinject");

?>
