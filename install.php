<?php

	/* Installer that might not work..
	You won't get stuck unless you put weird inputs.. because it's vulnerable.. 
	This is never tested so please do it at your own risk. 
	*/

	error_reporting(0);
	ignore_user_abort(true);

	function secure_hash(string $str, string $salt): string {
		return sha1(sha1(md5($str)) . $salt);
	}
	function generate_random_string($len=40){
		//generate csprng random string 
		$code = '';
		$_table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZabcdefghijklmnopqrstuvwxyz';
		$_table_len = strlen($_table) - 1;
		for ($i=0; $i<$len; $i++){
			$code .= $_table[random_int(0,$_table_len)];
		}
	}
	function check_system(){
		/*
			Check if all dependencies are installed
		*/
		$os = (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") ? "win": "unix";
		$build = PHP_VERSION_ID;
		$build_possible = ($build > 70000); // php 7.0.0 or higher
		@exec("composer show", $composer);
		$composer_exist = (strpos($composer[0], "phpmailer/phpmailer") !== false);
		$is_installed = @file_exists("./lib/exclude/config.php");
		$is_writable = is_writable("./lib/exclude/config.php");
		$is_writable = is_writable("./install.php") && $is_writable;
		$mysqli_exist = function_exists("mysqli_connect");
		$curl_exist = function_exists("curl_setopt");
		return ['os' => $os,
			'php' => $build,
			'php-composer' => $composer_exist,
			'php-mysqli' => $mysqli_exist,
			'php-curl' => $curl_exist,
			'writable' => $is_writable,
			'installed' => $is_installed];
	}
	function install(){
		// 0. generate random salt
		$salt = generate_ransom_string();

		// 1. install db
		$query = mysqli_connect($_POST['mysql-hostname'], $_POST['mysql-username'], $_POST['mysql-password'], $_POST['mysql-database']);
		if(!$query){
			$error =  mysqli_connect_error();
			echo json_encode(['type' => 'error', 'reason' => $error]);
			exit;
		}

		// 1-1. install table
		mysqli_query($query, "CREATE TABLE IF NOT EXISTS `chal` (".
			"`challenge_id` mediumint(9) NOT NULL AUTO_INCREMENT,".
			"  `challenge_name` varchar(100) DEFAULT NULL,".
			"  `challenge_desc` text DEFAULT NULL,".
			"  `challenge_score` mediumint(9) DEFAULT NULL,".
			"  `challenge_flag` varchar(255) DEFAULT NULL,".
			"  `challenge_rate` float NOT NULL DEFAULT 0,".
			"  `challenge_solve_count` mediumint(9) DEFAULT 0,".
			"  `challenge_is_open` tinyint(1) DEFAULT 0,".
			"  `challenge_by` varchar(255) DEFAULT 'stypr',".
			"  PRIMARY KEY (`challenge_id`)".
			") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		mysqli_query($query, "CREATE TABLE IF NOT EXISTS `log` (".
			"  `log_no` int(255) NOT NULL AUTO_INCREMENT,".
			"  `log_id` varchar(100) DEFAULT NULL,".
			"  `log_challenge` varchar(255) DEFAULT NULL,".
			"  `log_type` varchar(64) DEFAULT NULL,".
			"  `log_date` datetime DEFAULT NULL,".
			"  `log_info` varchar(512) DEFAULT NULL,".
			"  PRIMARY KEY (`log_no`)".
			") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		mysqli_query($query, "CREATE TABLE IF NOT EXISTS `user` (".
			"  `user_no` mediumint(9) NOT NULL AUTO_INCREMENT,".
			"  `user_id` varchar(100) NOT NULL,".
			"  `user_pw` varchar(40) NOT NULL,".
			"  `user_nickname` varchar(100) NOT NULL,".
			"  `user_score` int(10) unsigned NOT NULL DEFAULT 0,".
			"  `user_join_date` datetime NOT NULL,".
			"  `user_auth_date` datetime DEFAULT NULL,".
			"  `user_join_ip` varchar(15) NOT NULL,".
			"  `user_auth_ip` varchar(15) DEFAULT NULL,".
			"  `user_last_solved` datetime DEFAULT NULL,".
			"  `user_comment` varchar(255) DEFAULT NULL,".
			"  `user_permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT 0,".
			"  PRIMARY KEY (`user_no`)".
			") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		// make username/password/db
		$u = mysqli_real_escape_string($query, $_POST['username']);
		$p = secure_hash($_POST['password'], $salt);
		$n = mysqli_real_escape_string($query, $_POST['nickname']);
		mysqli_query($query, "INSERT INTO user VALUES(NULL, '$u', '$p', '$n', 0, NOW(), NULL, '0', NULL, NULL, NULL, 9);");

		// 2. install config
		$data  = '<?php ';
		$data .= 'define("__INSTALL__", true);';
		$data .= 'define("__HASH_SALT__", "' . $salt . '");';
		// wechall
		$data .= 'define("__WECHALL__", "'.$_POST['wechall-id'].'");';
		$data .= 'define("__DB_HOST__", "'.$_POST['mysql-hostname'].'");';
		$data .= 'define("__DB_USER__", "'.$_POST['mysql-username'].'");';
		$data .= 'define("__DB_PASS__", "'.$_POST['mysql-password'].'");';
		$data .= 'define("__DB_BASE__", "'.$_POST['mysql-database'].'");';
		$data .= 'define("__TEMPLATE__", __DIR__ . "/../../template/");';
		$data .= 'define("__GMAIL_USER__", "' . $_POST['gmail-username'] . '");';
		$data .= 'define("__GMAIL_PASS__", "' . $_POST['gmail-password'] . '");';
		$data .= 'define("__HOST__", "' . $_POST['base-url']. '");';
		$f = fopen('./lib/exclude/config.php', 'wb');
		fwrite($f, $data); fclose($f);
		// check if file is on the right place.
		if(!is_file("./lib/exclude/config.php")){
			echo json_encode(['type' => 'error', 'reason' => 'service not installed']);
			exit;
		}
		@unlink("install.php");
		echo json_encode(['type' => 'success', 'reason' => 'good to go']);
		exit;
	}
	$check = check_system();
	if($check['installed']){ die("You already installed. why do you even have this file?"); }
	if($_POST){
		@install();
		exit;
	}
	$check = json_encode($check);
?>
<!doctype html>
<html prefix="og: http://ogp.me/ns#" lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="google" content="notranslate">
		<meta name="robots" content="noindex, nofollow">
		<title>Installer</title>
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/octicons/4.4.0/font/octicons.css">
		<link rel="stylesheet" type="text/css" href="//unpkg.com/primer-css@9.4.0/build/build.css">
		<link rel="stylesheet" type="text/css" href="./static/stylesheet/main.css">
	</head>
	<body>
		<div id="container" class="container-xl clearfix px-3 pt-3 pb-4 mt-4">
			<div class="col-12 selector-language mb-0">
				<div id="out"></div>
				<div id="opt"></div>
			</div>
		</div>
		<script src="//unpkg.com/jquery@3.2.1/dist/jquery.js"></script>
		<script>
			var SERVER_STATUS = <?php echo $check; ?>

			var check_string = function(str, min, max){
				if(!min) min=5; if(!max) max=30;
				var _regexp = '^[a-zA-Z0-9-_!@$.%^&*()가-힣]{'+min+','+max+'}$';
				var _check = new RegExp(_regexp).test(str);
				return _check;
			}

			function act_install(){
				var input = {};
				$("#out").addClass("flash");
				$.each($('form').serializeArray(), function(i, field) { 
					input[field.name] = field.value; });

				if(!check_string(input['username'], 5, 100)){ 
					$("#out").addClass("flash-error");
					$('#out').html('Username is invalid.<br>' +
						'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{8, 100}$</pre>');
					return false;
				}
				if(!check_string(input['nickname'], 3, 20)){
					$('#out').html('Nickname is invalid.<br>' +
						'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{3, 20}$</pre>');
					return false;
				}
				if(!check_string(input['password'], 4, 100)){
					$('#out').html('Password is invalid.<br>' +
						'<pre>RegExp: ^[a-zA-Z0-9-_!@$.%^&*()가-힣]{4, 100}$</pre>');
					return false;
				}
				$.post("?", input, function(d){
					location.replace("/");
				});
				return false;
			}

			function main(){
				if(SERVER_STATUS['php'] < 70000){
					$("#out").addClass("flash");
					$("#out").addClass("flash-error");
					$("#out").html("<span class='octicon octicon-info'></span> Your server-side language is too outdated. Please update and try again.");
					return;
				}
				if(SERVER_STATUS['php-composer'] != true){
					$("#out").addClass("flash");
					$("#out").addClass("flash-error");
					$("#out").html("<span class='octicon octicon-info'></span> You've not installed either the composer or its dependencies.<br>" +
						"Please <a href='https://getcomposer.org/download/'>download</a> composer and run <code>composer install</code>.");
					return;
				}
				if(SERVER_STATUS['php-mysqli'] != true){
					$("#out").addClass("flash");
					$("#out").addClass("flash-error");
					$("#out").html("<span class='octicon octicon-info'></span> <br> Your server settings disabled the php-mysqli module." +
						"Please add mysqli module from your settings and try again.");
					return;
				}
				if(SERVER_STATUS['php-curl'] != true){
					$("#out").addClass("flash");
					$("#out").addClass("flash-error");
					$("#out").html("<span class='octicon octicon-info'></span> <br> Your server settings disabled the php-curl module." +
						"Please add mysqli module from your settings and try again.");
					return;
				}
				if(SERVER_STATUS['writable'] != true){
					$("#out").addClass("flash");
					$("#out").addClass("flash-error");
					$("#out").html("<span class='octicon octicon-info'></span> <br> The scripts need to have write perm with appropriate user perm set." +
						"Please check appropriate settings and come back again.");
					return;
				}
				$("#opt").html('<h2 class="setup-form-title mb-3">Simple Auto Installer (hopefully)</h2><h3>Yo, no fancy stuff here. Just write all details below.</h3><hr>'+
				'<form onsubmit="return act_install();">'+
				'<dl class="form-group"><dt class="input-label">'+

				'<label autocapitalize="off" autofocus="autofocus" for="username">MySQL Host</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" required  id="mysql-hostname" name="mysql-hostname" size="30" type="text" />'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" autofocus="autofocus" for="username">MySQL User</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" required  id="mysql-username" name="mysql-username" size="30" type="text" />'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" autofocus="autofocus" for="username">MySQL Pass</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" required  id="mysql-password" name="mysql-password" size="30" type="password" />'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" autofocus="autofocus" for="username">MySQL DB Name</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" required  id="mysql-database" name="mysql-database" size="30" type="text" />'+
				'</dd></dl><hr>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" autofocus="autofocus" for="username">Your Username</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" autofocus="autofocus" class="form-control" required  id="username" name="username" size="30" type="email" />'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="nickname">Your Nickname</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="nickname" size="30" type="text" id="nickname">'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">Your Password</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="password" size="30" type="password" id="password">'+
				'</dd></dl><hr>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">Your GMail Username for mailing purposes. (<a href="https://stackoverflow.com/questions/20337040/gmail-smtp-debug-error-please-log-in-via-your-web-browser">click here for troubleshooting</a>)</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="gmail-username" size="30" type="text" id="gmail-username">'+
				'</dd></dl>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">Your GMail Password</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="gmail-password" size="30" type="password" id="gmail-password">'+
				'</dd></dl><hr>'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">Your WeChall ID (Dont need it unless you have one)</label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="wechall-id" size="30" type="text" id="webchall-id">'+
				'<dl class="form-group"><dt class="input-label">'+
				'<label autocapitalize="off" for="password">Base URL (eg. https://chall.stypr.com/) </label>'+
				'</dt><dd>'+
				'<input autocapitalize="off" class="form-control" required  name="base-url" size="30" type="text" id="base-url">'+
				'<div id="output-message" class="mb-2" ></div>'+
				'<input type="submit" class="btn btn-primary" id="signup_button" value="Install">'+
				'</form>');
			}
			$(document).ready(main);
		</script>
	</body>
</html>