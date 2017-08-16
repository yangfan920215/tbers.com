<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 6/21/17
 * Time: 3:05 PM
 */

namespace libs;

use Illuminate\Support\Facades\Redis;

class accessToken {

    // 用户保存在redis里的信息key
    private $key;

    // 授权令牌
    private $token;

    // 用户名
    private $uid;

    // 用户信息
    private $user;

    // 有效时间
    private $time = 7200;

    // 密钥
    private $secret = 'anvo!@#$';

    // 分隔符
    private static $separator = '_';

    // 登录key前缀
    private static $login_prefix = 'accessToken';

    // 用户信息
    public static $session;

    public function __construct($uid, $user = '')
    {
        $this->uid = $uid;

        if ($user != ''){
            $this->user = json_encode($user, true);
        }

        // 设置用户授权令牌
        $this->setToken();

        // 设置redis访问key
        $this->setKey();
    }

    /**
     * 设置该用户的授权令牌
     * @param $login_prefix
     */
    private function setToken(){
        $this->token = md5($this->uid . $this->secret . date('Y-m-d H:i:s') . rand());
    }

    /**
     * 设置该用户在redis中的存储key
     * @param $login_prefix
     */
    private function setKey(){
        $this->key = self::$login_prefix . self::$separator . $this->token;
    }

    /**
     * 设置token
     */
    public function UserLogin(){
        if (Redis::exists($this->key)){
            return true;
        }

        return Redis::setex($this->key, $this->time, $this->user);
    }

    public function getKey(){
        return $this->key;
    }


    public function getToken(){
        return $this->token;
    }

    /**
     * 判断用户是否登录
     */
    public function checkLogin(){
        return Redis::exists($this->key);
    }

    public static function checkToken($token){
        self::$session = json_decode(Redis::get(self::$login_prefix . self::$separator . $token), true);
        return Redis::get(self::$login_prefix . self::$separator . $token);
    }

/*  public function getUser($key){
        $val = self::checkToken($this->token);
        $arr = json_decode($val);
        return isset($arr[$key]) ? $arr[$key] : false;
    }*/
}