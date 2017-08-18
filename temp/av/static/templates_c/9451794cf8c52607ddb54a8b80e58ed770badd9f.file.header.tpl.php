<?php /* Smarty version Smarty-3.1-DEV, created on 2017-08-18 16:57:32
         compiled from "/data/wwwroot/tbers.com/temp/av/tpl/public/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70747308359967ebd682564-86118972%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9451794cf8c52607ddb54a8b80e58ed770badd9f' => 
    array (
      0 => '/data/wwwroot/tbers.com/temp/av/tpl/public/header.tpl',
      1 => 1503046495,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70747308359967ebd682564-86118972',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_59967ebd687441_56552637',
  'variables' => 
  array (
    'title' => 0,
    'actresses_names' => 0,
    'actresses_name' => 0,
    'categories0' => 0,
    '_categories0' => 0,
    'categories1' => 0,
    '_categories1' => 0,
    'categories2' => 0,
    '_categories2' => 0,
    'hotVideos' => 0,
    'hotVideo' => 0,
    'qwe' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59967ebd687441_56552637')) {function content_59967ebd687441_56552637($_smarty_tpl) {?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gbk" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/style.css">

    <!-- Owl Carousel Assets -->
    <link href="../../owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="../../owl-carousel/owl.theme.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link rel="stylesheet" href="../../font-awesome-4.4.0/css/font-awesome.min.css" type="text/css">

    <!-- jQuery -->
    <script src="../../js/jquery-2.1.1.js"></script>

    <!-- Core JavaScript Files -->
    <script src="../../js/bootstrap.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<header>
    <!--Top-->
    <nav id="top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <strong>欢迎来到今日首发 海外永久域名 shoufa888.com</strong>
                </div>
                <div class="col-md-6 col-sm-6">
                    <ul class="list-inline top-link link">
                        <li><a href="/index"><i class="fa fa-home"></i> 首页</a></li>
                        <li><a href="./contact"><i class="fa fa-comments"></i> 找到我们</a></li>
                        <li><a href="javascript:alert('暂未开放,敬请期待!')"><i class="fa fa-question-circle"></i> 发车指南</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!--Navigation-->
    <nav id="menu" class="navbar">
        <div class="container">
            <div class="navbar-header"><span id="heading" class="visible-xs">菜单</span>
                <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/index"><i class="fa fa-home"></i> 首发</a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 个人中心</a>
                        <div class="dropdown-menu">
                            <div class="dropdown-inner">
                                <ul class="list-unstyled">
                                    <li><a href="javascript:alert('暂未开放,敬请期待!')">登录</a></li>
                                    <li><a href="javascript:alert('暂未开放,敬请期待!')">注册</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-play-circle-o"></i> 女优推荐</a>
                        <div class="dropdown-menu">
                            <div class="dropdown-inner">
                                <ul id="actresses_names" class="list-unstyled">
                                    <?php  $_smarty_tpl->tpl_vars['actresses_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['actresses_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['actresses_names']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['actresses_name']->key => $_smarty_tpl->tpl_vars['actresses_name']->value){
$_smarty_tpl->tpl_vars['actresses_name']->_loop = true;
?>
                                        <li><a href="./archive?key=<?php echo $_smarty_tpl->tpl_vars['actresses_name']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['actresses_name']->value;?>
</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-list"></i> 口味</a>
                        <div class="dropdown-menu" style="margin-left: -203.625px;">
                            <div class="dropdown-inner">

                                <ul id="categories0" class="list-unstyled">
                                    <?php  $_smarty_tpl->tpl_vars['_categories0'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_categories0']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories0']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_categories0']->key => $_smarty_tpl->tpl_vars['_categories0']->value){
$_smarty_tpl->tpl_vars['_categories0']->_loop = true;
?>
                                        <li><a href="archive.html?chid=<?php echo $_smarty_tpl->tpl_vars['_categories0']->value['CHID'];?>
"><?php echo $_smarty_tpl->tpl_vars['_categories0']->value['name'];?>
</a></li>
                                    <?php } ?>
                                </ul>
                                <ul id="categories1" class="list-unstyled">
                                    <?php  $_smarty_tpl->tpl_vars['_categories1'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_categories1']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories1']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_categories1']->key => $_smarty_tpl->tpl_vars['_categories1']->value){
$_smarty_tpl->tpl_vars['_categories1']->_loop = true;
?>
                                        <li><a href="archive.html?chid=<?php echo $_smarty_tpl->tpl_vars['_categories1']->value['CHID'];?>
"><?php echo $_smarty_tpl->tpl_vars['_categories1']->value['name'];?>
</a></li>
                                    <?php } ?>
                                </ul>
                                <ul id="categories2" class="list-unstyled">
                                    <?php  $_smarty_tpl->tpl_vars['_categories2'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_categories2']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories2']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_categories2']->key => $_smarty_tpl->tpl_vars['_categories2']->value){
$_smarty_tpl->tpl_vars['_categories2']->_loop = true;
?>
                                        <li><a href="archive.html?chid=<?php echo $_smarty_tpl->tpl_vars['_categories2']->value['CHID'];?>
"><?php echo $_smarty_tpl->tpl_vars['_categories2']->value['name'];?>
</a></li>
                                    <?php } ?>
                                </ul>

                            </div>
                        </div>
                    </li>
                    <li><a href="javascript:alert('暂未开放,敬请期待!')"><i class="fa fa-cubes"></i> 中文字幕</a></li>
                    <li><a href="../contact.html"><i class="fa fa-envelope"></i>找到我们</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="header-slide">
        <div id="owl-demo" class="owl-carousel">
            <?php  $_smarty_tpl->tpl_vars['hotVideo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hotVideo']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotVideos']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hotVideo']->key => $_smarty_tpl->tpl_vars['hotVideo']->value){
$_smarty_tpl->tpl_vars['hotVideo']->_loop = true;
?>
                <div class="item">
                    <div class="zoom-container">
                        <div class="zoom-caption">
                            <span><?php echo $_smarty_tpl->tpl_vars['hotVideo']->value['keyword'];?>
</span>
                            <a href="/single?v=<?php echo $_smarty_tpl->tpl_vars['hotVideo']->value['vid'];?>
">
                                <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                            </a>
                            <p><?php echo $_smarty_tpl->tpl_vars['hotVideo']->value['title'];?>
</p>
                        </div>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['hotVideo']->value['preview_url'];?>
" />
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</header>
<?php echo $_smarty_tpl->tpl_vars['qwe']->value;?>
<?php }} ?>