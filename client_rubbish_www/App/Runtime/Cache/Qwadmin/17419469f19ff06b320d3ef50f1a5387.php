<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- 启用ie最高浏览器 -->
    <title>招商首页</title>
    <link rel="stylesheet" href="../../a8/7919/58db308e38aafaafe031e7d0bf.css">
    <link rel="stylesheet" href="../../72/b599/44ee5cf3d47fea19ca3f1a62c4.css">
    <link rel="stylesheet" href="../../44/884c/c9231aa847125bcf89ef821c00.css">
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet"> <!-- 特殊字体 -->
</head>
<body>
<!--公用的头部-->
<div class="jack-header">
    <div class="w">
        <div class="header-l">
            <a href="<?php echo ($dayu_pcclient); ?>" class="header-l-a">PC客户端</a>
            <a href="<?php echo ($dayu_preventHijack); ?>" class="header-l-a">防劫持教程</a>
        </div>
        <div class="header-r">
            <div class="header-drop down-drop">
                <a href="###" class="header-drop-a">
                    <img src="../../96/0da1/a0a23e147cfd81f6e9a93a322b.png" alt="">
                    <span class="header-user"><?php echo ($val["nickname"]); ?></span>
                    <img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop bar-drop down-menu">

                </div>
            </div>
            <span class="header-line">|</span>
            <div class="header-drop">
                <span class="wallet">钱包余额：</span><span class="header-money">￥<?php echo ($val["gold_show"]); ?>元</span>
            </div>
            <span class="header-line">|</span>
            <div class="header-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="header-btn">充值</a>
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="header-btn">提款</a>
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="header-btn">转账</a>
            </div>
        </div>
    </div>
</div>
<!--导航-->
<div class="jack-nav">
    <div class="w">
        <div class="nav-l">
            <a href="<?php echo ($dayu_signin); ?>" class="header-logo"><img src="../../43/5b09/0e52057c5d089dc49b3b0d8e57.png" width="209" height="60" alt=""></a>
        </div>
        <ul class="nav-r">
            <li class="nav-li down-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a">
                    全部游戏<img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop nav-first down-menu">
                </div>
            </li>
            <li class="nav-li down-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a">
                    游戏记录<img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop down-menu">

                </div>
            </li>
            <li class="nav-li down-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a">
                    资金管理<img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop down-menu">
                </div>
            </li>
            <li class="nav-li down-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a">
                    账户管理<img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop down-menu">
                </div>
            </li>
            <li class="nav-li down-drop">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a nav-agency">
                <a href="http://www.dayu.lgv/reg/7d6b1a7f" class="nav-a nav-agency">
                    <img src="../../42/37d2/b0d77077a16658f58bc5bb67c6.png" alt="">
                    <span class="agency-center">
                        <em>代理中心</em>
                        <span>Agency center</span>
                    </span>
                    <img src="../../93/f21b/2e0254831b96e7288e957fb30d.png" class="arrow-down" alt="">
                </a>
                <div class="nav-drop nav-last down-menu">

                </div>
            </li>
        </ul>
    </div>
</div>
<!--轮播图-->
<div class="jack-banner">
    <div class="fullSlide">
        <div class="bd">
            <ul>
                <?php if(is_array($img_json)): foreach($img_json as $key=>$vo): ?><li><a target="_blank" href="<?php echo ($val["_url1"]); ?>"><img src="../..<?php echo ($vo); ?>" alt=""></a></li><?php endforeach; endif; ?>
            </ul>
        </div>
        <div class="hd">
            <div class="w">
                <ul></ul>
            </div>
        </div>
        <div class="prev-next">
            <a class="prev"></a>
            <a class="next"></a>
        </div>
    </div>
</div>
<div class="register">
    <div class="w">
        <a href="<?php echo ($val["_url1"]); ?>" class="register-a"><img src="../../ca/ab0e/b29d1a305433ad9d9182c5d293.jpg" alt=""></a>
        <a href="<?php echo ($val["_url2"]); ?>" class="register-a"><img src="../../af/fb2d/33ec1fcd64c82ad4900395712a.jpg" alt=""></a>
        <a href="<?php echo ($dayu_signin); ?>" class="register-a"><img src="../../42/4558/f86986802732780a31e9b44471.jpg" alt=""></a>
    </div>
