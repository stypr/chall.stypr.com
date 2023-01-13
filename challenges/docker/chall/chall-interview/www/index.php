<?php
	require("query.php");
	$protection = "__PROTECTION__";
	/**
		CREATE TABLE `cloud_db` (
			`id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`hash` VARCHAR(40) NOT NULL,
			`question` TEXT NOT NULL,
			`data` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COMMENT='Cloud database for Interview..'
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=2
		;
		INSERT INTO `cloud_db` (`id`, `hash`, `question`, `data`) VALUES (1, 'ecf910825550bb36f5602456f21bc4214c893193', 'What is Your Greatest Strength?', '000C0000789CED55C16E83300CEDA7F803265456951EA79DB69E77987634604A4448586C4AF9FB39749DAA69D24E6D278D27458063BF675B7178EF89C57897C8411617C2529165D98FF609ABCD225DA5CB74B952BF4DB46FD2F51A96974AE81C3D0B0695BC86D61FC46B8D0286E1CDF7019E02A1E879801709E476523FDC3ABD1917063A1E285C72FA7F9FFFEC3EFB3EFF5996A6F3FC5F015BA8714F40079DF896EC082CC1BB1D0CC188D12737C65A4EE019F7F173F0A1A1129001A1F0DD08541AF101AAB88C128D8481EFE093164F7428422EFE67403C9424682C0C353930A23C2D71B4B7E34936F922B0EC27A3864F22087B0C8664045F41D7E7D614187955533DB7D0383F40AD4BF9B8C68ECE583599D152DCA954566A7D456E009D16D497865C41093CC6D25AD432A71064367A429CC49A06ED05E49A557EA4A1AAA242B46C7BD4A0892BB604BA40CC10C81232697271A3EF4ABD5E61A05C6B76B121CA28356051F4018BF118ADFEC9AD4FC58C1933FE033E0094C9BAE4');
	**/

	// Ref. http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
	function rrmdir($dir) {
		@exec("rm -rf $dir",$nullpointer);
		return true;
	}

	// Ref. http://stackoverflow.com/questions/14674834/php-convert-string-to-hex-and-hex-to-string
	function strToHex($string){
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}
		return strToUpper($hex);
	}

	// generate tmp
	$tmp = "/tmp/tmp/";
	if(!is_dir($tmp)){
		@mkdir($tmp);
	}
	if($_POST){
		// Desc. generate tar.gz and upload to mysql for backing up the data.
		if(isset($_POST['q']) && isset($_POST['a'])){
			$rand = sha1(rand(1000,9999) . time(). "**SEED**" . time(). rand(1, rand(100000,999999)));
			$_tmp = $tmp . $rand . "/";

			$question = $_POST['q'];
			$answer = $_POST['a'];
			if(!is_string($question) || !is_string($answer) || strlen($question) >= 21000||
				preg_match("/(schema|@|proc|left|substr|mid|right|select|union)/i", $question)){
				echo json_encode(["type" => "error", "reason" => "Intruder detection :)"]);
				exit;
			}

			mkdir($_tmp);
			// generate file
			$fp = fopen($_tmp . "question.txt", "a+");
			fwrite($fp, $question);
			fclose($fp);
			$fp = fopen($_tmp . "answer.txt", "a+");
			fwrite($fp, $answer);
			fclose($fp);
			// generate phar
			$a = new PharData($_tmp . 'archive.tar');
			$a->addFile($_tmp. "question.txt" , "question.txt");
			$a->addFile($_tmp . "answer.txt" , "answer.txt");

			// *.tar
			$fp = fopen($_tmp . "archive.tar", "rb");
			$fp = strToHex(fread($fp, filesize($_tmp."archive.tar")));

			// *.tar -> *.tar.gz
			$query = new Query();
			$query->connect("10.1.0.137", "interview", "interview", "interview");
			if($query->check() == false) die("SQL server's down :(");

			$q = $query->query("INSERT INTO cloud_db VALUES(NULL, '".$rand."', \"" . $question . "\", HEX(COMPRESS(UNHEX('".$fp."'))))#");
			if($q !== True){
				// bugfix: if duplicate, reset the table.. (sorry users.. :p)
				if((stripos($q, 'Duplicate Entry') !== false && stripos($q, '18446744073709551615') !== false) ||
					(stripos($q, 'Failed to read auto-increment') !== false)){
					$query->query("DELETE FROM cloud_db WHERE id!=1");
					$query->query("ALTER TABLE cloud_db AUTO_INCREMENT=2");
				}
				$near = stripos($q, 'to use near \'') + strlen('to use near \'');
				echo json_encode(["type" => "error", "reason" => substr($q, $near, 0x50)]);
			}else{
				echo json_encode(["type" => "success", "hash" => $rand]);
			}
			unset($a);
			rrmdir($_tmp);
		}
		exit;
	}

	$q = $_SERVER['QUERY_STRING'];
	$q = explode("&", $q);

	if($q[0] == "read" && strlen($q[1]) == 40){
		$readmode = true;
		$hash = $q[1];
		$query = new Query();
		$query->connect("10.1.0.137", "interview", "interview", "interview");
		// get files from mysql query -> uncompress, etc.. -> read files by file

		// *.tar.gz - > *.tar
		$data = $query->query("SELECT id, hash, question, uncompress(unhex(data)) as uncompressed FROM cloud_db WHERE hash='". $query->filter($hash, "url") ."'", 1);
		if(!isset($data)) die("<script>alert('not found');history.go(-1);</script>");

		if($q[1] !== $data['hash'] || $data['hash'] == "" || ($query->filter($data['hash'], "url") !== $data['hash'])) die('<script>alert("no hack");history.go(-1);</script>');
		$question = htmlspecialchars($data['question']);
		$uncompressed = $data['uncompressed'];

		try{
			// *.tar -> files
			$rand = $data['hash'];
			$_tmp = $tmp . $rand . "/";
			//is_dir
			if(is_dir($_tmp)){
				@rrmdir($_tmp);
			}
			@mkdir($_tmp);
			$fp = fopen($_tmp."archive.tar", "a+");
			fwrite($fp, $uncompressed);
			fclose($fp);
			// because i'm da php noob
			@exec("tar xvf " . $_tmp . "archive.tar -C " . $_tmp, $null);
			@exec("cat " . $_tmp . "question.txt | head -c1024", $question);
			@exec("cat " . $_tmp . "answer.txt | head -c1024", $answer);
			// parse data
			$question = implode("\n", $question);
			$answer = implode("\n", $answer);

			if(stripos($answer, "__PROTECTION__") !== false || stripos($answer, "__PROTECTION__") !== false){
					$question = "**filtered**";
					$answer = "**filtered**";
			}
		}catch(Exception $e){
			$question="Invalid";
			$answer="Invalid";
		}
		@rrmdir($_tmp);
		$count = $query->query("SELECT COUNT(*) AS count FROM cloud_db", 1)['count'];
		if($count >= 500 || stripos($question, "flag{054c9") !== false || 
			stripos($answer, "flag{054c9") !== false ){
			/* Auto-delete on every 500 requests */
			$query->query("DELETE FROM cloud_db WHERE id!=1");
			$query->query("ALTER TABLE cloud_db AUTO_INCREMENT=2");
		}
	}
