<?php

// TODO: view-source:http://kaeruct.github.io/examples/yt-js-api/
$playrand = rand(0, 11); // 0 ~ n-1

if($_SERVER['QUERY_STRING'] === 'background'){
	$seconds_to_cache = 3600;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
	header("Expires: $ts");
	header("Pragma: cache");
	header("Cache-Control: max-age=$seconds_to_cache");
	header("Content-Type:image/jpeg");
	readfile("./static/background/background".rand(1,10).".jpg");
	exit;
}

define("__INIT_LOAD__", True);
require("init.php");

$stat = @get_stat();
$mem = @explode("/", $stat[0]);
$mem = ($mem[0] / $mem[1]) * 100;
$cpu = $stat[1];
$disk = @explode("/", $stat[2]);
$disk = ($disk[0] / $disk[1]) * 100;
$traffic = explode("/", $stat[3]);

?><!--
	This website is not affiliated with the EAGLEJUMP Corporation(https://www.eagle-jump.jp/).
	Respective copyrights apply to this website. Please contact administrator for more information.

	This website is something like an anime fan-page. Don't be so serious about the company.
	Image copyrights: 得能正太郎・芳文社／NEW GAME!!製作委員会 (http://www.dokidokivisual.com/, http://newgame-anime.com/)
-->
<!doctype html>
<html prefix="og: http://ogp.me/ns#" lang="en">
<head>
	<title>EAGLEJUMP Sandbox</title>
	<meta charset=utf-8>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="google" content="notranslate">
	<meta property="og:title" content="EAGLEJUMP Sandbox">
	<meta property="og:url" content="https://eagle-jump.org/">
	<meta property="og:image" content="https://eagle-jump.org/static/logo.png">
	<meta property="og:description" content="株式会社EAGLEJUMPのサンドボックスを ご紹介します。">
	<meta name=description content="株式会社EAGLEJUMPのサンドボックスを ご紹介します。">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon-precomposed" href="favicon.ico">
	<link href="/static/stylesheet/eaglejump.css?20190901" rel="stylesheet" type="text/css">
	<!--[if lt IE 9]><script src="/static/javascript/html5.js"></script><![endif]-->
</head>
<body>
	<div class="now-loading" id="loader"><div><img src="/static/picture/loading.gif"></div></div>
	<div class="opener">
		<div class="cover"></div>
		<div class="cover-cover"></div>
		<header>
			<div class="logo"></div>
			<div class="description">株式会社EAGLEJUMPのサンドボックスを紹介します。</div>
			<div class="status">
				<table align="center">
					<tr>
						<td rowspan=2><img alt="network in/out" src="/static/picture/stat-net.png"></td>
						<td><?php echo $traffic[1]; ?></td>
						<td rowspan=2><img src="/static/picture/stat-disk.png"></td>
						<td><?php echo substr(round($disk, 2), 0, 5); ?>%</td>
						<td rowspan=2><img src="/static/picture/stat-mem.png"></td>
						<td><?php echo substr(round($mem, 2), 0, 5); ?>%</td>
						<td rowspan=2><img src="/static/picture/stat-proc.png"></td>
						<td><?php echo substr(round($cpu, 2), 0, 5); ?>%</td>
					</tr>
					<tr>
						<td><?php echo $traffic[0]; ?></td>
						<td><div class="stat disk"><span style="width:<?php echo $disk; ?>%"></div></div></td>
						<td><div class="stat mem"><span style="width:<?php echo $mem; ?>%"></div></div></td>
						<td><div class="stat cpu"><span style="width:<?php echo $cpu; ?>%"></div></div></td>
					</tr>
				</table>
			</div>
		</header>
	</div>
	<div class="container">
        <div class="category">
            <div class="title">お知らせ</div>
            <div class="subtitle">Noticeboard</div>
            <div class="content">
                <div id="noticeboard" class="noticeboard">
                    <b>[2022-04-18 16:22+9GMT]</b> <i>@stypr</i>: Server is finally back up after 2 years.. Services may be unstable due to migrations.<br>
                    <b>[2020-01-26 02:02+9GMT]</b> <i>@stypr</i>: The frontend for this page has been fixed due to some minor issues. All servers are up again.<br>
                    <b>[2019-11-18 10:24+9GMT]</b> <i>@stypr</i>: The server is very unstable due to lack of internal resources. Some services might be a bit unstable for a while.<br>
                    <b>[2019-09-01 13:53+9GMT]</b> <i>@stypr</i>: Server maintainence is scheduled between 8th and 12th September. There can be a severe disruption.<br>
                    <b>[2018-11-11 12:25+9GMT]</b> <i>@stypr</i>: There was a configuration problem in the server and now guesser challenge should work well. Thanks to debukuk for the pointing it out.<br>
                    <b>[2018-08-17 23:11+9GMT]</b> <i>@stypr</i>: Server will be under maintanence starting from 18th August.<br>
                    <b>[2018-05-16 17:17+9GMT]</b> <i>@stypr</i>: Fixed bugs in SQLSandbox, PHPTrick, and hidden+. Please <a href="mailto:root@stypr.com">mail me</a> if the server seems not working.<br>
                    <b>[2018-05-03 20:12+9GMT]</b> <i>@stypr</i>: hiddenplus is back alive! Thanks for reporting this! Note that this challenge refreshes every hour.<br>
                    <b>[2017-12-28 18:00+9GMT]</b> <i>@stypr</i>: Yearly updates are done. Sorry for any inconvenience caused.<br>
                    <b>[2017-09-30 16:04+9GMT]</b> <i>@stypr</i>: Another error was found and fixed in smartie. Thanks to Cernica for reporting the issue.<br>
                    <b>[2017-09-06 18:47+9GMT]</b> <i>@stypr</i>: eagle-jump challenge has been deployed.<br>
                    <b>[2017-05-06 19:03+9GMT]</b> <i>@stypr</i>: New design has been applied to the status page.<br>
                </div>
            </div>
		</div>
		<div class="category">
			<div class="title">ネットワークステータス </div>
			<div class="subtitle">Network Status</div>
			<div class="content">
				<!-- すべてのサービスは正常に稼働しています。 -->
<?php
	// $vm = ['network' => ['instance_name' => 'ip']] //
	$networks = array_keys($vm);
	for($i=0; $i<count($networks); $i++){
		$network_name = $networks[$i];
?>
				<div class="network">
					<div class="name"><?php echo $network_name; ?></div>
<?php
		$instances = array_keys($vm[$network_name]);
		for($j=0; $j<count($instances); $j++){
			$instance_name = $instances[$j];
			$instance_info = $vm[$network_name][$instance_name];
			$instance_type = $instance_info[0];
			$instance_ip = $instance_info[1];
			$instance_status = $instance_info[2];  //ping($instance_ip);
            if($instance_status == true){
    			$instance_status = "alive";
	    		$instance_status_jp = "ふつう";
		    	$instance_status_desc = "Normal";
            }else{
    			$instance_status = "dead";
	    		$instance_status_jp = "ビジー";
		    	$instance_status_desc = "Busy";
            }
?>
					<div class="service">
						<div class="service-name"><?php echo $instance_name; ?></div>
						<div class="service-status <?php echo $instance_type; ?>-<?php echo $instance_status; ?>"></div>
						<div class="service-description"><?php echo $instance_status_jp; ?></div>
						<div class="service-description-desc"><?php echo $instance_status_desc; ?></div>
					</div>
<?php
		}
?>
				</div>
<?php } ?>
			</div>
		</div>
	</div>
	<div class="sayonara">
		<footer>
			<p class="left">Designed and developed with &hearts; by stypr. Respective copyrights apply.</p>
			<p class="right">
				<a href="mailto:root&#64;stypr.com">Contact</a> &middot;
				<a href="//harold.kim/donate/">Donate</a> &middot;
				<a href="http://newgame-anime.com/">NEWGAME!アニメ</a>
			</p>
		</footer>
	</div>
	<script src="/static/javascript/jquery.js"></script>
	<script src="/static/javascript/jquery-snowfall.js"></script>
	<script src="/static/javascript/jquery-loader.js"></script>
	<script src="/static/javascript/eaglejump.js"></script>
</body>
</html>
