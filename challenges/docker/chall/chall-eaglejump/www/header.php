<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>Welcome to Eagle Jump</title>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link href="//bootstrapk.com/examples/justified-nav/justified-nav.css" rel="stylesheet">
	<script src="//bootstrapk.com/assets/js/ie-emulation-modes-warning.js"></script>
</head>

<body>
<div class="container">
	<div class="masthead">
		<a class="btn btn-default pull-right" href='/?p=login'>Login</a>
		<h3 class="text-muted">Eagle Jump</h3>
		<nav>
		<ul class="nav nav-justified">
			<li <?php if(!$_GET['p'] || $_GET['p'] == 'home') echo 'class ="active"';?>><a href="/">Home</a></li>
			<li <?php if($_GET['p'] == 'intro') echo 'class ="active"';?>><a href="/?p=intro">INTRO</a></li>
			<li <?php if($_GET['p'] == 'business') echo 'class ="active"';?>><a href="/?p=business">BUSINESS</a></li>
			<li <?php if($_GET['p'] == 'diary') echo 'class ="active"';?>><a href="/?p=diary">DIARY</a></li>
			<li <?php if($_GET['p'] == 'contact') echo 'class ="active"';?>><a href="/?p=contact">CONTACT</a></li>
		</ul>
		</nav>
	</div>
<hr>
