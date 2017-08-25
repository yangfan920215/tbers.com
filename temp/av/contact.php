<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 8/25/17
 * Time: 3:43 PM
 */
// 编码设置
header ( 'Content-Type:text/html;charset=utf-8' );

// 错误级别,-1开启全部错误,0关闭全部PHP错误报告
error_reporting ( -1 );
// error_reporting(0);

// 设置时区
date_default_timezone_set ( "Asia/Shanghai" );

// 加载配置文件
require ('./conf.php');

// 加载composer
require('./vendor/autoload.php');

require ('./libs/func.php');


