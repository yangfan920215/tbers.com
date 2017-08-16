<?php

class DayCommissionSetting extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'day_commission_setting';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = false; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'turnover_min',
        'commission_rate',
        'commission_max',
        'created_at',
        'updated_at'
            
    ];
     /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'turnover_min',
        'commission_rate',
        'commission_max',
        'created_at',
        'updated_at'
    ];

    public static $rules = [
        'turnover_min' => 'required|integer',
        'commission_rate' => 'required',
        'commission_max' => 'integer',
    ];

    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)
}
