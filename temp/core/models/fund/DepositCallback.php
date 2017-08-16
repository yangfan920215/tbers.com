<?php

class DepositCallback extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deposit_callbacks';

    const DEPOSIT_STATUS_SUCCESS = 1;
    const DEPOSIT_STATUS_FAILURE = 1;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'pay_time',
        'bank_id',
        'mc_bank_id',
        'amount',
        'company_order_num',
        'mownecum_order_num',
        'pay_card_num',
        'pay_card_name',
        'channel',
        'area',
        'fee',
        'transaction_charge',
        'key',
        'deposit_mode',
    ];
    public static $resourceName = 'DepositCallback';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';
    public static $titleColumn = '';
    public static $rules = [
        'pay_time' => 'required|date',
        'bank_id' => ['required', 'integer'],
        'mc_bank_id' => ['required', 'integer'],
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'company_order_num' => 'required|between:1,64',
        'mownecum_order_num' => 'required|between:1,64',
        'pay_card_num' => 'regex:/^[0-9*]{16,32}$/',
        'pay_card_name' => '',
        'channel' => '',
        'area' => '',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'transaction_charge' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'key' => 'required|between:32,32',
        'deposit_mode' => 'required|in:1,2,3',
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)

    /**
     * 平台响应状态：失败
     */
    const RESPONSE_STATUS_FAIL = 0;

    /**
     * API响应状态：成功
     */
    const RESPONSE_STATUS_SUCCESS = 1;

    /**
     * 添加新记录，并返回实例
     * @param array $aInitData
     * @return DepositCallback
     */
    public static function createCallback(array $aInitData) {
        $oDepositCallback = new DepositCallback($aInitData);
        if (!$bSucc = $oDepositCallback->save()) {
//            pr($oDepositCallback->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oDepositCallback;
    }

    /**
     * 设置响应的状态为成功
     * @return boolean
     */
    public function setResponseSuccessful() {
        $this->status = self::RESPONSE_STATUS_SUCCESS;
        return $this->save();
    }

    /**
     * 设置响应的状态为失败
     * @param type $sMsg 附带失败信息
     * @return boolean
     */
    public function setResponseFailed($sMsg = '') {
        $this->error_msg = $sMsg;
        $this->status = self::RESPONSE_STATUS_FAIL;
        return $this->save();
    }


}
