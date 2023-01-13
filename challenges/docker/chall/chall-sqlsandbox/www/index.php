<?php

	// flag{9491af2651aa4b675b98259f221e1015}
	ini_set('display_errors', 'on');
	error_reporting(7);
	if(!is_dir("./tmp")){
		@mkdir("./tmp");
	}

	// block everything :)
	@session_save_path('./tmp');
	@session_name('sqlsandbox');
	@session_start();

	// autoclean php for security..
	@exec("ls -al ./tmp/*.php | wc -l", $sz);
	$sz = implode("", $sz);
	if($sz >= 2){
		@system("rm -rf ./tmp/ 2>/dev/null >/dev/null");
		@system("rm -rf /tmp/* 2>/dev/null >/dev/null");
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>SQLSandbox</title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="header">
<header class="navbar navbar-default navbar-fixed-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/" class="navbar-brand">SQLSandbox</a>
    </div>
    <nav class="collapse navbar-collapse" role="navigation">
      <ul class="nav navbar-nav">
        <li>
          <a href="./">Evaluate</a>
        </li>
        <li>
          <a href="#" onclick="alert('nope nub lolz');">Purchase</a>
        </li>
        <li>
          <a href="mailto:no-reply&#64;stypr.com">Contact</a>
        </li>
      </ul>
    </nav>
   </div>
</header>
<div style="margin-bottom:30px;"></div>
  <div class="container">
    <div class="page-header">
      <h1>MySQL Test Zone</h1>
    </div>
    <div id="row">
    <div class="col-md-12">
	<p class="lead">
		If you can't trust our SQLSandbox, you can evaluate/test in this page. You can inject your payloads here.<br>PS: We are now offering $500k for anyone who breaks into our box. :)
	</p>

	<div class="row">
		<div class="col-md-12">
			<form class="form" method="POST">
				<small for="query">Enter your payloads here:</small>
				<div class="row">
				<div class="col-md-9">
					<input type="text" class="form-control" name="query" value="<?php echo htmlspecialchars($_POST['query']); ?>">
				</div>
				<div class="col-md-3">
					<input type="submit" class="btn btn-primary right" value="query()" style="width:100%;">
					</div>
				</div>
			</form>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
		<div class="col-md-12">
		Your result comes here :-
	<pre>
<?php

$_POST['query'] = substr($_REQUEST['query'], 0, 75);
$q = @mysql_connect("localhost", "sqlsandbox", "sqlsandbox");
if(!$q){ die("wtf?!"); }
@mysql_select_db("sqlsandbox");

if(!$_POST['query']) goto bye;
// trollollol
if(preg_match("/(`|@|sys|kill|on|htaccess|htpasswd|conf|misc|limit|\-|\_|scan|open|rmdir|mkdir|perl|position|pol|table|desc|read|len|import|regexp|like|anal|tuple|python|hacking|dev|lib|misc|mnt|net|proc|root|var\/|boot|etc|usr|bin|echo|jsp|asp|txt|insert|log|interval|union|select|drop|update|challenge|abs|acos|coal|adddate|addtime|aes_decrypt|aes_encrypt|ascii|asc|asin|atan|avg|between|cast|ceil|char|case|chr|collation|concat|conv|connection|cos|cot|count|crc32|curdate|date|time|database|day|decode|default|div|elt|encode|encry|exp|extract|field|floor|format|find_in_set|found_rows|from_unixtime|print|hex|hour|if|inet|instr|is|left|right|mid|load|local|log|lpad|ltrim|match|max|md5|microsecond|max|min|-|minute|mod|not|null|oct|password|pi|pow|power|quote|rand|repeat|replace|reverse|rlike|sha|rpad|trim|sounds|space|sin|schema|sort|std|strcmp|subdate|substr|sum|uncompress|upper|xor|year|version|des|bin|bit|test|by|\|from|union|select|or|sleep|\|-|benchmark|exe|proc|\')/im", $_POST['query']) ||
	substr_count($_POST['query'], "(") > 2 || substr_count($_POST['query'], "\"") > 2 ){
	die("[*] No Hack :p");
}
$query = $_POST['query'];
$qq = @mysql_query("SELECT \"" . $query . "\" FROM sqlsandb0x");
if(!$qq){
	echo @substr(mysql_error(),0, 75);
}else{
	echo "QUERY() Successful.";
}
bye:
?>
	</pre>
</body>
</html>
