<?php

/**
 * 用户返点表
 *
 * @author wallace
 */
class CommissionReport extends BaseModel {

    protected $table = 'commission_reports';
    public static $resourceName = 'CommissionReport';

    protected $fillable = [
        'id',
        'user_id',
        'month',
        'bet',
        'win',
        'day_commission',
        'bonus',
        'fee',
        'net_profit',
        'accumulated_net_profit',
        'active_number',
        'bonus_rate',
        'bonus_amount',
        'created_at',
        'updated_at'
    ];

    public static $rules = [

    ];

    public $orderColumns = [
        'date' => 'desc',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'month',
        'bet',
        'win',
        'day_commission',
        'bonus',
        'fee',
        'net_profit',
        'accumulated_net_profit',
        'active_number',
        'bonus_rate',
        'bonus_amount',
    ];
}
