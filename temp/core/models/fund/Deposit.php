<?php

class Deposit extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deposits';

    const NOTE_MODEL_SELF = 1;
    const NOTE_MODEL_MC = 2;
    const NOTE_MODEL_SDPAY = 3;
    //手续费成功
    const IS_DEDUCT_FEE_SUCCESS = 1;
    //手续费失败
    const IS_DEDUCT_FEE_FAIL = 2;
    //手续费未充值
    const IS_DEDUCT_FEE_WAITTING = 0;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'user_id',
        'username',
        'top_agent',
        'bank_id',
        'amount',
        'company_order_num',
        'deposit_mode',
        'web_url',
        'note',
        'note_model',
        'mownecum_order_num',
        'collection_bank_id',
        'accept_card_num',
        'accept_email',
        'accept_acc_name',
        'real_amount',
        'fee',
        'pay_time',
        'accept_bank_address',
        'status',
        'error_msg',
        'mode',
        'break_url',
        'mc_token',
        'is_deduct_fee',
        'pay_mode'
    ];
    public static $resourceName = 'Deposit';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'status',
        'username',
        'top_agent',
        'created_at',
        'amount',
        'bank_id',
        'company_order_num',
        'mownecum_order_num',
        'pay_time',
        'real_amount',
        'note',
        'fee',
        'deposit_mode',
        'updated_at',
         'pay_mode'
    ];
    public static $htmlNumberColumns = [
        'amount' => 2,
        'real_amount' => 2,
        'fee' => 2,
    ];
    public static $totalColumns = [
        'amount',
        'real_amount',
        'fee',
    ];
    public static $viewColumnMaps = [
        'amount' => 'amount_formatted',
        'real_amount' => 'real_amount_formatted',
        'fee' => 'fee_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'validStatuses',
        'bank_id' => 'aBanks',
        'deposit_mode' => 'aDepositMode',
        'note_model' => 'aNoteMode',
        'is_deduct_fee' => 'aDeductFee',
    ];
    public static $noOrderByColumns = [
        'updated_at'
    ];
    public static $aDeductFee=[
        '0'=>'no_fee',
        '1'=>'successful',
        '2'=>'fail',
    ];

    /**
     * API: 充值请求
     * @var int
     */
    const DEPOSIT_API_REQUEST = 1;

    /**
     * API: 充值响应
     * @var int
     */
    const DEPOSIT_API_RESPONSE = 2;

    /**
     * API: 充值确认
     * @var int
     */
    const DEPOSIT_API_APPROVE = 3;

    /**
     * 充值渠道：银行卡
     * @var int
     */
    const DEPOSIT_MODE_BANK_CARD = 1;

    /**
     * 充值渠道：第三方
     * @var int
     */
    const DEPOSIT_MODE_THIRD_PART = 2;

    /**
     * 充值渠道：二维码
     * @var int
     */
    const DEPOSIT_MODE_QRCODE = 3;

    /**
     * 充值渠道：sdpay
     * @var int
     */
    const DEPOSIT_MODE_SDPAY = 4;

    /**
     * 附言模式：平台自有附言
     * @var int
     */
    const DEPOSIT_NOTE_MODE_SELF = 1;

    /**
     * 附言模式：Mownecum附言
     * @var int
     */
    const DEPOSIT_NOTE_MODE_MOW = 2;

    /**
     * 状态：未处理（新订单）
     * @var int
     */
    const DEPOSIT_STATUS_NEW = 0;

    /**
     * 状态：申请成功
     * @var int
     */
    const DEPOSIT_STATUS_RECEIVED = 1;

    /**
     * 状态：申请失败（包括发起申请未收到响应）
     * @var int
     */
    const DEPOSIT_STATUS_REFUSED = 2;

    /**
     * 状态：成功
     * @var int
     */
    const DEPOSIT_STATUS_SUCCESS = 3;

    /**
     * 状态：加游戏币失败
     * @var int
     */
    const DEPOSIT_STATUS_ADD_FAIL = 4;
/**
     * 状态：充值失败
     * @var int
     */
    const DEPOSIT_STATUS_DEPOSIT_FAIL = 5;
