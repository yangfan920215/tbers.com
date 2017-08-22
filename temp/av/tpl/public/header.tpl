<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="gbk" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="">
    <meta name="author" content="">

    <title><{$title}></title>

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
                                    <{foreach $actresses_names as $actresses_name}>
                                        <li><a href="./archive?key=<{$actresses_name}>"><{$actresses_name}></a></li>
                                    <{/foreach}>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-list"></i> 口味</a>
                        <div class="dropdown-menu" style="margin-left: -203.625px;">
                            <div class="dropdown-inner">

                                <ul id="categories0" class="list-unstyled">
                                    <{foreach $categories0 as $_categories0}>
                                        <li><a href="archive.html?chid=<{$_categories0.CHID}>"><{$_categories0.name}></a></li>
                                    <{/foreach}>
                                </ul>
                                <ul id="categories1" class="list-unstyled">
                                    <{foreach $categories1 as $_categories1}>
                                        <li><a href="archive.html?chid=<{$_categories1.CHID}>"><{$_categories1.name}></a></li>
                                    <{/foreach}>
                                </ul>
                                <ul id="categories2" class="list-unstyled">
                                    <{foreach $categories2 as $_categories2}>
                                        <li><a href="archive.html?chid=<{$_categories2.CHID}>"><{$_categories2.name}></a></li>
                                    <{/foreach}>
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
            <{foreach $hotVideos as $hotVideo}>
                <div class="item">
                    <div class="zoom-container">
                        <div class="zoom-caption">
                            <span><{$hotVideo['keyword']}></span>
                            <a href="single<{$hotVideo['embedded_url_path']}>">
                                <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                            </a>
                            <p><{$hotVideo['title']}></p>
                        </div>
                        <img src="<{$hotVideo['preview_url']}>" />
                    </div>
                </div>
            <{/foreach}>
        </div>
    </div>
</header>
<{$qwe}>