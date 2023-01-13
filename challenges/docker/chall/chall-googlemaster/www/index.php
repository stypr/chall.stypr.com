<?php

function gethostbyaddr_timeout($ip, $dns, $timeout=1000)
{
    // random transaction number (for routers etc to get the reply back)
    $data = rand(0, 99);
    // trim it to 2 bytes
    $data = substr($data, 0, 2);
    // request header
    $data .= "\1\0\0\1\0\0\0\0\0\0";
    // split IP up
    $bits = explode(".", $ip);
    // error checking
    if (count($bits) != 4) return "ERROR";
    // there is probably a better way to do this bit...
    // loop through each segment
    for ($x=3; $x>=0; $x--)
    {
        // needs a byte to indicate the length of each segment of the request
        switch (strlen($bits[$x]))
        {
            case 1: // 1 byte long segment
                $data .= "\1"; break;
            case 2: // 2 byte long segment
                $data .= "\2"; break;
            case 3: // 3 byte long segment
                $data .= "\3"; break;
            default: // segment is too big, invalid IP
                return "INVALID";
        }
        // and the segment itself
        $data .= $bits[$x];
    }
    // and the final bit of the request
    $data .= "\7in-addr\4arpa\0\0\x0C\0\1";
    // create UDP socket
    $handle = @fsockopen("udp://$dns", 53);
    // send our request (and store request size so we can cheat later)
    $requestsize=@fwrite($handle, $data);

    @socket_set_timeout($handle, $timeout - $timeout%1000, $timeout%1000);
    // hope we get a reply
    $response = @fread($handle, 1000);
    @fclose($handle);
    if ($response == "")
        return $ip;
    // find the response type
    $type = @unpack("s", substr($response, $requestsize+2));
    if ($type[1] == 0x0C00)  // answer
    {
        // set up our variables
        $host="";
        $len = 0;
        // set our pointer at the beginning of the hostname
        // uses the request size from earlier rather than work it out
        $position=$requestsize+12;
        // reconstruct hostname
        do
        {
            // get segment size
            $len = unpack("c", substr($response, $position));
            // null terminated string, so length 0 = finished
            if ($len[1] == 0)
                // return the hostname, without the trailing .
                return substr($host, 0, strlen($host) -1);
            // add segment to our host
            $host .= substr($response, $position+1, $len[1]) . ".";
            // move pointer on to the next segment
            $position += $len[1] + 1;
        }
        while ($len != 0);
        // error - return the hostname we constructed (without the . on the end)
        return $ip;
    }
    return $ip;
}

session_name("request_session");
session_start();
error_reporting(0);

