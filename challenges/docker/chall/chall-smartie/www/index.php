<?php
error_reporting(0);
session_start();
if(!isset($_SESSION['user']))
{
	exit("<script>alert('login plz');location.href='./login.php';</script>");
}
?>
<!DOCTYPE html>
<html lang="kr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>intranet service</title>

    <link href="css/bootstrap.css" rel="stylesheet">

    <link href="css/modern-business.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <a class="navbar-brand" href="index.php">intranet</a>
        </div>

        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="?page=notice">notice</a></li>
            <li><a href="?page=services">services</a></li>
            <li><a href="?page=file">webhard</a></li>
            <li> <a href="?page=staff">staff</a></li>
			<li> <a href="logout.php">logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
    
<?php

error_reporting(0);
include_once './libs/Smarty.class.php';

class SmartyInclude
{
    protected $tpl = null;

    public $_POST = null;

    public $_SERVER = null;

    public $_GET = null;

    public $_REQUEST = null;

    public $_SESSION = null;

    function __construct($test)
    {
        global $_POST, $_GET, $_SERVER;

        $this->_POST = $_POST;
        $this->_GET = $_GET;
		$this->_SERVER = $_SERVER;
        $this->_REQUEST = $_REQUEST;
        $this->_SESSION = $_SESSION;
        $this->__iTpl();
    }

    function __iTpl()
    {
        $this->tpl = new Smarty();
    }


    function __display($file)
    {
        $this->tpl->display($file);
    }
}


class Apps_display extends SmartyInclude
{

    function displayOn($p)
    {
		$p=preg_replace('/\//i','',$p);
        $this->__display($p);
    }
}
$lang=$_GET['page'];
    switch($lang){
						case "notice":
							$lang='notice';
							break;
						case "services":
							$lang='services';
							break;
						case "file":
							$lang='file';
							break;
						case "staff":
							$lang='staff';
							break;
							case "logout":
							$lang='logout';
							break;
	}
if(!isset($lang)){ $lang='main'; }
$obj = new Apps_display();
$obj->__display($lang.".html");

?>

    <div class="container">

      <hr>

      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p>Copyright &copy; wowhacker 2014</p>
          </div>
        </div>
      </footer>

    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/modern-business.js"></script>
  </body>
</html>