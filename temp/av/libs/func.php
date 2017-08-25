<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 8/25/17
 * Time: 9:54 AM
 */

function sortByColumn(&$arr, $key){
    $keys = [];
    foreach ($arr as $k => $v) {
        $keys[$k] = $v[$key];
    }
    array_multisort($keys, SORT_DESC, $arr);
}

function createEmbedded_url(&$arr){
    if (isset($arr['embedded_url'])){
        $arr['embedded_url_path'] = parse_url($arr['embedded_url'])['path'];
    }else{
        foreach ($arr as &$v){
            $v['embedded_url_path'] = parse_url($v['embedded_url'])['path'];
        }
    }

}

function D($data, $is_ext = TRUE){
    echo '<pre>';
    print_r($data);
    if ($is_ext) {
        exit;
    }
}

function getHost(){
    $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? 'https' : 'http';
    return  $http . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"];
}
