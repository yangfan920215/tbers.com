<?php

class UserProfit extends BaseModel {

    public static $resourceName = 'UserProfit';
    protected $table = 'user_profits';
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'user_id',
        'is_agent',
        'username',
        'parent_user_id',
        'parent_user',
        'deposit',
        'withdrawal',
        'bet',
        'win',
        'profit',
        'net_profit',
        'commission',
        'bonus',
    ];

    protected $fillable = [
        'id',
        'date',
        'user_id',
        'is_agent',
        'username',
        'parent_user_id',
        'parent_user',
        'deposit',
        'withdrawal',
        'bet',
        'win',
        'profit',
        'net_profit',
        'commission',
        'bonus',
        'created_at',
        'updated_at',
    ];

    public $orderColumns = [
        'date' => 'desc'
    ];
    const AGENT = 1;
    const CUSTOMER = 0;
        public static $validStatus = [
        self::AGENT => 'agent',
        self::CUSTOMER => 'customer',
    ];
        
    protected function getIsAgentAttribute()
    {
        if (isset(self::$validStatus[$this->attributes['is_agent']])) {
            self::comaileLangPack();
            return self::translate(self::$validStatus[$this->attributes['is_agent']]);
        }
        return;
    }
    
    public static function getAllUserAgent($iStart = null, $iEnd = null, $iUserName = null){
        $iPageSize = 15;
        $aColumns = ['*'];
        $oQuery =  self::where('is_agent', 1);
        if(!empty($iStart)){
                    $oQuery = $oQuery->where('date', '>=', $iStart);
        }
        if(!empty($iEnd)){
                    $oQuery = $oQuery->where('date', '<=', $iEnd);
        }
        if(!empty($iUserName)){
                    $oQuery = $oQuery->where('username', $iUserName);
        }
        $oQuery = $oQuery->groupBy('user_id');
        return $oQuery->paginate($iPageSize, $aColumns);
    }
    
    public static function getLsOrDepositByUserId($userId, $field){
        $iDate = date("Y-m-01", time());
        return self::where('date', '>=', $iDate)->where('user_id', $userId)->sum($field);
    }
    public static function getAllActiveUser($id){
        return self::where('parent_user_id', $id)->groupBy('user_id')->get();
    }
    
    public static function getAgentSumByUserId($aUserId, $field, $iStart = null, $iEnd = null){
        $oQuery =  self::wherein('user_id', $aUserId);
        if(!empty($iStart)){
                    $oQuery = $oQuery->where('date', '>=', $iStart);
        }
        if(!empty($iEnd)){
                    $oQuery = $oQuery->where('date', '<=', $iEnd);
        }
        return $oQuery->sum($field);
//        return self::where('date', '>=', $iStart)->where('date', '<=', $iEnd)->whereIn('user_id', $aUserId)->sum($field);
    }
    
    public static function getSumProfitByUserId($iUserId, $iStart = null, $iEnd = null){
        $sql = "select sum(profit) as sum_profit,platform from user_bet_records where  user_id in($iUserId)";
        if(!empty($iStart)){
            $sql .= " AND date >= '" . $iStart . "'";
        }
        if(!empty($iEnd)){
            $sql .= " AND date<= '". $iEnd ."'";
        }
        $sql .= " group by platform";
        return DB::select($sql);
    } 
    
    public static function getUserIdtById($id){
        return self::where('id', $id)->first();
    }
    
    public static function getAllLevelUserById($iUserId, $id, $iStart = null, $iEnd = null){
        $iPageSize = 15;
        $aColumns = ['*'];
        $oQuery = self::where('parent_user_id', $iUserId);
        if(!empty($iStart)){
                    $oQuery = $oQuery->where('date', '>=', $iStart);
        }
        if(!empty($iEnd)){
                    $oQuery = $oQuery->where('date', '<=', $iEnd);
        }
        $oQuery = $oQuery->orwhere('id', $id);
        $oQuery = $oQuery->groupBy('user_id')->orderBy('id', 'asc');
        return $oQuery->paginate($iPageSize,$aColumns);
    }
    
        public static function getAllUserAgents($iStart = null, $iEnd = null, $iUserName = null){
            $oQuery =  self::where('is_agent', 1);
            if(!empty($iStart)){
                        $oQuery = $oQuery->where('date', '>=', $iStart);
                        
            }
            if(!empty($iEnd)){
                        $oQuery = $oQuery->where('date', '<=', $iEnd);
            }
            if(!empty($iUserName)){
                        $oQuery = $oQuery->where('username', $iUserName);
            }
            $oQuery = $oQuery->groupBy('user_id');
            return $oQuery->get();
    }
    
      public static function getAgentSumProByUserId($aUserId, $field, $iStart = null, $iEnd = null){
        $oQuery = UserBetReport::wherein('user_id', $aUserId);
        if(!empty($iStart)){
                    $oQuery = $oQuery->where('date', '>=', $iStart);
        }
        if(!empty($iEnd)){
                    $oQuery = $oQuery->where('date', '<=', $iEnd);
        }
        return $oQuery->sum($field);
//        return self::where('date', '>=', $iStart)->where('date', '<=', $iEnd)->whereIn('user_id', $aUserId)->sum($field);
    }
    
    public static function getCountLowUserByUserId($iUserId){
        return User::where('parent_id', $iUserId)->count();
    }
    
    public static function getAllAgent($iUserName = null){
        $iPageSize = 15;
        $aColumns = ['*'];
        $oQuery = User::where('is_agent', 1);
        if(!empty($iUserName)){
            $oQuery = $oQuery->where('username', $iUserName);
        }
        return $oQuery->paginate($iPageSize,$aColumns);
    }
    
     public static function getAllLevelsUserById($id, $iStart = null, $iEnd = null){
        $iPageSize = 15;
        $aColumns = ['*'];
        $oQuery = User::where('parent_id', $id);
//        if(!empty($iStart)){
//                    $oQuery = $oQuery->where('date', '>=', $iStart);
//        }
//        if(!empty($iEnd)){
//                    $oQuery = $oQuery->where('date', '<=', $iEnd);
//        }
//        $oQuery = $oQuery->orwhere('id', $id);
//        $oQuery = $oQuery->groupBy('user_id')->orderBy('id', 'asc');
        return $oQuery->paginate($iPageSize,$aColumns);
    }
}
