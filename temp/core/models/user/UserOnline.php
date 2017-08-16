<?php
/**
 * Created by PhpStorm.
 * User: royal
 * Date: 15-5-13
 * Time: 下午4:44
 */
use libs\accessToken;
// use Config;

class UserOnline extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_NONE;

    protected $table = 'user_onlines';

    protected $fillable = [
        'user_id',
        'session_id',
        'expires_time',
    ];

    public $timestamps = true;
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserOnline';


    /**
     * 用户下线
     * @param $userId
     * @return mixed
     */
    public static function offline($userId)
    {
        $oUser = self::firstOrCreate(['user_id' => $userId]);
        $accessToken = new accessToken($userId);
        $oUser->session_id = $accessToken->getKey();
        $oUser->expires_time = time() - 1;
        return $oUser->save();
    }

    /**
     * 用户上线
     * @param $userId
     * @return bool
     */
    public static function  online($userId)
    {
        $oUser = self::firstOrCreate(['user_id' => $userId]);
        $accessToken = new accessToken($userId);
        $oUser->session_id = $accessToken->getKey();
        $oUser->expires_time = time() + config('session.lifetime') * 60;
        return $oUser->save();
    }

    /**
     * 用户是否在线
     * @param $userId
     * @return bool
     */
    public static function isOnline($userId){
        if($oUser = self::where('user_id', '=', $userId)->first()){
            if($oUser->expires_time > time()){
                return true;
            }
        }
        return false;
    }

    public static function getListByUserIds($aUserIds = null) {
        if (!$aUserIds || !count($aUserIds)) return [];
        $oUserOnlines = UserOnline::whereIn('user_id', $aUserIds)->get();
        $data = [];
        foreach ($oUserOnlines as $key => $oUserOnline) {
            $data[$oUserOnline->user_id] = intval(time() < $oUserOnline->expires_time); // status 1: online, 0: offline
        }
        return $data;
    }

    /**
     * 团队的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamOnlineNum($fatherId)
    {
        $onlineNum = 0;

        if ($aUserIds = User::getAllUsersBelongsToAgent($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     * 团队：代理的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamAgentOnlineNum($fatherId)
    {
        $onlineNum = 0;

        if ($aUserIds = User::getTeamAgentUserIds($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     * 团队：玩家的在线数
     * @param $fatherId
     * @return int
     */
    public static function getTeamPlayerOnlineNum($fatherId)
    {
        $onlineNum = 0;

        if ($aUserIds = User::getTeamPlayerUserIds($fatherId)) {
            $onlineNum = self::where('expires_time', '>', time())->whereIn('user_id', $aUserIds)->count();
        }

        return $onlineNum;
    }

    /**
     *直属玩家数
     * @param $fatherId
     * @return int
     */
    public static function getDirectPlayerOnlineNum($fatherId)
    {
        //todo
    }

    /**
     *直属代理数
     * @param $fatherId
     * @return int
     */
    public static function getDirectAgentOnlineNum($fatherId)
    {
        //todo
    }


}