function smtp_mail($server){
    $context = stream_context_create([
	    'ssl' => [
	        'verify_peer' => false,
	        'verify_peer_name' => false
	    ]
    ]);

    // check if ipv6
    if(filter_var($server, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
	$server = "[" . $server . "]";
	die("IPv6 not acceptible");
    }
    $recipients = explode(',', $to);
    $user = 'flag';
    $pass = 'flag{afa563f5592af1a29c082f544c1bd4c7}';
    $smtp_host = "tls://".$server;
    $smtp_port = 465;

    echo "[+] Sending a mail request to an IT administrator..\n";
    if (!($socket = stream_socket_client($smtp_host . ":" . $smtp_port, $errno, $errstr, 3, STREAM_CLIENT_CONNECT, $context))){
        echo("[-] Socket Error.. Check your Connections.. (ssl://admin.google.com:465)");
        return 0;
    }

    server_parse($socket, '220');
    fwrite($socket, 'EHLO '.$smtp_host."\r\n");
    server_parse($socket, '250');
    fwrite($socket, 'AUTH LOGIN'."\r\n");
    server_parse($socket, '334');
    fwrite($socket, base64_encode($user)."\r\n");
    server_parse($socket, '334');
    fwrite($socket, base64_encode($pass)."\r\n");
    server_parse($socket, '235');
    fwrite($socket, 'MAIL FROM: <'.$user.'>'."\r\n");
    server_parse($socket, '250');
    fwrite($socket, 'RCPT TO: <stypr7@gmail.com>'."\r\n");
    server_parse($socket, '250');
    fwrite($socket, 'DATA'."\r\n");
    server_parse($socket, '354');
    fwrite($socket, "Subject: About the service issue..\r\nTo: <stypr7@gmail.com>\r\n\r\n\r\nThere is a lot of problems on this SMTP service. do you know what is the probleme? Thank you!\r\n");
    fwrite($socket, '.'."\r\n");
    server_parse($socket, '250');
    fwrite($socket, 'QUIT'."\r\n");
    fclose($socket);
        echo("[*] The request has been sent to an IT administrator.");
    return true;
}

function server_parse($socket, $expected_response)
{
    $server_response = '';
    while (substr($server_response, 3, 1) != ' '){
        if (!($server_response = fgets($socket, 256))){
                        echo("[-] Critical Error.. Maybe server problem?");
                        exit;
        }
    }

    if (!(substr($server_response, 0, 3) == $expected_response)){
                        echo("[-] Critical Error.. Maybe server problem?");
                        exit;
    }
}?>
<!DOCTYPE html>
<html lang="en">
        <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>ptms@google</title>
                <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
                <style>
                                /*
                                 * Globals
                                 */

                                /* Links */
                                a,
                                a:focus,
                                a:hover {
                                  color: #fff;
                                }

                                /* Custom default button */
                                .btn-default,
                                .btn-default:hover,
                                .btn-default:focus {
                                  color: #333;
                                  text-shadow: none; /* Prevent inheritence from `body` */
                                  background-color: #fff;
                                  border: 1px solid #fff;
                                }


                                /*
                                 * Base structure
                                 */

                                html,
                                body {
                                  height: 100%;
                                  background-color: #333;
                                }
                                body {
                                  color: #fff;
                                  text-align: center;
                                  text-shadow: 0 1px 3px rgba(0,0,0,.5);
                                }

                                /* Extra markup and styles for table-esque vertical and horizontal centering */
                                .site-wrapper {
                                  display: table;
                                  width: 100%;
                                  height: 100%; /* For at least Firefox */
                                  min-height: 100%;
                                  -webkit-box-shadow: inset 0 0 100px rgba(0,0,0,.5);
                                                  box-shadow: inset 0 0 100px rgba(0,0,0,.5);
                                }
                                .site-wrapper-inner {
                                  display: table-cell;
                                  vertical-align: top;
                                }
                                .cover-container {
                                  margin-right: auto;
                                  margin-left: auto;
                                }

                                /* Padding for spacing */
                                .inner {
                                  padding: 30px;
                                }


                                /*
                                 * Header
                                 */
                                .masthead-brand {
                                  margin-top: 10px;
                                  margin-bottom: 10px;
                                }

                                .masthead-nav > li {
                                  display: inline-block;
                                }
                                .masthead-nav > li + li {
                                  margin-left: 20px;
                                }
                                .masthead-nav > li > a {
                                  padding-right: 0;
                                  padding-left: 0;
                                  font-size: 16px;
                                  font-weight: bold;
                                  color: #fff; /* IE8 proofing */
                                  color: rgba(255,255,255,.75);
                                  border-bottom: 2px solid transparent;
                                }
                                .masthead-nav > li > a:hover,
                                .masthead-nav > li > a:focus {
                                  background-color: transparent;
                                  border-bottom-color: #a9a9a9;
                                  border-bottom-color: rgba(255,255,255,.25);
                                }
                                .masthead-nav > .active > a,
                                .masthead-nav > .active > a:hover,
                                .masthead-nav > .active > a:focus {
                                  color: #fff;
                                  border-bottom-color: #fff;
                                }

                                @media (min-width: 768px) {
                                  .masthead-brand {
                                        float: left;
                                  }
                                  .masthead-nav {
                                        float: right;
                                  }
                                }


                                /*
                                 * Cover
                                 */

                                .cover {
                                  padding: 0 20px;
                                }
                                .cover .btn-lg {
                                  padding: 10px 20px;
                                  font-weight: bold;
                                }


                                /*
                                 * Footer
                                 */

                                .mastfoot {
                                  color: #999; /* IE8 proofing */
                                  color: rgba(255,255,255,.5);
                                }


                                /*
                                 * Affix and center
                                 */

                                @media (min-width: 768px) {
                                  /* Pull out the header and footer */
                                  .masthead {
                                        position: fixed;
                                        top: 0;
                                  }
                                  .mastfoot {
                                        position: fixed;
                                        bottom: 0;
                                  }
                                  /* Start the vertical centering */
                                  .site-wrapper-inner {
                                        vertical-align: middle;
                                  }
                                  /* Handle the widths */
                                  .masthead,
                                  .mastfoot,
                                  .cover-container {
                                        width: 100%; /* Must be percentage or pixels for horizontal alignment */
                                  }
                                }
                </style>
        </head>

  <body>

    <div class="site-wrapper">
      <div class="site-wrapper-inner">
        <div class="cover-container">
          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">admin@stypr</h3>
              <ul class="nav masthead-nav">
                <li class="active"><a href="#">Manage</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Logout</a></li>
              </ul>
            </div>
          </div>
          <div class="inner cover">

<?php
//var_dump($_SERVER);
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['HTTP_CF_PSEUDO_IPV4'];
// $ip = $_SERVER['HTTP_CF_PSEUDO_IPV4'];
// var_dump($ip);

// $hostname = @gethostbyaddr($ip);
$hostname = @gethostbyaddr_timeout($ip, "1.0.0.1");
if($hostname == "ERROR"){
	$hostname = @gethostbyaddr_timeout($ip, "1.1.1.1");
}

if($hostname != $ip && $hostname == "admin.google.com" && $hostname === "admin.google.com"){
                if(isset($_GET['flag']) && $_SERVER['QUERY_STRING'] == "flag"){
                        echo "<pre>";
                        smtp_mail($ip);
                        echo "</pre>";
                }
?>
                          <p class="lead">
                                  <code>Click "Send Request" to send a issue to an administrator at admin.google.com.</code>
                          </p>
                          <p class="lead">
                                  <a href="#" class="btn btn-lg btn-default" onclick="document.location.href='?flag';">Send Request</a>
                          </p>
<?php
		goto footer;
}
?>
                          <p class="lead"><code>Sorry, You should be from "admin.google.com" in order to get requests.</code></p>
                          <p class="lead">
                                  <a href="#" class="btn btn-lg btn-default disabled">Send Request</a>
                          </p>
<?php

footer:
?>

          </div>

          <div class="mastfoot">
            <div class="inner">
              <p>Copyleft &copy; 2014 stypr.<a href="#hint: this is not a web challenge..think about osi layer :)"></a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
