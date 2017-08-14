<?php get_template_part( 'header', get_post_format() ); ?>
<body>
<div id="hd">
  <div class="pps-header">
    <div class="header-inner">
      <div class="pps-logo">
        <?php $options = get_option('mfthemes_options'); $logo =  $options['logo'] ? $options['logo'] : get_bloginfo('template_url')."/assets/images/logo.png" ;?>
        <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php echo $logo;?>" alt="<?php bloginfo('name'); ?>" /></a></div>
      <div class="pps-so">
        <div class="pps-so-panel">
          <form target="_blank" action="<?php bloginfo('siteurl'); ?>" method="get">
            <input type="text" id="tsText" class="input" name="s" value="" onblur="if(this.value==&#39;&#39;){this.value=defaultValue};this.className=&#39;input&#39;" onfocus="if(this.value==defaultValue){this.value=&#39;&#39;};this.className=&#39;input input_fouse&#39;" autocomplete="off">
            <input type="submit" class="button" value="搜索" >
          </form>
          <span class="ico-h-so"></span> </div>
        <div class="pps-so-suggest">
          <ul class="suggest-list">
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="pps-nav">
    <div class="nav-inner">
      <div class="nav-main">
        <ul class="nav-list">
          <li class="nav-item nav-index"><a href="<?php bloginfo('siteurl'); ?>" >首页</a></li>
          <li class="nav-item">
            <?php 
	$menuParameters = array(
	    'theme_location'=>'primary',
		'container'	=> false,
		'echo'	=> false,
		'items_wrap' => '%3$s',
		'depth'	=> 0,
	    );
	   echo strip_tags(wp_nav_menu( $menuParameters ), '<a>' );
                    ?>
          </li>
        </ul>
      </div>
      <div class="pps-download-sya">
        <div class="ddl"> <a> <em class="pps-pic">
          <?php mfthemes_main_ad();?>
          </em> </a> </div>
      </div>
    </div>
  </div>
</div>
<?php mfthemes_before_content();?>
<div class="ge"></div>
<div id="bd"> 
  <!--col-sya-->
  <div class="col-sya p-cols">
    <div class="p-col1">
      <div class="p-col1-1">
        <div class="bx pitch">
          <div class="hd">
            <h2 id="强档热播" class="h">强档热播</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <ul class="p-list">
              <?php
$sticky = get_option('sticky_posts');
rsort( $sticky );
$sticky = array_slice( $sticky, 0, 10);
query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );
if (have_posts()) :
while (have_posts()) : the_post();
?>
              <li class="p-item"> <a href="<?php the_permalink(); ?>" target="_blank" title="<?php the_title(); ?>" rel="bookmark" class="thumb-outer"> <img alt="<?php the_title(); ?>" width="148" height="216" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216" class="thumb"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"> <b class="ico-video-quality"></b> </span> </a>
                <div class="t"><a href="<?php the_permalink(); ?>" target="_blank">
                  <?php the_title(); ?>
                  </a></div>
                <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
              </li>
              <?php endwhile; endif; ?>
            </ul>
          </div>
        </div>
        <div class="bx pitch">
          <div class="hd">
            <h2 id="新片速递" class="h">新片速递</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <div class="tab"> <a href="javascript:void(0)" class="tab-item select">华语</a>/<a href="javascript:void(0)" class="tab-item">欧美</a>/<a href="javascript:void(0)" class="tab-item">日韩</a>/<a href="javascript:void(0)" class="tab-item">预告</a> </div>
            <div class="tab-panel">
              <?php $posts = get_posts( "category_name=huayu&numberposts=5" ); ?>
              <?php if( $posts ) : ?>
              <ul class="p-list">
                <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
                <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                  <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <?php the_title(); ?>
                    </a></div>
                  <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
                </li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
            <div class="tab-panel hide">
              <?php $posts = get_posts( "category_name=oumei&numberposts=5" ); ?>
              <?php if( $posts ) : ?>
              <ul class="p-list">
                <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
                <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                  <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <?php the_title(); ?>
                    </a></div>
                  <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
                </li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
            <div class="tab-panel hide">
              <?php $posts = get_posts( "category_name=rihan&numberposts=5" ); ?>
              <?php if( $posts ) : ?>
              <ul class="p-list">
                <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
                <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                  <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <?php the_title(); ?>
                    </a></div>
                  <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
                </li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
            <div class="tab-panel hide">
              <?php $posts = get_posts( "category_name=yugao&numberposts=5" ); ?>
              <?php if( $posts ) : ?>
              <ul class="p-list">
                <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
                <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                  <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <?php the_title(); ?>
                    </a></div>
                  <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
                </li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="bx pitch">
          <div class="hd">
            <h2 id="华语电影" class="h">华语电影</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <?php $posts = get_posts( "category_name=huayu&tag=推荐&numberposts=5" ); ?>
            <?php if( $posts ) : ?>
            <ul class="p-list">
              <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
              <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                  <?php the_title(); ?>
                  </a></div>
                <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php
    $category_id = get_cat_ID( '华语' );
    $category_link = get_category_link( $category_id );
