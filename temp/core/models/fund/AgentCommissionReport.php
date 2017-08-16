<?php
class AgentCommissionReport extends BaseModel {

    protected $table = 'agent_commission_reports';
    public static $resourceName = 'AgentCommissionReport';
    public static $checkboxenable = true;
    const STATUS_NEW = 0;
    const STATUS_DISPLAYED = 1;
    const STATUS_SENT = 2;
    public static $aStatus = [
        self::STATUS_NEW => 'new',
        self::STATUS_DISPLAYED => 'displayed',
        self::STATUS_SENT => 'sent',
    ];

    protected $fillable = [
        'id',
        'period',
        'username',
        'bet',
        'win',
        'day_commission',
        'bonus',
        'fee',
        'profit',
        'accumulative_profit',
        'active_number',
        'rate',
        'commission',
        'status',
        'created_at',
        'updated_at'
    ];

    public static $rules = [
        'period' => 'required',
        'username' => 'required|max:16',
        'bet' => 'required',
        'win' => 'required',
        'day_commission' => 'required',
        'bonus' => 'required',
        'fee' => 'required',
        'profit' => 'required',
        'accumulative_profit' => 'required',
        'active_number' => 'required|integer',
        'rate' => 'required|integer',
        'commission' => 'required',
    ];

    public $orderColumns = [
        'id' => 'desc',
    ];
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];
    public static $viewColumnMaps = [
        'status' => 'formatted_status',
    ];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'period',
        'username',
        'bet',
        'win',
        'day_commission',
        'bonus',
        'fee',
        'profit',
        'accumulative_profit',
        'active_number',
        'rate',
        'commission',
        'status',
    ];

    protected function getFormattedStatusAttribute() {
        return __('_agentcommissionreport.' . strtolower(Str::slug(static::$aStatus[$this->attributes['status']])));
    }
}
