<?php
/**
 * 增加日志
 * @param $log
 * @param bool $name
 */

function addlog($log, $name = false)
{
    $Model = M('log');
    if (!$name) {
        session_start();
        $uid = session('uid');
        if ($uid) {
            $user = M('member')->field('user')->where(array('uid' => $uid))->find();
            $data['name'] = $user['user'];
        } else {
            $data['name'] = '';
        }
    } else {
        $data['name'] = $name;
    }
    $data['t'] = time();
    $data['ip'] = $_SERVER["REMOTE_ADDR"];
    $data['log'] = $log;
    $Model->data($data)->add();
}


/**
 *
 * 获取用户信息
 *
 **/
function member($uid, $field = false)
{
    $model = M('Member');
    if ($field) {
        return $model->field($field)->where(array('uid' => $uid))->find();
    } else {
        return $model->where(array('uid' => $uid))->find();
    }
}

/**
 * 调式函数
 * @param $var
 */
if (!function_exists('_D')) {
    function _D($var){
        echo '<pre>';
        var_dump($var);
        exit;
    }
}

if (!function_exists('get_img_upload_filename')) {
    function get_img_upload_filename($param){
        addlog($param);
        return substr($param, 6);
    }
}

if (!function_exists('get_img_upload_basefile')) {
    function get_img_upload_basefile($param){
        addlog($param);
        return substr($param, 2, 4);
    }
}



if (!function_exists('get_img_upload_firesfile')) {
    function get_img_upload_firesfile($param){
        addlog($param);
        return substr($param, 0, 2);
    }
}

/**
 * 将以json形式存入的数据分解并入数组
 * @param $json
 * @param $arr
 * @return array
 */
if (!function_exists('_parseJson')) {
    function _parseJson($json, $arr)
    {
        $json_arr = json_decode($json, true);
        return is_array($json_arr) ? array_merge($json_arr, $arr) : $arr;
    }
}

if (!function_exists('mkdirs')) {
    function mkdirs($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdirs(dirname($dir))) {
                return FALSE;
            }
            if (!mkdir($dir, 0777)) {
                return FALSE;
            }
        }
        return TRUE;
    }
}