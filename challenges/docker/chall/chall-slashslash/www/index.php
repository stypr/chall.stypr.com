<?php

libxml_disable_entity_loader(false);
// load document
$xml = new DOMDocument();
$xml->load("DATABASE-CHALL-STYPR-13137.xml", LIBXML_NOWARNING);
$_GET['id'] = ($_GET['id']) ? ($_GET['id']) : ("1");

// fetch username and comment
$xpath = new Domxpath($xml);

$query = $xpath->query('//user[@id="'.$_GET['id'].'"]/username');
$username =  $query[0]->textContent;
$query = $xpath->query('//user[@id="'.$_GET['id'].'"]/comment');
$comment = $query[0]->textContent;
if($username && $comment){
	$valid = true;
}

// strlen
if(strlen($_GET['id']) > 15){
	$username = "** no hack **";
	$comment = "** no hack **";
}

// filter
if(strpos($username, "you_cant_guess_me") !== false){
	$username = "** filtered **";
	$comment = "** filtered **";
}
if(strpos($username, "are_you_sure") !== false){
	$username = "** filtered **";
	$comment = "** filtered ** ";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>//</title>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<style>html{position:relative;min-height:100%;}body{margin-bottom:60px;}.footer{position:absolute;bottom:0;width:100%;height:60px;background-color:#f5f5f5;}.container{width:auto;max-width:840px;padding:0 15px;}.container .text-muted{margin:20px 0;}</style>
</head>
<body>
<div class="container">
<div class="page-header">
<h1>// &middot; The slash and the slash!</h1>
</div>
    <p class="lead">Yo, this slash bruh rhymes good. can you slash to crash the script? <a href="?id=2">admin</a></p>
<pre>
Connecting to the server...
Fetching the query...
------------
<?php if($valid){ ?>
username: <?php echo $username; ?>

password: ** secret **
comment: <?php echo $comment; ?>

<?php }else{ ?>
Could not fetch the query.
<?php } ?>
------------
Closing the query...
</pre>
</div>
<div class="footer">
<div class="container">
<p class="text-muted">Copyleft &copy; 2015 stypr.</p>
</div>
</div>
    </body>
</html>