?>
<!-- /srv/admin.php -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Interview &middot; Write anywhere, anytime!</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<style>html{position:relative;min-height:100%;}body{margin-bottom:60px;}.footer{position:absolute;bottom:0;width:100%;height:60px;background-color:#f5f5f5;}.container{width:auto;max-width:840px;padding:0 15px;}.container .text-muted{margin:20px 0;}</style>
</head>
<body>
<div class="container">
<div class="page-header">
<h1>Interview &middot; The cloud resume service</h1>
</div>
    <p class="lead">
		<div class="alert alert-info">
			Holy cow, tomorrow is the deadline for the résumé and my files are deleted ARRRRGGGH!!<br>
			<p align=right><i>&mdash;  Patrick Star</i></p>
		</div>
		<center>
			It's time to say goodbye to these cancerous situations, I've now developed a new resume cloud service for all of you!<br>
			<i>Note: This is a beta test edition, server will automatically delete your files for every 500 submissions.</i><br>
			<br><a href="?read&ecf910825550bb36f5602456f21bc4214c893193">Read Sample</a></center>
	</p>
	<br>
<?php if($readmode){ ?>
	<form onsubmit="return false;">
		<div class="form-group">
			<label for="question">Question:</label>
			<input type="text" class="form-control" id="question"  value="<?php echo htmlspecialchars($question); ?>" disabled>
			<small id="question-help" class="form-text text-muted">You can write your question here.</small>
		</div>
		<div class="form-group">
			<label for="answer">Answer:</label>
			<textarea class="form-control" id="answer" rows="10" disabled><?php echo htmlspecialchars($answer); ?></textarea>
			<small id="word-count" class="form-text text-muted">Word Count: 0</small>
		</div>
		<br><br>
	</form>
<?php }else{ ?>
	<form onsubmit="return submit_data(); return false;">
		<div class="form-group">
			<label for="question">Question:</label>
			<input type="text" class="form-control" id="question"  placeholder="What attracted you to this company?">
			<small id="question-help" class="form-text text-muted">You can write your question here.</small>
		</div>
		<div class="form-group">
			<label for="answer">Answer:</label>
			<textarea class="form-control" id="answer" rows="10"></textarea>
			<small id="word-count" class="form-text text-muted">Word Count: 0</small>
		</div>
		<div class="row">
			<div class="col-md-6">
				<button type="submit" class="btn btn-primary">Save!</button>
			</div>
			<div class="col-md-6">
				<p align=right id="result">..</p>
			</div>
		</div>
		<br><br>
	</form>
<?php } ?>
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
		$("document").ready(function(){
			$("#answer").keyup(function(){
				var size = $("#answer").val().split(" ").length;
				$("#word-count").html("Word Count: " + size);
			});
<?php if($readmode) { ?>
			var size = $("#answer").val().split(" ").length;
			$("#word-count").html("Word Count: " + size);
<?php } ?>
		});
		function submit_data(){
			var question = $("#question").val();
			var answer = $("#answer").val();
			var submit_data = {'q' : question, 'a': answer}
			$.post('?send', submit_data, function(data){
				var result = JSON.parse(data);
				if(result['type'] == "success"){
					$("#result").html("Saved. <a href='?read&" + result['hash'] + "'>" + result['hash'].substring(30) + "</a>");
				}else{
					$("#result").html("Error found during the process.");
				}
			});
			return false;
		}
	</script>
 </body>
</html>
