<?php

class UserBetRecord extends BaseModel {

    public static $resourceName = 'UserBetRecord';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'user_id',
        'username',
        'platform',
        'bet',
        'win',
        'profit',
        'day_commission',
        'bonus',
        'net_profit',
        'status_day_commission',
    ];

    protected $table = 'user_bet_records';


    protected $fillable = [
        'date',
        'user_id',
        'username',
        'platform',
        'bet',
        'win',
        'profit',
        'day_commission',
        'bonus',
        'net_profit',
        'status_day_commission',
    ];
    public $orderColumns = [
        'date' => 'desc'
    ];

    protected function getNetProfitAttribute() {
        $fPreNetProfit = $this->attributes['net_profit'];
        $this->attributes['net_profit'] = $this->profit + $this->bonus + $this->day_commission;
        if ($fPreNetProfit != $this->attributes['net_profit'])
            $this->save();
        return $this->attributes['net_profit'];
    }

}
