<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 6/25/17
 * Time: 10:09 AM
 */
/**
 * 调试函数
 * @param mixd $data
 * @param string $is_ext
 */
function D($data, $is_ext = TRUE){
    echo '<pre>';
    print_r($data);
    if ($is_ext) {
        exit;
    }
}


/**
 * 将配置表数组转化为select组建所需数据格式
 * @param array $config
 * @param $key
 * @param $val
 * @return array
 */
function toSelect(array $config, $key, $val){
    $select = array();
    foreach ($config as $item) {
        if (isset($item[$key]) && isset($item[$val])){
            $select[$item[$key]] = $item[$val];
        }
    }
    return $select;
}