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

// 加载配置文件
require ('./conf.php');

// 加载composer
require('./vendor/autoload.php');

require ('./libs/func.php');

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

if (mb_substr($urls['path'], 0, 7) === '/single'){
    $av = mb_substr($urls['path'], 8);
    $urls['path'] = '/single';
}

if (mb_substr($urls['path'], 0, 8) === '/archive'){
    $archive_key = mb_substr($urls['path'], 9);
    $urls['path'] = '/archive';
}

$tpl = './tpl' . $urls['path'] . '.tpl';

$ret = file_exists($tpl);

$file = $ret ? $tpl : './tpl/index.tpl';

// 页面渲染
$data = [];

$server = array (
    'host'      =>  '127.0.0.1' ,
    'port'      =>  6379 ,
) ;
$redis = new \Predis\Client($server);

$data['categories'] = json_decode($redis->get('avgle_categories'), true);
$data['hotVideos'] = json_decode($redis->get('avgle_chid_' . $head), true);
sortByColumn($data['hotVideos'],  'viewnumber');

switch ($file){
    case './tpl/index.tpl':
        $jrVideos = json_decode($redis->get('avgle_chid_' . $juru), true);
        sortByColumn($jrVideos,  'viewnumber');
        createEmbedded_url($jrVideos);

        for ($x = 0; $x < 5; $x++) {
            createEmbedded_url($jrVideos[$x]);
        }
        $smarty->assign('jrVideos0', $jrVideos[0]);
        $smarty->assign('jrVideos1', $jrVideos[1]);
        $smarty->assign('jrVideos2', $jrVideos[2]);
        $smarty->assign('jrVideos3', $jrVideos[3]);
        $smarty->assign('jrVideos4', $jrVideos[4]);


        // 特别推荐
        $pornstarVideos = json_decode($redis->get('avgle_chid_' . $pornstar), true);
        sortByColumn($pornstarVideos,  'viewnumber');
        createEmbedded_url($pornstarVideos);

        $pornstarVideos0 = [];
        $pornstarVideos1 = [];
        $pornstarVideos00 = [];
        $pornstarVideos11 = [];

        foreach ($pornstarVideos as $key => $pornstarVideo) {
            $pornstarVideo['keyword'] = strlen($pornstarVideo['keyword']) > 10 ? mb_substr($pornstarVideo['keyword'], 0, 10) : $pornstarVideo['keyword'];


            if ($key < 8){
                if ($key == 0){
                    $pornstarVideos0 = $pornstarVideo;
                    continue;
                }

                if ($key == 1){
                    $pornstarVideos1 = $pornstarVideo;
                    continue;
                }

                if (in_array($key, [2, 4, 6])){
                    $pornstarVideos00[] = $pornstarVideo;
                }else{
                    $pornstarVideos11[] = $pornstarVideo;
                }
                continue;
            }
            break;
        }
        $smarty->assign('pornstarVideos0', $pornstarVideos0);
        $smarty->assign('pornstarVideos1', $pornstarVideos1);
        $smarty->assign('pornstarVideos00', $pornstarVideos00);
        $smarty->assign('pornstarVideos11', $pornstarVideos11);


        // 女优推荐
        $schoolVideos = json_decode($redis->get('avgle_chid_' . $school), true);
        sortByColumn($schoolVideos,  'viewnumber');
        createEmbedded_url($schoolVideos);

        $schoolVideos0 = [];
        $schoolVideos1 = [];
        $schoolVideos2 = [];
        foreach ($schoolVideos as $key => &$schoolVideo) {
            $schoolVideo['keyword'] = strlen($schoolVideo['keyword']) > 10 ? mb_substr($schoolVideo['keyword'], 0, 10) : $schoolVideo['keyword'];

            if ($key < 6){
                if (in_array($key, [0, 1])){
                    $schoolVideos0[] = $schoolVideo;
                }elseif(in_array($key, [2, 3])){
                    $schoolVideos1[] = $schoolVideo;
                }else{
                    $schoolVideos2[] = $schoolVideo;
                }
                continue;
            }
            break;
        }
        $smarty->assign('schoolVideos0', $schoolVideos0);
        $smarty->assign('schoolVideos1', $schoolVideos1);
        $smarty->assign('schoolVideos2', $schoolVideos2);


        // 热门视频
        $smlVideos = json_decode($redis->get('avgle_chid_' . $sm), true);
        sortByColumn($smlVideos,  'viewnumber');
        createEmbedded_url($smlVideos);

        $smVideos0 = [];
        $smVideos1 = [];

        foreach ($smlVideos as $key => &$smlVideo) {
            $smlVideo['keyword'] = strlen($smlVideo['keyword']) > 10 ? mb_substr($smlVideo['keyword'], 0, 10) : $smlVideo['keyword'];

            if ($key < 5){
                if ($key == 0){
                    $smVideos0 = $smlVideo;
                }else{
                    $smVideos1[] = $smlVideo;
                }
                continue;
            }
            break;
        }

        $smarty->assign('smVideos0', $smVideos0);
        $smarty->assign('smVideos1', $smVideos1);

        // cosplay
        $cosplayVideos = json_decode($redis->get('avgle_chid_' . $cosplay), true);
        sortByColumn($cosplayVideos,  'viewnumber');
        createEmbedded_url($cosplayVideos);

        $cosplayVideos = array_slice($cosplayVideos, 0 , 3);

        $smarty->assign('cosplayVideos', $cosplayVideos);
        break;
    case './tpl/archive.tpl':

        $data['searchs'] = json_decode($redis->get('avgle_name_' . $archive_key), true);

        sortByColumn($data['searchs'],  'viewnumber');
        createEmbedded_url($data['searchs']);

        foreach ($data['searchs'] as &$search) {
            $search['addtime'] = date('Y-m-d H:i:s', $search['addtime']);
        }

        $smarty->assign('searchs', $data['searchs']);

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


createEmbedded_url($data['hotVideos']);

// 轮播热门视频
$smarty->assign('hotVideos', $data['hotVideos']);
// 女优推荐
$data['categories0'] = $tabs_name_30;
$data['categories1'] = $tabs_name_60;
$data['categories2'] = $tabs_name_100;

$data['categories01'] = [];
$data['categories02'] = [];
$data['categories03'] = [];

$data['categories11'] = [];
$data['categories12'] = [];
$data['categories13'] = [];

$data['categories21'] = [];
$data['categories22'] = [];
$data['categories23'] = [];

$k = 1;
foreach ($data['categories0'] as $category) {
    $data['categories0' . $k][] = [
        'name' => $category,
        'decode' => urlencode($category)
    ];
    $k++;
    $k = $k > 3 ? 0 : $k;
}
$k = 1;
foreach ($data['categories1'] as $category) {
    $data['categories1' . $k][] = [
        'name' => $category,
        'decode' => urlencode($category)
    ];
    $k++;
    $k = $k > 3 ? 0 : $k;
}
$k = 1;
foreach ($data['categories2'] as $category) {
    $data['categories2' . $k][] = [
        'name' => $category,
        'decode' => urlencode($category)
    ];
    $k++;
    $k = $k > 3 ? 0 : $k;
}
// 视频类型
$smarty->assign('categories01', $data['categories01']);
$smarty->assign('categories02', $data['categories02']);
$smarty->assign('categories03', $data['categories03']);
$smarty->assign('categories11', $data['categories11']);
$smarty->assign('categories12', $data['categories12']);
$smarty->assign('categories13', $data['categories13']);
$smarty->assign('categories21', $data['categories21']);
$smarty->assign('categories22', $data['categories22']);
$smarty->assign('categories23', $data['categories23']);


// sider推荐女优
$data['tabs_sidebar'] = [];
foreach ($tabs_sidebar as $tab_sidebar) {
    $data['tabs_sidebar'][] = [
        'name' => $tab_sidebar,
        'decode' => urlencode($tab_sidebar)
    ];
}

// sider 人妻
$wifeVideos = json_decode($redis->get('avgle_chid_' . $wife), true);
sortByColumn($wifeVideos,  'viewnumber');
createEmbedded_url($wifeVideos);
foreach ($wifeVideos as $key => &$wifeVideo) {
    $wifeVideo['keyword'] = strlen($wifeVideo['keyword']) > 5 ? mb_substr($wifeVideo['keyword'], 0, 5) : $wifeVideo['keyword'];
}
$wifeVideos = array_slice($wifeVideos , 0, 3);


$smarty->assign('wifeVideos', $wifeVideos);

$smarty->assign('tabs_sidebar', $data['tabs_sidebar']);
$smarty->assign('host', getHost());
$smarty->display($file);