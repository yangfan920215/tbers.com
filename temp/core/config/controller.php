<?php
return [
    /**
     * Index控制器部分
     */
    'index'=>array(
        // 域名
        'dom'=>'http://www.qupaimobile.com',

        // 客服链接
        'customerLink' => 'https://vp9.live800.com/live800/chatClient/chatbox.jsp?companyID=80000041&configID=2084&codeType=custom&info=("+"userId=&loginname=&name=&hashCode=&amp;timestamp=1499045055" +")',
        // PC端找回密码页面
        'resetpasswordLink' => 'http://www.qupai.lgv/#resetpassword',
        // 显示信息
        'showMsg' => '开户礼金8-88元',
        // 轮播图路径
        'banner_ing'=>array(
            '/assets/images/banner/banner1.png',
            '/assets/images/banner/banner2.png',
            '/assets/images/banner/banner3.png'
        ),
        //
        'games_file'=>'/assets/images/gameicons/',
        'default_img_file'=>'default.jpg',
        // 游戏渠道渲染所需信息
        'agents' => array(
            'img_file'=>'/assets/images/agents/',
            'agents'=>array(
                array(
                    'id'=>'pt',
                    'name'=>'PT游戏',
                    'img_name'=>'game-1.png',
                ),
                array(
                    'id'=>'mg',
                    'name'=>'MG游戏',
                    'img_name'=>'game-2.png',
                ),
                array(
                    'id'=>'hb',
                    'name'=>'HB游戏',
                    'img_name'=>'game-3.png',
                ),
                array(
                    'id'=>'ttg',
                    'name'=>'TTG游戏',
                    'img_name'=>'game-4.png',
                ),
                array(
                    'id'=>'gos,prg',
                    'name'=>'其他游戏',
                    'img_name'=>'game-5.png',
                ),
            ),
        ),
    ),


    /**
     * Wallet控制器部分
     */
    'wallet'=>array(
        'minCash'=>10,
        'maxCash'=>50,
        'weixin_min_cash'=>10,
        'weixin_max_cash'=>1000,
        'name'=>array(
            1=>'主钱包',
            2=>'pt钱包',
            3=>'im钱包',
            4=>'ag钱包',
            5=>'hb钱包',
            6=>'mg钱包',
        ),
    ),
];