/**
 * SPDAY 配置
 */
    const SDPAYREQUESTURL="https://sdpay.officenewline.org:21103/ToService.aspx";
    const SDPAYMERCHANTID = "BM9794";
    const SDPAYREQUESTCMD='6006';
    const SDPAYRESPOSNCMD='60071';
    const SDPAYCALLBACKCMD='6007';
    const SDPAYUNIT='1';
    const SDPAYLANGUAGE='zh-cn';
    const SDPAYBACKURL= "http://sdpay-jyjw6nemd8.bomao.com/mc-api/type=sdpayAddTransfer";
    const SDPAYKEY1="RtBBeV9KIlU=";
    const SDPAYKEY2="KOHatfK97YY=";
    const SDPAYKEY3= "c02fee0287044aaf966ca4c57e53cab1";
    
    public static $validStatuses = [
        self::DEPOSIT_STATUS_NEW => 'New',
        self::DEPOSIT_STATUS_RECEIVED => 'apply-received',
        self::DEPOSIT_STATUS_REFUSED => 'apply-refused',
        self::DEPOSIT_STATUS_SUCCESS => 'Success',
        self::DEPOSIT_STATUS_ADD_FAIL => 'add-failture',
        self::DEPOSIT_STATUS_DEPOSIT_FAIL=>'deposit-failture',
    ];
    public static $aDepositMode = [
        self::DEPOSIT_MODE_BANK_CARD => 'bank-card',
        self::DEPOSIT_MODE_THIRD_PART => 'the-third-part',
        self::DEPOSIT_MODE_QRCODE => 'qrcode',
        self::DEPOSIT_MODE_SDPAY => 'sdpay',
    ];
    public static $aNoteMode = [
        self::NOTE_MODEL_SELF => 'self note',
        self::NOTE_MODEL_MC => 'mc note',
        self::NOTE_MODEL_SDPAY=>'sdpay',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc',
    ];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'deposit_mode' => 'formatted_deposit_mode',
        'is_tester' => 'friendly_is_tester',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required|between:1,50',
        'top_agent' => 'between:1,50',
        'bank_id' => 'required|integer',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'company_order_num' => 'between:1,64',
        'deposit_mode' => 'in:1,2,3,4',
        //'web_url' => '',
        'note' => 'between:1,32',
        'note_model' => 'in:1,2',
        'mownecum_order_num' => 'between:1,50',
        'collection_bank_id' => 'integer',
        'accept_card_num' => 'numeric',
        'accept_email' => 'between:1,200',
        'accept_acc_name' => 'between:1,19',
        'real_amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'pay_time' => 'date',
        'accept_bank_address' => 'between:1,100',
        'status' => 'in:0,1,2,3,4,5',
        'is_deduct_fee'=>'in:0,1,2',
        'error_msg' => 'between:1,255',
        'mode' => 'in:0,1,2',
        'break_url' => 'between:1,1000',
        'mc_token' => 'between:32,32',
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = ['mode'];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)

    /**
     * 添加新订单，并返回该订单实例
     * @param array $aInitData
     * @return \Deposit
     */

    public static function createDeposit(array $aInitData) {
        $oDeposit = new Deposit($aInitData);
        if (!$bSucc = $oDeposit->save()) {
//            pr($oDeposit->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oDeposit;
    }

    /**
     * [getAccountHiddenAttribute 访问器方法, 生成只显示末尾4位的银行卡帐号信息, 且每4位空格隔开]
     * @return [String]          [只显示末尾4位的银行卡帐号信息,且每4位空格隔开]
     */
    protected function getAccountHiddenAttribute() {
        $str = str_repeat('*', (strlen($this->account) - 4));
        $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($this->account, -4);
        return $account_hidden;
    }

    protected function beforeValidate() {
//        if (!$this->exists) {
//            $this->status = self::DEPOSIT_STATUS_NEW;
//            $this->request_time = Carbon::now()->toDateTimeString();
//        }
//        $this->transaction_charge or $this->transaction_charge = 0;
//        $this->transaction_amount or $this->transaction_amount = 0;

        return parent::beforeValidate();
    }

    /**
     * [countTransactionCharge 计算手续费 ]
     * @param  [type] $iAmount [description]
     * @return [type]          [description]
     */
    public function countTransactionCharge($iAmount) {
        // TODO
        return 0;
    }

    /**
     * _updateStatus 更新提现记录状态
     * @param  Int $iToStatus   将要改变的状态值
     * @param  Array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    private function _updateStatus($iToStatus, array $aExtraData = []) {
        // 以下是状态流
        // 0 => 1,2
        // 1 => 3,4
        if (!$this->exists) {
            return FALSE;
        }
        if (!empty($aExtraData) && is_array($aExtraData)) {
            $this->fill($aExtraData);
        }
        
        $aExtraData['status'] = $iToStatus;
        $iAffectRows = self::where('id', '=', $this->id)->where('status', '=', $this->status)->update($aExtraData);
        $iAffectRows == 1 && $this->status = $iToStatus;
//        pr($this->validationErrors);
        return $iAffectRows == 1;
    }
       /**
     * _updateStatus 更新提现手续费状态
     * @param  Int $iToStatus   将要改变的状态值
     * @param  Array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    private function _updatesetDeductFeeStatus($iToStatus, array $aExtraData = []) {
        // 以下是状态流
        // 0 => 1,2
        // 1 => 3,4
        
        if (!$this->exists) {
            return FALSE;
        }
        if (!empty($aExtraData) && is_array($aExtraData)) {
            $this->fill($aExtraData);
        }
        
        $aExtraData['is_deduct_fee'] = $iToStatus;
        $iAffectRows = self::where('id', '=', $this->id)->where('is_deduct_fee', '=', $this->is_deduct_fee)->update($aExtraData);
        $iAffectRows == 1 && $this->is_deduct_fee = $iToStatus;
//        pr($this->validationErrors);
        return $iAffectRows == 1;
    }

    /**
     * 设置状态：订单申请成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setReceived(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_NEW && $this->_updateStatus(self::DEPOSIT_STATUS_RECEIVED, $aExtraData);
    }

    /**
     * 设置状态：订单申请失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setRefused(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_NEW && $this->_updateStatus(self::DEPOSIT_STATUS_REFUSED, $aExtraData);
    }

    /**
     * 设置状态：订单完成，充值成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setSuccess(array $aExtraData = []) {
//        echo $this->status;exit;
        return $this->status == self::DEPOSIT_STATUS_RECEIVED && $this->_updateStatus(self::DEPOSIT_STATUS_SUCCESS, $aExtraData);
    }

  /**
     * 设置状态：充值失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setDepositFail(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_RECEIVED && $this->_updateStatus(self::DEPOSIT_STATUS_DEPOSIT_FAIL, $aExtraData);
    }
    /**
     * 设置状态：添加游戏币失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setAddFail(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_RECEIVED && $this->_updateStatus(self::DEPOSIT_STATUS_ADD_FAIL, $aExtraData);
    }
    /**
     * 设置状态：手续费失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setDeductFeeFail(array $aExtraData = []) {
        return $this->is_deduct_fee == self::IS_DEDUCT_FEE_WAITTING && $this->_updatesetDeductFeeStatus(self::IS_DEDUCT_FEE_FAIL, $aExtraData);
    }

    /**
     * 设置状态：手续费成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setDeductFeeSuccess(array $aExtraData = []) {
        return $this->is_deduct_fee == self::IS_DEDUCT_FEE_WAITTING && $this->_updatesetDeductFeeStatus(self::IS_DEDUCT_FEE_SUCCESS, $aExtraData);
    }
    
    
    /**
     * 用平台订单号获取订单对象
     * @param string $sCompanyOrderNum 平台订单号
     * @return Deposit|null
     */
    public static function findDepositByCompanyOrderNum($sCompanyOrderNum) {
        return Deposit::firstByAttributes(['company_order_num' => $sCompanyOrderNum]);
    }

    public static function getDepositAmountByDate($sBeginDate, $sEndDate, $iUserId) {
        $oQuery = self::where('user_id', '=', $iUserId);
        if (!is_null($sBeginDate)) {
            $oQuery->where('created_at', '>=', $sBeginDate);
        }
        if (!is_null($sEndDate)) {
            $oQuery->where('created_at', '<=', $sEndDate);
        }
        $oQuery->where('status', '=', self::DEPOSIT_STATUS_SUCCESS);
        $aUserProfits = $oQuery->get(['real_amount']);
        $data = [];
        $i = 0;
        foreach ($aUserProfits as $oUserProfit) {
            $data[$i]['real_amount'] = $oUserProfit->real_amount;
            $i++;
        }
        return $data;
    }

    public static function getTotalAmountByDate($sBeginDate, $sEndDate, $iUserId) {
        $aUserDeposits = self::getDepositAmountByDate($sBeginDate, $sEndDate, $iUserId);
        $aTotalDeposits = [];
        foreach ($aUserDeposits as $data) {
            $aTotalDeposits[] = $data['real_amount'];
        }
        $fTotalDeposit = array_sum($aTotalDeposits);
        return $fTotalDeposit;
    }

    /**
     * 生成通讯加密串
     * @param array $aPostData 通信数据包
     * @param int   $iType 通信类型
     * @return string|FALSE
     */
    public static function getApiKey($aPostData, $iType) {
        if (empty($aPostData) || !is_array($aPostData)) {
            return FALSE;
        }
        $aKeyRule = []; // 加密串拼接顺序
        switch ($iType) {
            case self::DEPOSIT_API_REQUEST: //充值
                $aKeyRule = [
                    'company_id', 'bank_id', 'amount', 'company_order_num', 'company_user',
                    'estimated_payment_bank', 'deposit_mode', 'group_id', 'web_url', 'memo', 'note', 'note_model'
                ];
                break;
            case self::DEPOSIT_API_RESPONSE: //充值响应
                $aKeyRule = [
                    'bank_card_num', 'bank_acc_name', 'amount', 'email', 'company_order_num',
                    'datetime', 'note', 'mownecum_order_num', 'status', 'mode',
                    'issuing_bank_address', 'break_url', 'deposit_mode', 'collection_bank_id'
                ];
                break;
            case self::DEPOSIT_API_APPROVE: //充值确认
                $aKeyRule = [
                    'pay_time', 'bank_id', 'amount', 'company_order_num',
                    'mownecum_order_num', 'pay_card_num', 'pay_card_name', 'channel',
                    'area', 'fee', 'transaction_charge', 'deposit_mode'
                ];
                break;
            default :
                return FALSE;
        }
        $sDataStr = '';
        foreach ($aKeyRule as $v) {
            $sDataStr .= array_get($aPostData, $v, '');
        }
        $oSysConfig = new SysConfig;
        $sKey = $oSysConfig->readValue('mc_company_key');
        return md5(md5($sKey) . $sDataStr);
    }

    /**
     * 向任务队列追加充值额统计任务
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @return bool
     */
    public static function addProfitTask($sDate, $iUserId, $fAmount, $type = 'deposit') {
        $aTaskData = [
            'type' => $type,
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => substr($sDate, 0, 10),
        ];
        return true;
        //return BaseTask::addTask('StatUpdateProfit', $aTaskData, 'stat');
    }

    /**
     * [getSerialNumberShortAttribute 获取序列号的截断格式]
     * @return [type] [4位序列号的截断格式]
     */
    protected function getCompanyOrderNumShortAttribute() {
        return substr($this->company_order_num, 0, 4) . '...';
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedStatusAttribute() {
        return __('_deposit.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedDepositModeAttribute() {
        return __('_deposit.' . strtolower(Str::slug(static::$aDepositMode[$this->attributes['deposit_mode']])));
    }

    protected function getAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    protected function getFeetFormattedAttribute() {
        return $this->getFormattedNumberForHtml('fee');
    }

    protected function getRealAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('real_amount');
    }

//    protected function getFriendlyIsTesterAttribute() {
//        return yes_no(intval($this->is_tester));
//    }

}
