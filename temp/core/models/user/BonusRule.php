<?php

class BonusRule extends BaseModel {
   
    protected $table = 'bonus_rule';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'AuditList';

    public static $columnForList = [
        'active_user_num',
        'net_profit',
        'bonus_rate',
    ];

  

    protected $fillable = [
           'active_user_num',
        'net_profit',
        'bonus_rate',
    ];
    public static $rules = [

    ];
    public $orderColumns = [
        'id' => 'asc'
    ];

   public static function getAllBonusRule(){
       return self::get();
   }

   public static function getRate($iActiveUserNum, $fNetProfit){
        if ($fNetProfit <= 0 || $iActiveUserNum < 5) return 0;
       $aBonusRules = self::where('active_user_num', '<=', $iActiveUserNum)->orderBy('net_profit', 'desc')->get();
       foreach ($aBonusRules as $oBonusRule) {
           if ($fNetProfit >= $oBonusRule->net_profit) return $oBonusRule->bonus_rate;
       }
       return 0;
   }
}
