<{include './public/header.tpl' title='今日首发-archive'}>
<!-- Header -->
	
	<!-- /////////////////////////////////////////Content -->
	<div id="page-content" class="archive-page">
		<div class="container">
			<div class="row">
				<div id="main-content" class="col-md-8">
					<{foreach $searchs as $search}>
					<article>
						<a href="#"><h2 class="vid-name"><{$search.keyword}></h2></a>
						<div class="info">
							<h5>By <a href="#"> Matt </a></h5>
							<span><i class="fa fa-calendar"></i> <{$search.addtime}> </span>
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
									<span><{$search.keyword}>1</span>
									<a href="<{$host}>/single<{$search.embedded_url_path}>">
										<i class="fa fa-play-circle-o fa-5x" style="color: #fff"></i>
									</a>
									<p></p>
								</div>
								<img src="<{$search.preview_url}>" />
							</div>
							<p> <{$search.title}><!-- <a href="#">更多...</a> --></p>
						</div>
					</article>
					<div class="line"></div>
					<{/foreach}>

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
				<{include './public/sidebar.tpl'}>
			</div>
		</div>
	</div>

<{include './public/footer.tpl'}>
