<?php

	function is_sha1($str) {
		return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
	}
	function rrmdir($dir) {
		@exec("rm -rf $dir",$nullpointer);
		return true;
	}

	// generate tmp
	$tmp = "/tmp/lameass/";
	if(!is_dir($tmp)){
		@mkdir($tmp);
	}
	$param = @explode('&', $_SERVER['QUERY_STRING']);
	if($_GET){
		if($param[0] === "download" && is_sha1($param[1])){
			if(is_dir($tmp . $param[1])){
				$filename = $tmp. $param[1]. "/archive.zip";
				if(!is_file($filename) || !file_exists($filename)){
					echo "./archive.zip not found!";
					exit;
				}
				$fd = fread(fopen($filename,"rb"), filesize($filename));
				$fs = strlen($fd);
				header('Content-Description: Secure Transfer');
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename=archive.zip');
				header('Expires: 1');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . $fs);
				// download file
				@rrmdir($tmp.$param[1]."/");
				die($fd);
			}else{
				echo "hash not found.";
			}
			exit;
		}
	}

	if($_POST){
		if($param[0] !== "archive"){
			die("nope");
		}
		$filename = [];
		$content = [];

		// check data
		for($i=1;$i<=3;$i++){
			$tmpf = $_POST['f' . $i];
			$tmpc = $_POST['c' . $i];
			/*
				https://www.owasp.org/index.php/Testing_for_Command_Injection_(OTG-INPVAL-013)
				The following special character can be used for command injection such as |  ; & $ > < ` \ !
			*/
			if(!is_string($tmpf) || !is_string($tmpc) ||
				stripos($tmpf, "/") !== false || stripos($tmpf, "\\") !== false ||
				stripos($tmpf, "localhost") !== false || stripos($tmpf, ":") !== false ||
				stripos($tmpf, "<") !== false || stripos($tmpf, "&") !== false ||
				stripos($tmpf, "#") !== false || stripos($tmpf, "$") !== false |
				stripos($tmpf, ">") !== false || stripos($tmpf, "|") !== false ||
				stripos($tmpf, ";") !== false || stripos($tmpf, "\\") !== false ||
				stripos($tmpf, "!") !== false || stripos($tmpf, "(") !== false ||
				stripos($tmpf, ")") !== false || stripos($tmpf, "'") !== false ||
				stripos($tmpf, "\"") !== false ||
				$tmpf == "" || $tmpc == "" ||
				strlen($tmpc >= 1024) || strlen($tmpc >= 255)){
					die(json_encode(["type"=> "error", "reason" => "no hack :)"]));
			}
			$filename[] = $tmpf;
			$content[] = $tmpc;
		}
		$rand = sha1(rand(1000,9999) . time(). "**STEREOTYPED_LAMEASS_LIBRARY**" . time(). rand(1, rand(100000,999999)));
		$_tmp = $tmp . $rand . "/";
		mkdir($_tmp);

		// upload data
		for($i=0;$i<3;$i++){
			$fp = fopen($_tmp . $filename[$i], "a+");
			fwrite($fp, $content[$i]);
			fclose($fp);
		}

		// zip data
		@system('cd '.$_tmp.';zip -r archive.zip * >/dev/null');
		// return value
		die(json_encode(["type"=> "success", "reason" => $rand]));
	}
?>
<!-- pwned ... now we blocked everything based on OTG-INPVAL-013 -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>LameassLibrary</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<style>html{position:relative;min-height:100%;}body{margin-bottom:60px;}.footer{position:absolute;bottom:0;width:100%;height:60px;background-color:#f5f5f5;}.container{width:auto;max-width:840px;padding:0 15px;}.container .text-muted{margin:20px 0;}</style>
</head>
<body>
<div class="container">
<div class="page-header">
<h1>LameassLibrary on Sale!</h1>
</div>
    <p class="lead">
		<div class="alert alert-danger">
			We develop broken&amp;lame applications. Our mission is to make world-wide corporations bankrupt!<br>
			<p align=right><i>&mdash; CEO, Stereotyped Company.</i></p>
		</div>
		<center>
			<p>
				Our development team is new to PHP, but don't worry about the efficiency; everything is processed with an external application.<br>
				We have the sample service below, test our great service and get back to us.<br>
			</p>
			<p>
				Usage: Put three files with contents, we will zip your records and provide you the download link.
			</p>
		</center>
		<br>
		<form onsubmit="return run(); return false;">
			<div class="row">
				<div class="col-md-4">
					<label>Filename:</label>
					<input type="text" class="form-control" id="file-1"  placeholder="a.txt">
					<small class="form-text text-muted">Content</small>
					<textarea class="form-control" id="content-1" rows="10" ></textarea>
				</div>
				<div class="col-md-4">
					<label>Filename:</label>
					<input type="text" class="form-control" id="file-2"  placeholder="b.txt">
					<small class="form-text text-muted">Content</small>
					<textarea class="form-control" id="content-2" rows="10"></textarea>
				</div>
				<div class="col-md-4">
					<label>Filename:</label>
					<input type="text" class="form-control" id="file-3"  placeholder="c.txt">
					<small class="form-text text-muted">Content</small>
					<textarea class="form-control" id="content-3" rows="10"></textarea>
				</div>
			</div>
			<div class="row">
				<br>
				<div class="col-md-6">
					<button type="submit" class="btn btn-primary">run()</button>
				</div>
				<div class="col-md-6">
					<p align=right id="result">..</p>
				</div>
			</div>
	</form>
</pre>
</div>
<div class="footer">
<div class="container">
<p class="text-muted">Copyleft &copy; 2016 stypr.</p>
</div>
</div>
	<script src="//code.jquery.com/jquery-3.2.1.min.js"
	integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	crossorigin="anonymous"></script>
	<script>
		function run(){
			for(var i=1;i<=3;i++){
				if(!$("#file-"+i).val() || !$("#content-"+i).val()){
					alert('You need to enter all inputs available on the form.');
					return false;
				}
			}
			var submit_data = {'f1': $("#file-1").val(), 'f2': $("#file-2").val(), 'f3': $("#file-3").val(),
								  'c1': $("#content-1").val(), 'c2': $("#content-2").val(), 'c3': $("#content-3").val(),}
			$.post('?archive', submit_data, function(data){
				var result = JSON.parse(data);

				if(result['type'] == "success"){
					$("#result").html("Saved. <a href='?download&" + result['reason'] + "'>" + result['reason'].substring(30) + "</a>");
				}else{
					$("#result").html("Error found during the process.");
				}
			});
			return false;
		}
	</script>
</body>
</html>
