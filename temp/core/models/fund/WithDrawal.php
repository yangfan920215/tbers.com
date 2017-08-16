<?php
use Carbon\Carbon;
class Withdrawal extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdrawals';
    public static $amountAccuracy = 2;
    public static $htmlNumberColumns = [
        'amount' => 2,
        'transaction_amount' => 2,
        'transaction_charge' => 2,
    ];
    public static $totalColumns = [
        'amount',
        'transaction_charge',
        'transaction_amount',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'serial_number',
        'mownecum_order_num',
        'user_id',
        'username',
        'user_forefather_ids',
        'request_time',
        'amount',
        'is_large',
        'account',
        'account_name',
        'province',
        'bank_id',
        'bank',
        'branch',
        'branch_address',
        'error_msg',
        'remark',
        'status',
        'locker_id',
        'locker',
        'auditor_id',
        'auditor',
        'verified_time',
        'finish_time',
        'transaction_charge',
        'transaction_amount',
        'mc_request_time',
        'mc_confirm_time',
        'is_sdpay',
        'claim_at',
    ];
    // const WITHDRAWAL_LIMIT_PER_DAY = 30;

    public static $resourceName = 'Withdrawal';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'user_forefather_ids',
        'request_time',
        'amount',
        'is_large',
        'account',
        'account_name',
        'bank',
        'province',
        'branch',
        'locker',
        'auditor',
        'verified_time',