?>
            <div class="act"><a href="<?php echo esc_url( $category_link ); ?>" class="more" target="_blank">更多<em>&gt;&gt;</em></a></div>
          </div>
        </div>
        <div class="bx pitch">
          <div class="hd">
            <h2 id="欧美电影" class="h">欧美电影</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <?php $posts = get_posts( "category_name=oumei&tag=推荐&numberposts=5" ); ?>
            <?php if( $posts ) : ?>
            <ul class="p-list">
              <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
              <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                  <?php the_title(); ?>
                  </a></div>
                <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php
    $category_id = get_cat_ID( '欧美' );
    $category_link = get_category_link( $category_id );
?>
            <div class="act"><a href="<?php echo esc_url( $category_link ); ?>" class="more" target="_blank">更多<em>&gt;&gt;</em></a></div>
          </div>
        </div>
        <div class="bx pitch">
          <div class="hd">
            <h2 id="日韩电影" class="h">日韩电影</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <?php $posts = get_posts( "category_name=rihan&tag=推荐&numberposts=5" ); ?>
            <?php if( $posts ) : ?>
            <ul class="p-list">
              <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
              <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                  <?php the_title(); ?>
                  </a></div>
                <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php
    $category_id = get_cat_ID( '日韩' );
    $category_link = get_category_link( $category_id );
?>
            <div class="act"><a href="<?php echo esc_url( $category_link ); ?>" class="more" target="_blank">更多<em>&gt;&gt;</em></a></div>
          </div>
        </div>
        <div class="bx">
          <div class="hd">
            <h2 id="经典回顾" class="h">经典回顾</h2>
            <span class="adorn-blue adorn"></span></div>
          <div class="bd">
            <?php $posts = get_posts( "category_name=jingdian&tag=推荐&numberposts=5" ); ?>
            <?php if( $posts ) : ?>
            <ul class="p-list">
              <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
              <li class="p-item"> <a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title(); ?>" class="thumb-outer"> <img width="148" height="216" class="thumb" alt="<?php the_title(); ?>" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=148&h=216"> <span class="video-quality video-quality<?php echo get_post_meta($post->ID,"video_quality",true); ?>"><b class="ico-video-quality"></b></span> </a>
                <div class="t"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                  <?php the_title(); ?>
                  </a></div>
                <div class="des"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php
    $category_id = get_cat_ID( '经典' );
    $category_link = get_category_link( $category_id );
?>
            <div class="act"><a href="<?php echo esc_url( $category_link ); ?>" class="more" target="_blank">更多<em>&gt;&gt;</em></a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="p-col3">
      <div class="retrieve bx-sya pitch">
        <div class="bd">
          <ul class="retrieve-list">
            <li> <span class="rt">类型：</span> <a href="?tag=剧情/" title="剧情">剧情</a> <a href="?tag=喜剧/" title="喜剧">喜剧</a> <a href="?tag=动作/" title="动作">动作</a> <a href="?tag=爱情/" title="爱情">爱情</a> <a href="?tag=惊悚/" title="惊悚">惊悚</a> <a href="?tag=恐怖/" title="恐怖">恐怖</a> <a href="?tag=犯罪/" title="犯罪">犯罪</a> <a href="?tag=冒险/" title="冒险">冒险</a> <a href="?tag=科幻/" title="科幻">科幻</a> </li>
            <li> <span class="rt">地区：</span> <a href="?tag=美国/" title="美国">美国</a> <a href="?tag=香港/" title="香港">香港</a> <a href="?tag=大陆/" title="大陆">大陆</a> <a href="?tag=韩国/" title="韩国">韩国</a> <a href="?tag=日本/" title="日本">日本</a> <a href="?tag=法国/" title="法国">法国</a> <a href="?tag=德国/" title="德国">德国</a> <a href="?tag=英国/" title="英国">英国</a> <a href="?tag=泰国/" title="泰国">泰国</a> <a href="?tag=印度/" title="印度">印度</a> </li>
            <li> <span class="rt">年份：</span> <a href="?tag=2014/" title="2014">2014</a> <a href="?tag=2013/" title="2013">2013</a> <a href="?tag=2012/" title="2012">2012</a> <a href="?tag=2011/" title="2011">2011</a> <a href="?tag=2010/" title="2010">2010</a> </li>
          </ul>
          <div class="mini-seacher">
            <form action="<?php bloginfo('siteurl'); ?>" method="get" target="_blank">
              <input class="input fl" name="s" type="text">
              <input class="button fl" type="submit" value="">
            </form>
          </div>
          
        </div>
      </div>
      <div class="rank bx-sya pitch">
        <div class="hd">
          <h2 class="h">电影排行</h2>
          <span class="ico-rank ico"></span></div>
        <div class="bd">
          <table>
            <tr>
              <td class="ph"></td>
              <td><ol class="rank-list tab-panel">
                  <?php if (function_exists('get_most_viewed')): ?>
                  <ul>
                    <?php get_most_viewed('post',10,20); ?>
                  </ul>
                  <?php endif; ?>
                </ol></td>
            </tr>
          </table>
          <div class="textwidget">
            <div id="sslider">
              <div id="sslider-wrap">
                <div id="sslider-main">
                  <?php mfthemes_paihang_ad();?>
                </div>
              </div>
              <span id="sslider-prev">&lt;</span><span id="sslider-next">&gt;</span> </div>
            <script>