</div>
<div class="flow">
    <div class="w">
        <div class="flow-con">
            <span class="flow-number">01</span>
            <span class="flow-text">
                <span class="span-1">开设大鱼账户</span>
                <span class="span-2">申请账户可通过自助开户注册，也可以联系人工开户注册。</span>
            </span>
        </div>
        <div class="flow-arrow"><img src="../../bd/7d3a/7121c8fb1aded27e275b4e6ca8.png" alt=""></div>
        <div class="flow-con">
            <span class="flow-number">02</span>
            <span class="flow-text">
                <span class="span-1">开设大鱼账户</span>
                <span class="span-2">推荐您使用快速登陆器迅速安全访问大鱼，登陆器在本页面右上角下载。</span>
            </span>
        </div>
        <div class="flow-arrow"><img src="../../bd/7d3a/7121c8fb1aded27e275b4e6ca8.png" alt=""></div>
        <div class="flow-con">
            <span class="flow-number">03</span>
            <span class="flow-text">
                <span class="span-1">开设大鱼账户</span>
                <span class="span-2">充提无需经过任何人，完全自主管理，10款精品高频彩票游戏供您娱乐。</span>
            </span>
        </div>
    </div>
</div>
<!--主要的内容-->
<div class="brand-con">
    <div class="w">
        <ul class="banner-list clearfix">
            <li>
                <div class="title">十四年信誉保障</div>
                <img src="../../images/brand/header-1.jpg" alt="">
                <div class="text">
                    大鱼游戏的主集团十四年成就行业龙头地位，实力响誉彩界，雄厚资金能力承诺100%兑现赔付，
                    确保您畅玩无忧。我们与主集团统一管理，风险管控能力引领行业顶尖水平，十四年稳健发展经
                    验为您挡风雨、稳发展。
                </div>
            </li>
            <li>
                <div class="title">顶级安全防护</div>
                <img src="../../images/brand/header-2.jpg" alt="">
                <div class="text">
                    大鱼平台由从业14年以上的专业技术团队研发，通过Global Trust国际安全认证，采用AES
                    256位加密保障资金信息安全，使用CDN多线加速，分布式高防云防御和军用级防火墙保证高速稳定，
                    并与国内15家主流银行深度对接，充值30秒内到账，提款3分钟内到账。
                </div>
            </li>
            <li>
                <div class="title">行业权威认证</div>
                <img src="../../images/brand/header-3.jpg" alt="">
                <div class="text">
                    大鱼拥有菲律宾政府FCLRC (First Cagayan Leisure and Resort Corporation)颁发的博
                    彩牌照并通过GLI (Gaming Laboratories International) 权威认证，是合法、安全的购彩平台。
                    我们秉持信誉至上，用户第一的经营理念，为用户提供专业、创新、极致的顶级博彩体验。
                </div>
            </li>
            <li>
                <div class="title">精彩游戏产品</div>
                <img src="../../images/brand/header-4.jpg" alt="">
                <div class="text">
                    为满足用户多样化娱乐需求，大鱼提供19款游戏，包括5款时时彩（重庆、江西、新疆、天津、黑龙江），
                    3款11选5（广东、江西、山东），2款快3(江苏、安徽)，2款低频游戏（福彩3D，体彩P3/P5），并独立
                    研发采用瑞士硬件开奖确保公正公平的7款自主彩（大鱼1分、2分、5分彩、大鱼11选5、大鱼快3、骰宝、龙虎斗）。
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="brand-run">
    <div class="w">
        <div class="title-com">
            <div class="h2">运营能力</div>
            <div class="h3">Operating&nbsp;&nbsp;ability</div>
        </div>
        <ul class="run-list-1 clearfix">
            <li>游戏大厅</li>
            <li>充提优势</li>
            <li>速度激情</li>
            <li>安全优势</li>
            <li>平台运营</li>
        </ul>
        <ul class="run-list-2 clearfix">
            <li>
                <div class="run-mb">自主彩(大鱼1分、2分、5分彩、大鱼11选5、大鱼快3、骰宝、龙虎斗|瑞士硬件开奖) </div>
                <div class="run-mb">时时彩（重庆、江西、新疆、天津、黑龙江）</div>
                <div class="run-mb">11选5（广东、江西、山东） </div>
                <div class="run-mb">快3(江苏、安徽) </div>
                <div class="run-mb">低频彩(福彩3D、体彩P3/P5)</div>
            </li>
            <li>
                <div>10家银行转账充值</div>
                <div>15家银行快捷充值</div>
                <div>19家银行银联快捷充值</div>
                <div>支付宝充值</div>
                <div class="run-mb">财付通充值</div>
                <div>单笔充值最低2元，最高19万元 </div>
                <div class="run-mb">单笔提现最低100元，最高5万元</div>
                <div>充值30秒内到账</div>
                <div class="run-mb">提现3分钟内到账</div>
                <div>充值每日不限次数</div>
                <div>提现每日可达20次</div>
            </li>
            <li>
                <div class="run-mb">单式10万注，秒投无压力</div>
                <div class="run-mb">万人不卡，光速体验</div>
                <div class="run-mb">超越极限，自由随心</div>
            </li>
            <li>
                <div>Global Trust国际认证</div>
                <div class="run-mb">美国Cisco刀片服务器</div>
                <div>AES 256位加密存储</div>
                <div>DSA数字签名加密通信</div>
                <div class="run-mb">实时动态安全扫描</div>
                <div>分布式高防云防御</div>
                <div>军用级防火墙</div>
                <div>国内CDN多线加速</div>
            </li>
            <li>
                <div>14年信誉保障</div>
                <div class="run-mb">支持元角分模式</div>
                <div>100%兑现赔付</div>
                <div class="run-mb">单期单注最高派奖40万元</div>
                <div>7X24小时在线客服</div>
                <div>推荐使用谷歌浏览器</div>
            </li>
        </ul>
    </div>
