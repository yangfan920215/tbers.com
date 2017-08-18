<{include './public/header.tpl' title='今日首发-日日精彩contact'}>
<!-- Header -->
	<!-- /////////////////////////////////////////Content -->
	<div id="page-content" class="contact-page">
		<div class="container">
			<div class="row">
				<div id="main-content" class="col-md-8">
					<div class="box">
						<center><div class="art-header">
							<h1 class="center">火钳留名 永久福利</h1>
						</div></center>
						<div class="art-content">
							<div id="contact_form">
								<div name="form1" id="ff" method="post" action="http://core.app/contact">
									<label>
									<span>QQ:</span>
									<input name="qq" id="qq" required>
									</label>
									<label>邮箱:</span>
									<input type="email"  name="email" id="email" required>
									</label>
									<!--<label>
									<span>Your message here:</span>
									<textarea name="message" id="message"></textarea>
									</label>-->
									<center><input id="contact_button" type="submit" class="sendButton" name="Submit" value="提交"></center>
								</div>
							</div>
						</div>
					</div>
				</div>
				<{include './public/sidebar.tpl'}>
			</div>
		</div>
	</div>
<{include './public/footer.tpl'}>

<script>
    $(function(){
        $('#contact_button').click(function(){
            var qq = $('#qq').val();
            var email = $('#email').val();

            // 验证QQ号是否合规
            var reQQ = /^[1-9]\d{4,9}$/;
            if(!reQQ.test(qq)){
                alert('QQ号码为空或格式不正确,请修正后重新输入！');
                return;
            }
            // 验证邮箱是否合规
            var reEmail = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
            if(!reEmail.test(email)){
                alert('邮箱为空或格式不正确,请修正后重新输入!');
                return;
            }

            $.ajax({
                type: "POST",
                url: "http://core.app/contact",
                data: {'qq':qq,'email':email},
                dataType: "json",
                success: function(data){
                    if(data.status == undefined || data.status !== 0){
                        var msg = data.msg == undefined ? '服务器繁忙,请稍后重试!' : data.msg;
                        alert(msg);
                        return;
                    }

                    $(data.data.actresses_names).each(function (index, item) {
                        $('#actresses_names').append('<li><a href="archive.html">' + item + '</a></li>');
                    });

                    alert(data.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('服务器异常,请稍后再试！');
                    // alert(xhr.status);
                    // alert(thrownError);
                }
            });
        });
</script>