jQuery(function ($) {
var sslider=$("#sslider-wrap"), n=0, l=$("a",sslider).length;
function go() {if(n<0)n=l-1;if(n>l-1)n=0;sslider.stop().animate({"scrollLeft":n*250});};
$("#sslider-next").click(function(){go(n++);});
$("#sslider-prev").click(function(){go(n--);});
var timer = setInterval(function(){$("#sslider-next").trigger("click")},5e3);
sslider.hover(function(){clearInterval(timer)},function(){timer=setInterval(function(){$("#sslider-next").trigger("click")},5e3)})
});
</script></div>
        </div>
      </div>
      <div class="bx-sya pitch">
        <div class="hd">
          <h2 class="h">即将上线</h2>
        </div>
        <div class="bd">
          <?php $posts = get_posts( "category_name=yugao&tag=即将上线&numberposts=5" ); ?>
          <?php if( $posts ) : ?>
          <ul class="p-list-syb">
            <?php foreach( $posts as $post ) : setup_postdata( $post ); ?>
            <li class="p-item"> <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" target="_blank" class="thumb-outer"><img alt="<?php the_title(); ?>" width="110" height="70" class="thumb" src="<?php bloginfo('template_directory'); ?>/timthumb.php?src=<?php echo get_post_meta($post->ID,"video_poster",true); ?>&w=110&h=70"></a>
              <div class="p-info">
                <div class="t"><a href="<?php the_permalink() ?>" target="_blank">
                  <?php the_title(); ?>
                  </a></div>
                <div class="sub"><?php echo get_post_meta($post->ID,"video_desc",true); ?></div>
                <div class="sub"><?php echo get_post_meta($post->ID,"video_date",true); ?></div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
      <?php mfthemes_zhuanti_content();?>
      <div class="bx-sya pitch attention">
        <div class="hd">
          <h2 class="h">关注我们</h2>
        </div>
        <div class="bd">
          <div class="attention-bd">
            <?php
	$options = get_option('mfthemes_options');
	if( $options['index_tsina']);
?>
            <wb:bulkfollow uids="<?php echo $options['index_tsina'];?>" type="0" width="292" border="0" count="3" color="FFFFFF,fbf9f9,000000,000000" titlebar="n" info="n" verified="n">
              <iframe src="http://widget.weibo.com/relationship/bulkfollow.php?uids=<?php echo $options['index_tsina'];?>&amp;sense=0&amp;verified=0&amp;count=3&amp;width=292&amp;height=489&amp;color=FFFFFF,fbf9f9,000000,000000&amp;nick=0&amp;language=zh_cn&amp;showtitle=0&amp;showinfo=0&amp;wide=0&amp;refer=http%3A%2F%2Fwww.dz9.net%2Fmovie%2F" width="292" height="489" frameborder="0" scrolling="no" marginheight="0"></iframe>
            </wb:bulkfollow>
          </div>
        </div>
      </div>
      <div class="bx-sya">
        <div class="hd">
          <h2 class="h">手机扫一扫</h2>
        </div>
        <div class="bd">
          <ul class="load-app-list">
            <li class="p-item"> 
              <script>
thisURL = document.URL;
strwrite = "<img src='http://qr.liantu.com/api.php?m=5&text=" + thisURL + "' width='246' height='246' alt='二维码' class='img'/>";
document.write( strwrite );
</script> 
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_template_part( 'footer', get_post_format() ); ?>
