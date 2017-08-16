<?php

class GiftVoucher extends BaseModel {

    public static $resourceName = 'GiftVoucher';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'code',
        'name',
        'activity_rule_id',
        'status',
        'quantity',
        'used',
        'validity_period',
        'agents',
    ];

    public static $ignoreColumnsInView = [

    ];

    /**
     * the main param for index page
     * @var string
     */
//    public static $mainParamColumn = 'parent_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => '',
        'activity_rule_id' => 'required',
        'wallet' => '',
        'quantity' => '',
        'start' => '',
        'end' => '',
        'agent_id' => '',
        'status' => 'in:0,1',
        'note' => 'max:255',
        'agents' => 'max:255',
    ];
    protected $table = 'gift_vouchers';
    public static $htmlSelectColumns = [
        'activity_rule_id' => 'aRule',
        'wallet' => 'aWallets',
    ];



    protected $fillable = [
        'id',
        'code',
        'name',
        'activity_rule_id',
        'status',
        'quantity',
        'used',
        'start',
        'end',
        'agents',
        'wallet',
        'admin',
        'note',
        'created_at',
        'updated_at',
    ];

    protected function beforeValidate() {
        if (!$this->code) {
            $sCode = substr(md5('gv'.$this->id . time() . random_str(5)), 0, 8);
            $this->code = $sCode;
        }
        parent::beforeValidate();
    }

    protected function getValidityPeriodAttribute() {
        $sValidityPeriod = '长期';
        if ($this->end && $this->start) $sValidityPeriod = $this->start.'-'.$this->end;
        return $sValidityPeriod;
    }
}
