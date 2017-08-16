<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserPtProfit extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_pt_profit';
    protected $softDelete = true;
    protected $fillable = [

    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserPtProfit';

    public static function getLastWeekLoss($iPlatform, $iUserId){
        $dLastMonday = date('Y-m-d', strtotime('-1 week last monday'));
        $dLastSunday = date('Y-m-d', strtotime('last sunday'));
        $aConditions = [
            'user_id' => ['=', $iUserId],
            'platform' => ['=', $iPlatform],
            'date' => ['between', [$dLastMonday, $dLastSunday]],
        ];
        $fProfit = self::doWhere($aConditions)->sum('profit');
        return $fProfit < 0 ? $fProfit : 0;
    }
}
