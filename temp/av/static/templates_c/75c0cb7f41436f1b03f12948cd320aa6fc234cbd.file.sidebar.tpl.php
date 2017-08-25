<?php /* Smarty version Smarty-3.1-DEV, created on 2017-08-25 17:25:13
         compiled from "/data/wwwroot/tbers.com/temp/av/tpl/public/sidebar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1683151255996ab49ef0ff9-75297418%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '75c0cb7f41436f1b03f12948cd320aa6fc234cbd' => 
    array (
      0 => '/data/wwwroot/tbers.com/temp/av/tpl/public/sidebar.tpl',
      1 => 1503653106,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1683151255996ab49ef0ff9-75297418',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_5996ab49ef4694_27358094',
  'variables' => 
  array (
    'tabs_sidebar' => 0,
    'host' => 0,
    'tab_sidebar' => 0,
    'wifeVideos' => 0,
    'wifeVideo' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5996ab49ef4694_27358094')) {function content_5996ab49ef4694_27358094($_smarty_tpl) {?><div id="sidebar" class="col-md-4">
    <!---- Start Widget ---->
    <div class="widget wid-follow">
        <div class="heading"><h4><i class="fa fa-users"></i> 关注我们</h4></div>
        <div class="content">
            <ul class="list-inline">
                <li>
                    <a href="javascript:alert('暂未开放,敬请期待!')">
                        <div class="box-facebook">
                            <span class="fa fa-facebook fa-2x icon"></span>
                            <span>1250</span>
                            <span>Fans</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:alert('暂未开放,敬请期待!')">
                        <div class="box-twitter">
                            <span class="fa fa-twitter fa-2x icon"></span>
                            <span>1250</span>
                            <span>Fans</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:alert('暂未开放,敬请期待!')">
                        <div class="box-google">
                            <span class="fa fa-google-plus fa-2x icon"></span>
                            <span>1250</span>
                            <span>Fans</span>
                        </div>
                    </a>
                </li>
            </ul>
            <img src="../images/banner.jpg" />
        </div>
        <div class="line"></div>
    </div>
    <!---- Start Widget ---->
    <div class="widget wid-tags">
        <div class="heading"><h4><i class="fa fa-tags"></i> Tag</h4></div>
        <div class="content">
            <ul class="list-inline">
                <?php  $_smarty_tpl->tpl_vars['tab_sidebar'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tab_sidebar']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs_sidebar']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tab_sidebar']->key => $_smarty_tpl->tpl_vars['tab_sidebar']->value){
$_smarty_tpl->tpl_vars['tab_sidebar']->_loop = true;
?>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['host']->value;?>
/archive/<?php echo $_smarty_tpl->tpl_vars['tab_sidebar']->value['decode'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab_sidebar']->value['name'];?>
 ,</a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="line"></div>
    </div>
    <!---- Start Widget ---->
    <div class="widget wid-post">
        <div class="heading"><h4><i class="fa fa-globe"></i> 人妻</h4></div>
        <div class="content">

            <?php  $_smarty_tpl->tpl_vars['wifeVideo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['wifeVideo']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['wifeVideos']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['wifeVideo']->key => $_smarty_tpl->tpl_vars['wifeVideo']->value){
$_smarty_tpl->tpl_vars['wifeVideo']->_loop = true;
?>
            <div class="post wrap-vid">
                <div class="zoom-container">
                    <div class="zoom-caption">
                        <span></span>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['host']->value;?>
/single<?php echo $_smarty_tpl->tpl_vars['wifeVideo']->value['embedded_url_path'];?>
">
                            <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                        </a>
                        <p></p>
                    </div>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['wifeVideo']->value['preview_url'];?>
" />
                </div>
                <div class="wrapper">
                    <h5 class="vid-name"><a href="#"><?php echo $_smarty_tpl->tpl_vars['wifeVideo']->value['keyword'];?>
</a></h5>
                    <div class="info">
                        <h6>By <a href="#">matt</a></h6>
                        <span><i class="fa fa-calendar"></i>25/3/2015</span>
                        <span><i class="fa fa-heart"></i>1,200</span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="line"></div>
    </div>
    <!---- Start Widget ---->
    <div class="widget wid-news">
        <div class="heading"><h4><i class="fa fa-clock-o"></i> Top News</h4></div>
        <div class="content">
            <div class="wrap-vid">
                <div class="zoom-container">
                    <div class="zoom-caption">
                        <span>keyword</span>
                        <a href="single.html">
                            <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                        </a>
                        <p>片子里的matt好惨</p>
                    </div>
                    <img src="../images/3.jpg" />
                </div>
                <h3 class="vid-name"><a href="#">片子里的matt好惨</a></h3>
                <div class="info">
                    <h5>By <a href="#">phoenix</a></h5>
                    <span><i class="fa fa-calendar"></i>25/3/2015</span>
                    <span><i class="fa fa-heart"></i>1,200</span>
                </div>
            </div>
            <div class="wrap-vid">
                <div class="zoom-container">
                    <div class="zoom-caption">
                        <span>keyword</span>
                        <a href="single.html">
                            <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                        </a>
                        <p>片子里的matt好惨</p>
                    </div>
                    <img src="../images/4.jpg" />
                </div>
                <h3 class="vid-name"><a href="#">片子里的matt好惨</a></h3>
                <div class="info">
                    <h5>By <a href="#">phoenix</a></h5>
                    <span><i class="fa fa-calendar"></i>25/3/2015</span>
                    <span><i class="fa fa-heart"></i>1,200</span>
                </div>
            </div>
            <div class="wrap-vid">
                <div class="zoom-container">
                    <div class="zoom-caption">
                        <span>keyword</span>
                        <a href="single.html">
                            <i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
                        </a>
                        <p>片子里的matt好惨</p>
                    </div>
                    <img src="../images/5.jpg" />
                </div>
                <h3 class="vid-name"><a href="#">片子里的matt好惨</a></h3>
                <div class="info">
                    <h5>By <a href="#">phoenix</a></h5>
                    <span><i class="fa fa-calendar"></i>25/3/2015</span>
                    <span><i class="fa fa-heart"></i>1,200</span>
                </div>
            </div>
        </div>
        <div class="line"></div>
    </div>
    <!---- Start Widget ---->
    <div class="widget wid-post">
        <div class="heading"><h4><i class="fa fa-comments"></i> 评论</h4></div>
        <div class="content">
            <div class="post">
                <a href="single.html">
                    <img src="../images/user.png" />
                </a>
                <div class="wrapper">
                    <a href="#"><h5>那些年曾经追过的老师</h5></a>
                    <ul class="list-inline">
                        <li><i class="fa fa-calendar"></i>25/3/2015</li>
                        <li><i class="fa fa-comments"></i>1,200</li>
                    </ul>
                </div>
            </div>
            <div class="post">
                <a href="single.html">
                    <img src="../images/user.png" />
                </a>
                <div class="wrapper">
                    <a href="#"><h5>老师才是永恒的</h5></a>
                    <ul class="list-inline">
                        <li><i class="fa fa-calendar"></i>25/3/2015</li>
                        <li><i class="fa fa-comments"></i>1,200</li>
                    </ul>
                </div>
            </div>
            <div class="post">
                <a href="single.html">
                    <img src="../images/user.png" />
                </a>
                <div class="wrapper">
                    <a href="#"><h5>所有的真爱 都在这里了</h5></a>
                    <ul class="list-inline">
                        <li><i class="fa fa-calendar"></i>25/3/2015</li>
                        <li><i class="fa fa-comments"></i>1,200</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="line"></div>
    </div>
    <div class="widget wid-banner">
        <img src="../images/banner-2.jpg" />
    </div>
</div><?php }} ?>