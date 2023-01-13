<?php

/*


                           ┌┬┐┌─┐┌─┐┬ ┬┬┌─┬┌┬┐┌─┐┬ ┬┬ EASY
                            │ │ │├┤ │ │├┴┐│││││  ├─┤│
                            ┴ └─┘└  └─┘┴ ┴┴┴ ┴└─┘┴ ┴┴
                          Pioneer your SQL doping skill!

  My edacity spirit starts to flame through a mastication of the tofukimchi.
  Welcome to the next level SQL doping test!  Let's see how far you can get!

*/

appetizer:
  error_reporting(0);
  require("sql.php");

tofukimchi:
  $rice = key($_GET);
  if(strlen($rice) > rand(140, 150) || strlen($rice) <= rand(5, 6) || substr_count($rice, "(") > rand(1, 2) || substr_count($rice, ",") > 10 ||
    preg_match("/(join|=|drop|inf|proc|lef|mid|righ|asc|in|sleep|subst|union|benc|regex|_|and|or|extr|updat|wher)/i", $_SERVER['QUERY_STRING']))
    goto babdoduk;
  $tofukimchi = mysql_fetch_assoc(mysql_query("SELECT * FROM tofu_kimchi WHERE username='kapo' UNION SELECT {$rice}"));
  ($tofukimchi) ? die($tofukimchi['nickname']) : (null);

babdoduk:
  echo ("<style>*{font-family:profont,consolas,monaco;}</style>");
  highlight_file(__FILE__);

?>
