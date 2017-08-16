<?php

class UserBetReport extends BaseModel {

    public static $resourceName = 'UserBetReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'username',
        'is_agent',
        'game_name_en',
        'game_name_cn',
        'game_code',
        'platform',
        'bet',
        'win',
        'profit',
        'progressive_bet',
        'progressive_win',
//        'bonus',
//        'day_commission',
//        'is_day_commission',
//        'status_day_commission',
    ];

    protected $table = 'user_bet_reports';


    protected $fillable = [
        'date',
        'username',
        'is_agent',
        'game_name_en',
        'game_name_cn',
        'game_code',
        'platform',
        'bet',
        'win',
        'profit',
        'progressive_bet',
        'progressive_win',
        'bonus',
        'day_commission',
        'is_day_commission',
        'status_day_commission',
    ];

    public $orderColumns = [
        'date' => 'desc'
    ];

    protected function getIsAgentAttribute()
    {
        return $this->attributes['is_agent'] ? '代理' : '玩家';
    }


    public static function getUgamesByUid($uid){
        return self::where('user_id', $uid)
            ->select(DB::raw('id, max(date) as date,game_name_cn,game_name_en,platform'))
            ->groupBy('game_code')
            ->orderBy('date', 'desc')
            ->limit(9)
            ->get();
    }
    // array('max(date) as date', 'game_name_cn', 'game_name_en')
}
