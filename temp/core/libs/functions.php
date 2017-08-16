<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 6/14/17
 * Time: 1:17 PM
 */


/**
 * 返回与前端约定格式的数据
 * @param int $status
 * @param array $data
 * @param string $msg
 */
function retJson($status = 4, $data = array(), $msg = ''){
    return json_encode(array(
        'status'=>$status,
        'msg'=>$msg,
        'data'=>$data,
    ), true);
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

/**
 * 翻译
 * @param string $key
 * @param array $replace
 * @param int $uc_type      1: 首字母大写； 2：全部单词首字母大写；3：先将slug格式转换为自然语言格式，再全部单词首字母大写
 * @param string $locale    语言代码
 * @return string
 */
function __($key, $replace = array(), $uc_type = 1, $locale = null) {
//    $pre = 'transfer.';
    !empty($replace) or $replace = [];
    $aKeyParts = explode('.', $key);
    if (count($aKeyParts) > 1) {
        list($sFile, $sKey) = $aKeyParts;
    } else {
        $sFile = '_basic';
        $sKey = $aKeyParts[0];
        $key = $sFile . '.' . $sKey;
    }
    $key = strtolower($key);
    $str = \Illuminate\Support\Facades\Lang::get($key, $replace, $locale);
    $str != $key or $str = $sKey;
    if ($uc_type > 0) {
        switch ($uc_type) {
            case 1:
                $str = ucfirst($str);
                break;
            case 2:
                $str = ucwords($str);
                break;
            case 3:
                $str = String::humenlize($str);
                $str = ucwords($str);
        }
//        $function = $uc_type == 1 ? 'ucfirst' : 'ucwords';
//        $str = $function($str);
    }
//    $str = Str::slug($str);
    return $str;
}

/**
 * trim数组中的每个value
 * @param $array
 * @return array
 */
function trimArray($array) {
    $data = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $data[$key] = trimArray($value);
        } else {
            $data[$key] = (trim($value));
        }
    }
    return $data;
}

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
 * 获取客户的IP
 * @return string
 */
function get_client_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        file_put_contents('/tmp/ip.log', "HTTP_CLIENT_IP:" . $_SERVER['HTTP_CLIENT_IP'] . "\n", FILE_APPEND);
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        file_put_contents('/tmp/ip.log', "HTTP_X_FORWARDED_FOR:" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "\n", FILE_APPEND);
    }
    if (isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER'])) {
        file_put_contents('/tmp/ip.log', "HTTP_PROXY_USER:" . $_SERVER['HTTP_PROXY_USER'] . "\n", FILE_APPEND);
    }
    if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
        file_put_contents('/tmp/ip.log', "REMOTE_ADDR:" . $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);
    }
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
    }
    if (isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER'])) {
        return $_SERVER['HTTP_PROXY_USER'];
    }
    if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return "0.0.0.0";
    }
}

/**
 * 格式化数字
 * @param $number
 * @param int $decimal
 * @return string
 */
function formatNumber($number, $decimal = 4) {
    $number = str_replace(',', '', $number);
    return number_format($number, $decimal, '.', '');
}

if ( ! function_exists('app_path'))
{
    /**
     * Get the path to the application folder.
     *
     * @param   string  $path
     * @return  string
     */
    function app_path($path = '')
    {
        return app('path').($path ? '/'.$path : $path);
    }
}


function getReport($sPlatforms, $sBgn, $sEnd, $sUsers, $sGames, $sGroups, $sFile){
    $sBgn = date('Y-m-d%20H:i:00', strtotime($sBgn));
    $sEnd = date('Y-m-d%20H:i:00', strtotime($sEnd));
    $sUrl = 'http://10.6.21.71:8000/apiex?platforms='.$sPlatforms.'&bgn='.$sBgn.'&end='.$sEnd.'&users='.$sUsers.'&games='.$sGames.'&groups='.$sGroups;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//    $aHttpInfo = curl_getinfo($ch);
    $output = curl_exec($ch);
    curl_close($ch);

    @file_put_contents('/tmp/'.$sFile.date('Ymd'), "\n\r url : \n\r".$sUrl."\n\r output : \n\r".$output."\n\r", FILE_APPEND);
    return json_decode($output, true);
}

function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}