<?php

class Profit extends BaseModel {

    public static $resourceName = 'Profit';
    protected $table = 'profits';
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'bet',
        'win',
        'progressive_win',
        'day_commission',
        'bonus',
        'net_profit',
        'profitability',
        'deposit',
        'withdraw',
        'deposit_withdraw_diff',
    ];

    protected $fillable = [
        'id',
        'date',
        'bet',
        'win',
        'progressive_win',
        'day_commission',
        'bonus',
        'net_profit',
        'profitability',
        'deposit',
        'withdraw',
        'deposit_withdraw_diff',
        'created_at',
        'updated_at',
    ];

    public $orderColumns = [
        'date' => 'desc'
    ];
}
