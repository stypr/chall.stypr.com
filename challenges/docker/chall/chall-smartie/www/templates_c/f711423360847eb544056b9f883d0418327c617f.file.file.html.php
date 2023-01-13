<?php /* Smarty version Smarty-3.1.12, created on 2015-01-24 02:30:21
         compiled from "file.html" */ ?>
<?php /*%%SmartyHeaderCode:311652555dbd0ce291-22275019%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f711423360847eb544056b9f883d0418327c617f' => 
    array (
      0 => 'file.html',
      1 => 1411051818,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '311652555dbd0ce291-22275019',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52555dbd114578_72995593',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52555dbd114578_72995593')) {function content_52555dbd114578_72995593($_smarty_tpl) {?>

    <div class="container">

      <div class="row">

        <div class="col-lg-12">
          <h1 class="page-header">파일함<small>파일 업로드와 다운로드</small></h1>
          <ol class="breadcrumb">
            <li><a href="index.html">홈</a></li>
            <li class="active">파일함</li>
          </ol>
        </div>

      </div>
 
      <div class="row">

        <div class="col-lg-12">
   </div>
   <form  enctype="multipart/form-data" action="upload.php" method="POST">
  <div class="form-group">
    <label for="exampleInputFile">File upload</label>
    <input type="file" id="filename"><br>
	<button type="submit" class="btn btn-default">Submit</button>
  </div>
  </form>
        </div>

      </div>

    </div>
<?php }} ?>