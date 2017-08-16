<?php

/**
 * 用户返点表
 *
 * @author wallace
 */
class UserDayCommission extends BaseModel {

    protected $table = 'user_day_commissions';
    public static $resourceName = 'UserDayCommission';

    protected $fillable = [
        'date',
        'user_id',
        'username',
        'status',
        'total',
        'commission',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'rate',
    ];

    public static $rules = [
        'date' => 'required:date',
        'user_id' => 'integer',
        'username' => 'required',
        'status' => 'in:0,1,2',
        'commission' => '',
        'pt' => '',
        'im' => '',
        'ag' => '',
        'hb' => '',
        'mg' => '',
    ];

    public $orderColumns = [
        'date' => 'desc',
        'status' => 'asc',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'user_id',
        'username',
        'status',
        'commission',
    ];

    public static $ignoreColumnsInEdit = ['date', 'user_id', 'username', 'status', 'commission_type'];

    public static $htmlSelectColumns = [
        'commission_type' => 'aCommissionType',
        'status' => 'aStatus', // 0:审核中, 1: 审核通过, 2: 审核拒绝, 3: 已派发
    ];

    const STATUS_WAITING_AUDIT = 0;
    const STATUS_AUDIT_FINISH = 1;
    const STATUS_AUDIT_REJECT = 2;
    const STATUS_BONUS_SENT = 3;

    public static $aStatus = [
        self::STATUS_WAITING_AUDIT => 'waiting audit',
        self::STATUS_AUDIT_FINISH => 'audited',
        self::STATUS_AUDIT_REJECT => 'rejected',
        self::STATUS_BONUS_SENT => 'sent',
    ];

    protected function getFriendlyStatusAttribute() {
        return __('_userdaycommission.' . self::$aStatus[$this->status]);
    }

    public function changeStatus($iFromStatus, $iToStatus) {
        $aExtraData['status'] = $iToStatus;
        $bSucc = self::where('id', '=', $this->id)->where('status', '=', $iFromStatus)->update($aExtraData);
        return $bSucc;
    }

    public function setToSent(){
        $aConditions = [
            'id'     => ['=',$this->id],
            'status' => ['=',self::STATUS_WAITING_AUDIT],
        ];
        $data        = [
            'status'  => self::STATUS_BONUS_SENT
        ];
        return $this->doWhere($aConditions)->update($data) > 0;
    }

    public function beforeValidate(){
        file_put_contents('/tmp/day-commission-edit', '['.date('Y-m-d H:i:s').'] admin : '.Session::get('admin_username').' before : '.$this->getOriginal('commission').' after : '.$this->commission."\n\r", FILE_APPEND);
        return parent::beforeValidate();
    }
}
