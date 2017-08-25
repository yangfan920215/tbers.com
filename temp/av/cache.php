<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 8/24/17
 * Time: 4:28 PM
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
require ('./libs/avgle.php');

$server  =  array (
    'host'      =>  '127.0.0.1' ,
    'port'      =>  6379 ,
) ;

$redis = new \Predis\Client($server);

// 开始获取全部电影类型信息进行缓存
$url = 'https://api.avgle.com/v1/categories';
$response = json_decode(file_get_contents($url), true);
if ($response['success']) {
    $categories = $response['response']['categories'];
    $redis->set('avgle_categories', json_encode($categories));
    echo '视频标题缓存更新成功!<br/>';
    // 更新索引信息视频

    $url = 'https://api.avgle.com/v1/videos/';
    $page = 0;
    $limit = '?limit=10';

    foreach ($categories as $category) {
        if (isset($category['CHID']) && isset($category['name'])){
            $query = '&c=' . $category['CHID'];
            $response = json_decode(file_get_contents($url . $page . $limit . $query), true);
            if ($response['success']) {
                $videos = $response['response']['videos'];
                $redis->set('avgle_chid_' . $category['CHID'], json_encode($videos));
                echo $category['name'] . '更新成功<br/>';
            }else{
                echo $category['name'] . '更新失败<br/>';
            }
        }
    }

    foreach (array_merge($tabs_name_30, $tabs_name_60, $tabs_name_100) as $name) {
        $url = 'https://api.avgle.com/v1/search/';
        $ret = file_get_contents($url . urlencode($name). '/0?limit=10');
        $response = json_decode($ret, true);
        if (isset($response['success']) && $response['success']) {
            $categories = $response['response']['videos'];
            $redis->set('avgle_name_' . urlencode($name), json_encode($categories));
            echo $name . '更新成功<br/>';
        }else{
            echo $name . '更新失败<br/>';
        }
    }

    echo '缓存更新完成';
}else{
    echo '视频标题缓存更新失败!<br/>';
    exit;
}