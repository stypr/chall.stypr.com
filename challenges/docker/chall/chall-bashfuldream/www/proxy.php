<?php
/* Side Note: stypr is not illuminati */
error_reporting(0);
header("Content-Type: text/html;");
$host = gethostname();

// what is my IP?
$fp = @fsockopen("ssl://stypr.com", 443, $errno, $errstr, 1);
if(!$fp){ echo ":( server dead."; exit; }else {
    $out = "GET /ip.php HTTP/1.1\r\n";
    $out .= "Host: stypr.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    $out = "";
    while (!feof($fp)) {
        $out .= fgets($fp, 128);
    }
    $ip = explode("\n", $out);
    $ip = $ip[count($ip)-1];
    fclose($fp);
}

if(isset($_GET['page'])){
  // hoping that nothing can be pwned beyond this stage..
  $page = preg_replace('/[^\x20-\x7E]/','', $_GET['page']);
  $page = preg_replace("/\s/ui", "", $page);
  $page = preg_replace("/[^\w@,.;*?$\/\-{}]/", "", $page);
  $page = preg_replace("'/((file|http|ftp|https):\/\/)?[\w-]+(\.[\w-]+)+([\w.,@^=%&;:\/~+#-]*[\w@^=%&\/~+#-])?/'", "", $page);
  $page = str_replace("flag", "illuminati", $page);

  // f**king filter only for our illuminati members.
  if(strlen($page) > 33) goto fuck; // Because triangle(3 angles, 3 sides) and above is not illuminati. :)
  if(substr_count($page,"$") > 4) goto fuck;
  if(substr_count($page,";") > 3) goto fuck;
  if(substr_count($page,"*") > 2) goto fuck;
  if(substr_count($page,"/") > 1) goto fuck;
  if(substr_count($page,"=") > 1) goto fuck;
  $page = (filter_var($page, FILTER_VALIDATE_URL) === false) ? $page : null;
  if($page){
    $result = str_replace("sh: ", "", str_replace("cat: ", "", shell_exec("cd /srv;wget -qO - " . $page . " 2>&1")));
    if(strstr(substr($result,0,255), "No such file or directory")) header("HTTP/1.0 404 Not Found");
    $result = str_replace($ip, "3.3.3.3", $result);
    $result = str_replace($host, "3.3.3.3", $result);
    // dreams won't come true
    echo (strrpos($result, "html>") <= 0) ? "<pre>".$result : $result;
  }else{
fuck:
    echo "<font color=red>Error</font><br />";
  }
  exit;
}
?>
