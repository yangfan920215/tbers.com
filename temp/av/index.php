<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 8/18/17
 * Time: 11:17 AM
 */

// 编码设置
header ( 'Content-Type:text/html;charset=utf-8' );

// 错误级别,-1开启全部错误,0关闭全部PHP错误报告
error_reporting ( -1 );
// error_reporting(0);

// 设置时区
date_default_timezone_set ( "Asia/Shanghai" );

// 加载composer
require('./vendor/autoload.php');

// smarty模板渲染
$smarty = new Smarty;

//$smarty->force_compile = true;
$smarty->debugging = true;
$smarty->caching = false;
$smarty->cache_lifetime = 1;
$smarty->left_delimiter = '<{';
$smarty->right_delimiter = '}>';

$smarty->setCacheDir('./static/cache');
$smarty->setConfigDir('./static/configs');
$smarty->setPluginsDir('./static/plugins');
$smarty->setTemplateDir('./static/templates');
$smarty->setCompileDir('./static/templates_c');

// 判断入口
$urls = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


if (substr($urls['path'], 0, 7) === '/single'){
    $av = substr($urls['path'], 8);
    $urls['path'] = '/single';
}

$tpl = './tpl' . $urls['path'] . '.tpl';

$ret = file_exists($tpl);

$file = $ret ? $tpl : './tpl/index.tpl';

switch ($file){
    case './tpl/index.tpl':
        $client = new  GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://av.app/',
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);

        try {
            // 获取数据
            $response = $client->get('http://core.app/avgleinit');
            $data = json_decode($response->getBody(), true);
            $data = $data['data'];

        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            echo (string) $e->getResponse()->getBody();
            exit;
        }
        break;
    case './tpl/contact.tpl':
        $client = new  GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://av.app/',
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);

        try {
            // 获取数据
            $response = $client->get('http://core.app/avgleinit');
            $data = json_decode($response->getBody(), true);
            $data = $data['data'];

        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            echo (string) $e->getResponse()->getBody();
            exit;
        }
        break;
    case './tpl/archive.tpl':
        $client = new  GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://av.app/',
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);

        try {
            // 获取数据
            $key = isset($_GET['key']) ? $_GET['key'] : '三上悠亜';
            $response = $client->get('http://core.app/archive?key=' . $key);
            $data = json_decode($response->getBody(), true);
            $data = $data['data'];

            foreach ($data['searchs'] as &$search) {
                $search['addtime'] = date('Y-m-d H:i:s', $search['addtime']);
            }

            $smarty->assign('searchs', $data['searchs']);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            echo (string) $e->getResponse()->getBody();
            exit;
        }
        break;
    case './tpl/single.tpl':
        $client = new  GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://av.app/',
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);

        try {
            // 获取数据
            $response = $client->get('http://core.app/avgleinit');
            $data = json_decode($response->getBody(), true);
            $data = $data['data'];

            $smarty->assign('av', $av);
        } catch (GuzzleHttp\Exception\GuzzleException $e) {
            echo (string) $e->getResponse()->getBody();
            exit;
        }

        $video_url = isset($_GET['video']) ? $_GET['video'] : header('./index');
        break;
}



foreach ($data['hotVideos'] as &$hotVideo){
    $hotVideo['embedded_url_path'] = parse_url($hotVideo['embedded_url'])['path'];
}

// 轮播热门视频
$smarty->assign('hotVideos', $data['hotVideos']);
// 女优推荐
$smarty->assign('actresses_names', $data['actresses_names']);

$data['categories0'] = [];
$data['categories1'] = [];
$data['categories2'] = [];

$k = 0;
foreach ($data['categories'] as $category) {
    $data['categories' . $k][] = $category;
    $k++;
    $k = $k > 2 ? 0 : $k;
}

// 视频类型
$smarty->assign('categories0', $data['categories0']);
$smarty->assign('categories1', $data['categories1']);
$smarty->assign('categories2', $data['categories2']);

$smarty->display($file);