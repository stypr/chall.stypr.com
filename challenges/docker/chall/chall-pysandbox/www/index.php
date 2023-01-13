<?php
error_reporting(0);
/*
 	this should be the method of calling in DOS applications... :)
*/
if(1 === 1){
	if($_REQUEST['query']){
		// filter queries here..
		$query = $_REQUEST['query'];
		if(strlen($query) >= 300){ $result = Array(0 => "[*] You can only send maximum 300 characters at once."); goto breakexec; }
		if(preg_match("/(file|open|conf|misc|index|limit|perl|position|where|desc|len|regexp|like|anal|tuple|python|hacking|dev|lib|misc|mnt|net|proc|root|var|tmp|boot|etc|echo|jsp|asp|txt|insert|log|interval|ddate|addtime|aes_decrypt|aes_encrypt|ascii|asc|asin|atan|avg|between|cast|ceil|char|case|chr|collation|concat|conv|connection|cos|cot|count|crc32|curdate|date|time|database|day|decode|default|div|elt|encode|encry|exp|extract|field|floor|format|find_in_set|found_rows|from_unixtime|hex|hour|if|inet|instr|is|left|right|mid|load|local|log|lpad|ltrim|vmatch|max|md5|microsecond|max|min|-|minute|mod|not|null|oct|password|pi|pow|power|quote|rand|repeat|replace|reverse|rlike|sha|rpad|trim|sounds|space|sin|schema|sort|std|strcmp|subdate|substr|sum|uncompress|upper|xor|year|version|des|bit|from|=|<|>|@|\[|subclass|information|while|test|by|\|&|\|-)/im",strtolower(trim($query)))){
			$result = Array(0 => "[*] Sandbox denied your request. No hacking man lol");
			goto breakexec;
		}
		// launch..!
		if(strrpos($query, "#!") === false){ $query = "#!/usr/bin/python\n" . $query; $debug = true;}
		$filename = "/tmp/" . sha1(md5(time())). rand(100000,999999) . ".py";
		$handle = fopen($filename, "w");
		fwrite($handle, $query);

		fclose($handle);
		chmod($filename, octdec(750));
		exec($filename . " 2>&1", $result);
		unlink($filename);
	}
breakexec:
?>
<!-- flag is in the /srv/flag.php -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PYSandbox</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
      <style>
                        html {
                          position: relative;
                          min-height: 100%;
                        }
                        body {
                          margin-bottom: 60px;
                        }
                        .footer {
                          position: absolute;
                                 bottom: 0;
                          width: 100%;
                          height: 60px;
                          background-color: #f5f5f5;
                        }

                        body > .container {
                          padding: 60px 15px 0;
                        }
                        .container .text-muted {
                                 margin: 20px 0;
                        }

                        .footer > .container {
                          padding-right: 15px;
                          padding-left: 15px;
                        }

                        code {
                          font-size: 80%;
                                                }
        </style>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
                         <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">pYThon S@ndb0x</a>
        </div>
                           <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Evaluate</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
                <div class="page-header">
                	<h1>Evaluate our new PYSandbox!</h1>
                </div>
                <p class="lead">
			Our team is going to develop an online IDE for python2 development. However, Our team has found a vulnerability that could compromise the entire system.<br>
			We've somehow replaced my python with Py binary for the linux. Now it's your time to bypass the cross-compiling shit.<br><br>
			Report to the administrator if you have found the hidden vulnerability. Thank you for your cooperation. :)
                </p>
                <hr>
                <div class="row">
                        <form method="POST">
                             <div class="col-md-6"><textarea name="query" value="" placeholder="Enter your payload.." style="width:100%; height:100px; max-height:100px; min-height:100px; min-width:100%; max-width:100%;" maxlength=200 class="form-control"></textarea>
				<hr><p align=right><input class="btn btn-primary" type="submit" value="SYSTEM()"></div></p>
                        </form>
                        <pre class="col-md-6">
<?php
if($result){
	if($debug == true){
		for($i=2;$i<12;$i++){
			print(htmlspecialchars($result[$i]) . "\n");
		}
	}else{
		for($i=0;$i<10;$i++){
			print(htmlspecialchars($result[$i]) . "\n");
		}
	}
}
?>
                        </pre>
                </div>
    </div>

    <div class="footer">
      <div class="container">
        <p class="text-muted">Copyleft &copy; 2015 stypr.</p>
      </div>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
                     </html>
<?php
}else{
	system("id");
}
?>