</div>
<!--公用的页脚-->
<div class="jack-foot">
    <div class="w">
        <div class="foot-top clearfix">
            <div class="foot-logo">
                <a href="<?php echo ($dayu_signin); ?>"><img src="../../5a/ba34/880649ecaf0cf6048694f4bde6.png" alt=""></a>
            </div>
            <div class="foot-number">
                <div class="number-wrap today-win"><span class="rmb">￥</span><div class="numberRun1"></div><div class="line-number">今日已实现兑奖</div></div>
                <div class="number-wrap"><div class="numberRun2"></div><div class="line-number">在线人数</div></div>
            </div>
        </div>
        <div class="foot-link clearfix">
            <div class="foot-a">
                <a href="<?php echo ($dayu_brand); ?>">大鱼品牌</a>
                <span></span>
                <a href="<?php echo ($dayu_help); ?>">帮助中心</a>
                <span></span>
                <a href="<?php echo ($dayu_mobile); ?>">手机客户端</a>
                <span></span>
                <a href="<?php echo ($dayu_pcclient); ?>">PC客户端</a>
                <span></span>
                <a href="<?php echo ($dayu_preventHijack); ?>">防劫持教程</a>
            </div>
            <div class="foot-authority">权威认证资质</div>
        </div>
        <div class="foot-erweima visible-xs-block"><img src="../../dd/6781/6d76eaa6fcc7c0db1b2276d87c.png" alt=""></div>
        <div class="foot-pic clearfix">
            <ul class="foot-icon">
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../af/f197/7234b199960800295f1a928af1.svg" src="../../02/9346/0db266ee47146d008dd88add59.png" width="23" height="23"/>
                    </svg>
                </li>
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../69/a625/e745dacf8b570df7118d912ef2.svg" src="../../7e/1c30/fad87c1641353267ef91d70348.png" width="23" height="23"/>
                    </svg>
                </li>
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../44/e844/ba53c256ea9ae2b97f15fcf19c.svg" src="../../f2/7691/5321df8daee9ed0761cbeaec78.png" width="23" height="23"/>
                    </svg>
                </li>
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../ee/f0fe/8f54632bc34ffe7b2de53f3c5f.svg" src="../../2c/bfcf/c95e55d01bc449e8f679a840b8.png" width="23" height="23"/>
                    </svg>
                </li>
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../c2/0993/3303db163d8ff764b02af80ef1.svg" src="../../76/649c/3c50288c3d93a7e894e7da1745.png" width="23" height="23"/>
                    </svg>
                </li>
                <li>
                    <svg width="23" height="23">
                        <image xlink:href="../../ac/7ac6/d276bddbed64ffc7d083528c93.svg" src="../../00/a2ce/989d5038f87bdb37304f8961fd.png" width="23" height="23"/>
                    </svg>
                    <div class="up-drop">
                        <div class="up-menu">
                            <div class="foot-code">大鱼公众号</div>
                            <div><img src="../../dd/6781/6d76eaa6fcc7c0db1b2276d87c.png" alt=""></div>
                            <div class="foot-game">
                                <div>微信号</div>
                                <div>baomaogame</div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="foot-tip clearfix">
                <li class="li-1">
                    <div class="tip-drop">
                        <span class="tip-con">PAGCOR</span>
                    </div>
                </li>
                <li class="li-2">
                    <div class="tip-drop">
                        <span class="tip-con">GLI</span>
                    </div>
                </li>
                <li class="li-3">
                    <div class="tip-drop">
                        <span class="tip-con">CEZA</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="foot-bottom clearfix">
            <div class="foot-text">
                <div class="copy-right">
                    <span>© 2014 大鱼游戏版权所有</span>
                    <span class="approve">菲律宾政府PAGCOR博彩牌照认证</span>
                </div>
                <div>大鱼游戏郑重提示：彩票有风险，投注需谨慎，不向未满18周岁的青少年出售彩票</div>
            </div>
            <div class="foot-bottom-r">
                <img src="../../3f/d0f1/b954349a52d07ad3c1d12ce49c.png" alt="">
            </div>
        </div>
    </div>
