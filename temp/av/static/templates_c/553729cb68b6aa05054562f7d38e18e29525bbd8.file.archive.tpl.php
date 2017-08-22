<?php /* Smarty version Smarty-3.1-DEV, created on 2017-08-22 11:25:22
         compiled from "./tpl/archive.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16903916445996a4eccc9510-41976012%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '553729cb68b6aa05054562f7d38e18e29525bbd8' => 
    array (
      0 => './tpl/archive.tpl',
      1 => 1503372317,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16903916445996a4eccc9510-41976012',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_5996a4ecccb7d3_34600475',
  'variables' => 
  array (
    'searchs' => 0,
    'search' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5996a4ecccb7d3_34600475')) {function content_5996a4ecccb7d3_34600475($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('./public/header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('title'=>'今日首发-archive'), 0);?>

<!-- Header -->
	
	<!-- /////////////////////////////////////////Content -->
	<div id="page-content" class="archive-page">
		<div class="container">
			<div class="row">
				<div id="main-content" class="col-md-8">
					<?php  $_smarty_tpl->tpl_vars['search'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['search']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['searchs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['search']->key => $_smarty_tpl->tpl_vars['search']->value){
$_smarty_tpl->tpl_vars['search']->_loop = true;
?>
					<article>
						<a href="#"><h2 class="vid-name"><?php echo $_smarty_tpl->tpl_vars['search']->value['keyword'];?>
</h2></a>
						<div class="info">
							<h5>By <a href="#"> Matt </a></h5>
							<span><i class="fa fa-calendar"></i> <?php echo $_smarty_tpl->tpl_vars['search']->value['addtime'];?>
 </span>
							<span><i class="fa fa-comment"></i> 0 Comments</span>
							<span><i class="fa fa-heart"></i> 0 </span>
							<ul class="list-inline">
								<li><a href="#" style="text-decoration: underline;color:#333;">Rate</a></li>
								<li> - </li>
								<li>
									<span class="rating">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star-half-o"></i>
									</span>
								</li>
							</ul>
						</div>
						<div class="wrap-vid">
							<div class="zoom-container">
								<div class="zoom-caption">
									<span><?php echo $_smarty_tpl->tpl_vars['search']->value['keyword'];?>
</span>
									<a href="single?vid=<?php echo $_smarty_tpl->tpl_vars['search']->value['vid'];?>
">
										<i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
									</a>
									<p></p>
								</div>
								<img src="<?php echo $_smarty_tpl->tpl_vars['search']->value['preview_url'];?>
" />
							</div>
							<p> <?php echo $_smarty_tpl->tpl_vars['search']->value['title'];?>
<!-- <a href="#">更多...</a> --></p>
						</div>
					</article>
					<div class="line"></div>
					<?php } ?>

			<!--		<center>
						<ul class="pagination">
							<li>
							  <a href="#" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							  </a>
							</li>
							<li><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li>
							  <a href="#" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							  </a>
							</li>
						</ul>
					</center> -->
				</div>
				<?php echo $_smarty_tpl->getSubTemplate ('./public/sidebar.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			</div>
		</div>
	</div>

<?php echo $_smarty_tpl->getSubTemplate ('./public/footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>