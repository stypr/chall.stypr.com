<?php
error_reporting(0);

define("__DEBUG__", __FLAG__); // the flag is hidden here. find it lol

if($_GET['flag'] === __DEBUG__){
    echo "development server";
}else{
    echo "production server";
}

echo "<hr>";
show_source("index.php");
?>
