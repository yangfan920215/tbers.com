<?php

class ActivityRule extends BaseModel {

    public static $resourceName = 'ActivityRule';

    const TYPE_ELSE = 0;
    const TYPE_FIRST_DEPOSIT = 1;
    const TYPE_SECOND_DEPOSIT = 2;

    public static $aTypes = [
        self::TYPE_ELSE => '其他',
        self::TYPE_FIRST_DEPOSIT => '首存',
        self::TYPE_SECOND_DEPOSIT => '再存',
    ];

    const LIMITATION_FACTORS_VERIFIED_PHONE = 1;
    const LIMITATION_FACTORS_VERIFIED_EMAIL = 2;
    const LIMITATION_FACTORS_BIND_CARD = 3;
    public static $aLimitationFactors = [
        self::LIMITATION_FACTORS_VERIFIED_PHONE => '验证手机',
        self::LIMITATION_FACTORS_VERIFIED_EMAIL => '验证邮箱',
        self::LIMITATION_FACTORS_BIND_CARD => '绑定银行卡',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'amount_min',
        'rate',
        'bonus_max',
        'multiple',
    ];

    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = [];

    public static $titleColumn = 'name';
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => '',
        'amount_min' => '',
        'rate' => '',
        'bonus_max' => '',
        'multiple' => '',
        'limitation_factor' => '',
    ];
    protected $table = 'activity_rules';
    public static $htmlSelectColumns = [
        'limitation_factor' => 'aLimitationFactors'
    ];



    protected $fillable = [
        'id',
        'name',
        'amount_min',
        'rate',
        'bonus_max',
        'multiple',
        'limitation_factor',
        'created_at',
        'updated_at',
    ];
}