</div>
<!--右侧固定栏-->
<ul class="jack-side">
    <li><a href="###"><img src="../../e6/ff20/45576c491f3963c18300f15481.png" width="28" height="26" alt=""><div class="online-server side-text">联系客服</div></a></li>
    <?php if(is_array($qq_json)): foreach($qq_json as $key=>$vo): ?><li><a href="tencent://message/?uin=<?php echo ($vo); ?>"><img src="../../22/2a72/c683af6e96dbf7015a8f940d66.png" height="26" alt=""><div class="side-text">联系主管</div></a></li><?php endforeach; endif; ?>
</ul>
<!--js文件-->
<script src="../../06/dd88/7cf0896b1896045412bc9e65ae.js"></script>
<script src="../../d1/c92d/7e6cf8fbd382890945f8e8a044.js"></script>
<script src="../../4c/46f8/111af9e28d45274685ed1e0db1.js"></script>
<script src="../../2a/f516/84bb93e0b61935a03c3a640966.js"></script>
<!--数字滚动效果-->
<script>
    $(function () {
        //今日实现兑奖
        var num1 = 1000000 + parseInt(10000*Math.random()); //初始化给随机数
        var numRun1 = $(".numberRun1").numberAnimate({num: num1, speed: 1000, symbol:","});
        setInterval(function () {
            num1 += parseInt(10000*Math.random());
            numRun1.resetData(num1);
        }, 20000);

        //在线人数
        var num2 = 5000 + parseInt(25*Math.random()-10); //初始化给随机数
        var numRun2 = $(".numberRun2").numberAnimate({num: num2, speed: 1000, symbol:","});
        setInterval(function () {
            num2 += parseInt(25*Math.random()-10);   //每次加的数字是-10到15的随机数
            if(num2 > 6000){
                num2 -= parseInt(25*Math.random()-10);   //每次加的数字是-15到10的随机数
            }
            else if(num2 < 5000){
                num2 += parseInt(25*Math.random()-10);   //每次加的数字是-10到15的随机数
            }
            numRun2.resetData(num2);
        }, 2000);

        var num3 = 10000000 + parseInt(10000*Math.random()); //初始化给随机数
        var numRun3 = $(".numberRun3").numberAnimate({num: num3, speed: 1000, symbol:","});
        setInterval(function () {
            num3 += parseInt(10000*Math.random());
            numRun3.resetData(num3);
        }, 2000);


        $(".online-server").on("click" , function(){
            var url = "https://vp9.live800.com/live800/chatClient/chatbox.jsp?companyID=80000041&configID=2085&codeType=custom&info=("+"userId=&loginname=&name=&hashCode=&amp;timestamp=1496218264" +")";
            window.open(url,"_blank","height=540,width=900,resizable=no");
        })
    });


</script>

</script>
</body>
</html>