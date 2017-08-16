<?php

class FundFlow extends BaseModel {
    protected $table = 'fund_flows';
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'description',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'available',
        'frozen',
        'withdrawable',
    ];

    public static $resourceName = 'FundFlow';
    
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'description',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'available',
        'frozen',
        'withdrawable',
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'total' => 'aValidActions',
        'balance' => 'aValidActions',
        'pt' => 'aValidActions',
        'im' => 'aValidActions',
        'ag' => 'aValidActions',
        'hb' => 'aValidActions',
        'mg' => 'aValidActions',
        'available' => 'aValidActions',
        'frozen' => 'aValidActions',
        'withdrawable' => 'aValidActions',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $titleColumn = 'description';
    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    public static $rules = [
        'description'   => 'required|max:20',
        'total'         => 'in:-1,0,1',
        'balance'         => 'in:-1,0,1',
        'pt'         => 'in:-1,0,1',
        'im'         => 'in:-1,0,1',
        'ag'         => 'in:-1,0,1',
        'hb'         => 'in:-1,0,1',
        'mg'         => 'in:-1,0,1',
        'available'     => 'in:-1,0,1',
        'frozen'     => 'in:-1,0,1',
        'withdrawable'  => 'in:-1,0,1',
    ];

    /**
     * 可用的资金增减操作
     * @var array 
     */
    public static $validActions = [
        0   => 'Fixedly',
        1   => 'Plus',
        -1  => 'Minus',
    ];
    
}