<?php

$db = @mysql_connect('10.1.0.137', 'tofukimchi', 'tofukimchi');
if(!$db) die("Server Down :(");

@mysql_select_db("tofukimchi");

?>