//        'transaction_charge',
        'transaction_amount',
        'status',
        'serial_number',
        'mownecum_order_num',
        'mc_request_time',
        'mc_confirm_time',
        'is_sdpay',
        'claim_at',
    ];
    public static $listColumnMaps = [
        'amount' => 'formatted_amount',
        'transaction_charge' => 'formatted_transaction_charge',
        'transaction_amount' => 'formatted_transaction_amount',
        'status' => 'formatted_status',
        'user_forefather_ids' => 'user_forefather_ids_formatted',
        'is_sdpay' => 'formatted_is_sdpay',
    ];
    public static $viewColumnMaps = [
        'amount' => 'formatted_amount',
        'transaction_charge' => 'formatted_transaction_charge',
        'transaction_amount' => 'formatted_transaction_amount',
        'status' => 'formatted_status',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'validStatuses',
        'is_sdpay' => 'validChannels',
    ];

    const WITHDRAWAL_STATUS_NEW = 0; // 待审核
    const WITHDRAWAL_STATUS_CLAIMED = 12; // claim
    const WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM = 1; // 审核待定
    const WITHDRAWAL_STATUS_VERIFIED = 2; // 审核通过，待处理
    const WITHDRAWAL_STATUS_REFUSE = 3; // 未通过审核（审核拒绝）
    const WITHDRAWAL_STATUS_SUCCESS = 4; // 成功
    const WITHDRAWAL_STATUS_FAIL = 5; // 失败
    const WITHDRAWAL_STATUS_DEDUCT_FAIL = 6; // 扣游戏币失败
    const WITHDRAWAL_STATUS_PART = 7; // mc部分成功，扣减部分游戏币
    const WITHDRAWAL_STATUS_REFUND = 8; // 已退游戏币（审核拒绝情况下）
    const WITHDRAWAL_STATUS_MC_PROCESSING = 9; // MC处理中
    const WITHDRAWAL_STATUS_MC_ERROR_RETURN = 10; // MC异常返回
    const WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL = 11; // MC提现失败

    const WITHDRAWAL_CHANNEL_DASHPAY = 0; // 待审核
    const WITHDRAWAL_CHANNEL_SDPAY = 1; // claim
    const WITHDRAWAL_CHANNEL_TONGHUIKA = 2; // 审核待定
    const WITHDRAWAL_CHANNEL_LEFU = 3;
    
    public static $applyCanChangeStatus = [
        self::WITHDRAWAL_STATUS_NEW
    ];
    public static $manualCanChangeStatus = [
        self::WITHDRAWAL_STATUS_VERIFIED
    ];

    public static function getManualCanChangeStatus($is_sdpay) {
        if ($is_sdpay) {
            return[
                self::WITHDRAWAL_STATUS_MC_PROCESSING,
                self::WITHDRAWAL_STATUS_VERIFIED,
                self::WITHDRAWAL_STATUS_MC_ERROR_RETURN,
            ];
        }
        return [self::WITHDRAWAL_STATUS_VERIFIED];
    }

    public static $statusWorkFlow = [
        '0' => [1, 12],
        '1' => [12, 2, 3],
        '2' => [4, 5, 6, 7, 8, 9, 10, 11],
        '3' => [5, 8],
        '4' => [],
        '5' => [],
        '6' => [],
        '7' => [],
        '8' => [],
        '9' => [],
        '10' => [],
        '12' => [1, 2, 3],
    ];
    public static $validStatuses = [
        self::WITHDRAWAL_STATUS_NEW => 'New',
        self::WITHDRAWAL_STATUS_CLAIMED => 'Claimed',
        self::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM => 'Waiting-For-Confirmation',
        self::WITHDRAWAL_STATUS_VERIFIED => 'Verified',
        self::WITHDRAWAL_STATUS_REFUSE => 'Rejected',
        self::WITHDRAWAL_STATUS_SUCCESS => 'Success',
        self::WITHDRAWAL_STATUS_FAIL => 'Failed',
        self::WITHDRAWAL_STATUS_DEDUCT_FAIL => 'Deduct-Failture',
        self::WITHDRAWAL_STATUS_PART => 'Part-Success',
        self::WITHDRAWAL_STATUS_REFUND => 'Refund',
        self::WITHDRAWAL_STATUS_MC_PROCESSING => 'MC-processing',
        self::WITHDRAWAL_STATUS_MC_ERROR_RETURN => 'MC-error-return',
        self::WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL => 'MC-create-withdrawal-failed',
    ];
    // 除审核通过及其后续状态外的其他状态
    public static $unVerifiedStatus = [
        self::WITHDRAWAL_STATUS_NEW,
        self::WITHDRAWAL_STATUS_CLAIMED,
        self::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM,
        self::WITHDRAWAL_STATUS_REFUSE,
        self::WITHDRAWAL_STATUS_REFUND,
    ];
    // 审核通过及其后续状态
    public static $verifiedStatus = [
        self::WITHDRAWAL_STATUS_VERIFIED,
        self::WITHDRAWAL_STATUS_SUCCESS,
        self::WITHDRAWAL_STATUS_FAIL,
        self::WITHDRAWAL_STATUS_DEDUCT_FAIL,
        self::WITHDRAWAL_STATUS_PART,
        self::WITHDRAWAL_STATUS_REFUND,
        self::WITHDRAWAL_STATUS_MC_PROCESSING,
        self::WITHDRAWAL_STATUS_MC_ERROR_RETURN,
    ];

    public static $validChannels = [
        self::WITHDRAWAL_CHANNEL_DASHPAY => 'dashpay',
        self::WITHDRAWAL_CHANNEL_SDPAY => 'sdpay',
        self::WITHDRAWAL_CHANNEL_TONGHUIKA => 'tonghuika',
        self::WITHDRAWAL_CHANNEL_LEFU => 'lefu',
    ];

    // 用某银行卡提现时，需要延后x个小时
    const WITHDRAWAL_TIME_LIMIT = 2;

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $rules = [
        'user_id' => 'required|integer',
        'serial_number' => 'between:1,25',
        'mownecum_order_num' => 'between:1,50',
        'request_time' => 'required|date',
        'amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'is_large' => 'in:0,1',
        'is_sdpay' => 'in:0,1,2,3',
        'province' => 'required|between:1,20',
        'branch' => 'required|between:1,50',
        'branch_address' => 'between:1,100',
        // 'account_id'      => 'required|integer',
        'account_name' => 'required|between:1,20',
        'account' => 'required|numeric',
        'bank_id' => 'required|integer',
        // 'bank'            => 'required|max:50',
        'error_msg' => 'max:50',
        'remark' => 'max:50',
        // 'status'          => 'in:0,1,2,3,4,5,6,7,8',
        'locker_id' => 'integer',
        'locker' => 'between:4,16',
        'auditor_id' => 'integer',
        'auditor' => 'between:4,16',
        'verified_time' => 'date',
        'finish_time' => 'date',
        'transaction_charge' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'transaction_amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = ['bank', 'username'];
    // 表单只读字段
    public static $aReadonlyInputs = ['province', 'branch', 'branch_address', 'account_name'];
    public static $ignoreColumnsInView = ['id', 'user_id', 'bank_id', 'locker_id', 'auditor_id'];
    public static $ignoreColumnsInEdit = ['serial_number', 'request_time', 'finish_time', 'error_msg', 'remark', 'status', 'auditor_id', 'auditor', 'verified_time']; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)

    /**
     * [getAccountHiddenAttribute 访问器方法, 生成只显示末尾4位的银行卡帐号信息, 且每4位空格隔开]
     * @return [String]          [只显示末尾4位的银行卡帐号信息,且每4位空格隔开]
     */

    protected function getAccountHiddenAttribute() {
        $str = str_repeat('*', (strlen($this->account) - 4));
        $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($this->account, -4);
        return $account_hidden;
    }

    /**
     * [getSerialNumberShortAttribute 获取序列号的截断格式]
     * @return [type] [4位序列号的截断格式]
     */
    protected function getSerialNumberShortAttribute() {
        return substr($this->serial_number, 0, 4) . '...';
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedStatusAttribute() {
        return __('_withdrawal.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    protected function getFormattedIsSdpayAttribute() {
        return __('_withdrawal.' . strtolower(Str::slug(static::$validChannels[$this->attributes['is_sdpay']])));
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的金额]
     * @return [type] [格式化后的金额]
     */
    protected function getFormattedAmountAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的实际提现金额]
     * @return [type] [格式化后的实际提现金额]
     */
    protected function getFormattedTransactionAmountAttribute() {
        return $this->getFormattedNumberForHtml('transaction_amount');
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的手续费]
     * @return [type] [格式化后的手续费]
     */
    protected function getFormattedTransactionChargeAttribute() {
        return $this->getFormattedNumberForHtml('transaction_charge');
    }

    protected function beforeValidate() {
        if (!$this->exists) {
            $this->status = self::WITHDRAWAL_STATUS_NEW;
            $this->request_time = Carbon::now()->toDateTimeString();
        }
        $this->transaction_charge or $this->transaction_charge = 0;
        $this->transaction_amount or $this->transaction_amount = 0;

        return parent::beforeValidate();
    }

    /**
     * [_updateStatus 更新提现记录状态]
     * @param  [Int] $iFromStatus [未改变前的状态值, 默认为待审核状态]
     * @param  [Int] $iToStatus   [将要改变的状态值]
     * @param  [Array] $aExtraData  [额外需要更新的数据]
     * @return [Response]              [description]
     */
    private function _updateStatus($iFromStatus = self::WITHDRAWAL_STATUS_NEW, $iToStatus, array $aExtraData = []) {
        if (!$this->exists) {
            return false;
        }
        // TODO 判断从fromStatus到toStatus是否符合提现流程
        if (!$bSucc = $this->judgeProcess($iFromStatus, $iToStatus)) {
            return false;
        }
        $aExtraData['status'] = $iToStatus;
        $bSucc = $this->update($aExtraData);
        return $bSucc;
    }

    /**
     * [judgeProcess 判断提现流程是否符合]
     * @param  [Integer] $iFromStatus [未改变前的状态值, 默认为待审核状态]
     * @param  [Integer] $iToStatus   [将要改变的状态值]
     * @return [Boolean]              [是否符合流程]
     */
    private function judgeProcess($iFromStatus, $iToStatus) {
        return in_array($iToStatus, self::$statusWorkFlow[$iFromStatus]);
    }

    public function setToClaimed($iStatus, $aExtraData = []) {
        return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_CLAIMED, $aExtraData);
    }

    /**
     * [setToWaitingForConfirmation 设置状态为客服待定]
     */
    public function setToWaitingForConfirmation($iStatus, $aExtraData = []) {
        return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM, $aExtraData);
    }

    /**
     * [setToVirified 设置状态为审核通过，待处理]
     * @param [Integer] $iStatus    [当前状态值]
     * @param [Array] $aExtraData [额外的参数]
     */
    public function setToVirified($iStatus, $aExtraData = []) {
        return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_VERIFIED, $aExtraData);
    }

    /**
     * [setToRejection 设置状态为未通过审核（审核拒绝）]
     * @param [Integer] $iStatus    [当前状态值]
     * @param [String] $sMsg       [拒绝备注]
     * @param [Array] $aExtraData [额外的参数]
     */
    public function setToRejection($iStatus, $aExtraData = [], $account_info = []) {
        $o_account = $account_info['oAccount'];
        $o_user = $account_info['oUser'];
        $amount_freeze = $account_info['amount'];
        $succ = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
        if ($succ == Transaction::ERRNO_CREATE_SUCCESSFUL) {
            return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_REFUSE, $aExtraData);
        }
        return false;
    }

    /**
     * [setToSuccess 设置状态为成功]
     */
    public function setToSuccess() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_SUCCESS);
    }

    /**
     * [setToFailture 设置状态为失败]
     */
    public function setToFailture() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_FAIL);
    }

    /**
     * [setToDeductFailture 设置状态为扣游戏币失败]
     */
    public function setToDeductFailture() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_DEDUCT_FAIL);
    }

    /**
     * [setToPartSuccess 设置状态为mc部分成功，扣减部分游戏币]
     */
    public function setToPartSuccess() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_PART);
    }

    /**
     * [setToPartSuccess 设置状态为已退款（审核拒绝情况下）]
     */
    public function setToRefund() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_REFUSE, self::WITHDRAWAL_STATUS_REFUND);
    }

    /**
     * 向任务队列追加提现额统计任务
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @return bool
     */
    public static function addProfitTask($sDate, $iUserId, $fAmount) {
        $aTaskData = [
            'type' => 'withdrawal',
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => substr($sDate, 0, 10),
        ];
        return true;
        //return BaseTask::addTask('StatUpdateProfit', $aTaskData, 'stat');
    }

    /**
     * [getTranslateValidStatus 翻译后的提现状态]
     * @return [Array] [提现状态]
     */
    public static function getTranslateValidStatus($iVerified = null) {
        $aValidStatuses = [];
        $aStatuses = [];
        if (!is_null($iVerified)) {
            if ($iVerified == 1) {
                $aStatuses = self::$verifiedStatus;
            } else if ($iVerified == 2) {
                $aStatuses = self::$unVerifiedStatus;
            }
        } else {
            $aStatuses = array_keys(self::$validStatuses);
        }
        // pr($aStatuses);exit;

        foreach ($aStatuses as $key => $value) {
            $sDesc = self::$validStatuses[$value];
            $aValidStatuses[$value] = __('_withdrawal.' . strtolower($sDesc));
        }
        return $aValidStatuses;
    }

    public static function findDepositBySerialNumberNum($sSerialNumber) {
        return self::firstByAttributes(['serial_number' => $sSerialNumber]);
    }

    protected function getUserForefatherIdsFormattedAttribute() {
        if ($this->user_forefather_ids) {
            $aIds = explode(',', $this->user_forefather_ids);
            $user = User::find($aIds[0]);
            if (is_object($user)) {
                return $user->username;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function isSDPay() {
        $amount = $this->amount;

        $aSettingString = json_decode(SysConfig::readValue('payment_amount_setting')); //从后台配置中获取设置

        $SDPayAmountSettingValue = $aSettingString[0]->sdpay_amount_max; //SDPay金额上限

        $DashPayAmountSettingValue = $aSettingString[1]->dashpay_amount_max; //DashPay金额上限

        $SDPaySortSettingValue = $aSettingString[0]->sort; //SDPay序号

        $DashPaySortSettingValue = $aSettingString[1]->sort; //DashPay序号

        if ($amount > $SDPayAmountSettingValue) {
            return false;
        } elseif ($amount < $SDPayAmountSettingValue) {
            return true;
        } elseif ($amount = $SDPayAmountSettingValue) {
            if ($SDPaySortSettingValue > $DashPaySortSettingValue) {
                return false;
            } else {
                return true;
            }
        }
    }

}
