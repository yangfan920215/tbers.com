<?php
use libs\accessToken;
use Carbon\Carbon;

class UserWithdrawal extends Withdrawal {

    const ERRNO_WITHDRAWAL_SUCCESS = -2401;
    const ERRNO_WITHDRAWAL_FAILED = -2402;


    public static $columnForList = [
        'serial_number',
        'request_time',
        // 'account',
        'account_name',
        'amount',
        'transaction_charge',
        'transaction_amount',
        'status',
        'claim_at'
    ];
    // protected $fillable = [
    //     'id',
    //     'serial_number',
    //     'user_id',
    //     'username',
    //     'request_time',
    //     'amount',
    //     'is_large',
    //     'account',
    //     'account_name',
    //     'province',
    //     'bank_id',
    //     'bank',
    //     'branch',
    //     'branch_address',
    //     'error_msg',
    //     'status',
    //     'auditor_id',
    //     'auditor',
    //     'verified_time',
    //     'transaction_charge',
    //     'transaction_amount',
    // ];

    protected $isAdmin = false;

    public static $customMessages = [
        'amount.regex'             => '提现金额必须是数字，且只保留2位小数',
        'province.required'        => '该银行卡所属省份信息缺失',
        'province.between'         => '该银行卡所属省份长度有误，请输入 :min - :max 位字符',
        'branch.required'          => '该银行卡的开户行名称缺失',
        'branch.between'           => '该银行卡的开户行名称长度有误，请输入 :min - :max 位字符',
        'branch_address.between'   => '该银行卡支行地址长度有误，请输入 :min - :max 位字符',
        'account_name.required'    => '该银行卡的开户人信息缺失',
        'account_name.between'     => '该银行卡的开户人信息长度有误，请输入 :min - :max 位数字',
        'account.required'         => '该银行卡帐号信息缺失',
        'account.numeric'          => '该银行卡帐号必须为数字',
        'transaction_charge.regex' => '手续费必须是数字，且只保留2位小数',
        'transaction_amount.regex' => '实际提现金额必须是数字，且只保留2位小数',
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
        // foreach(self::$validStatuses as $key => $value) {
        //     $aValidStatuses[$key] = __('_withdrawal.' . strtolower($value));
        // }
        return $aValidStatuses;
    }
    // const WITHDRAWAL_INFO    = 0;
    // const WITHDRAWAL_CONFIRM = 1;
    const IS_LARGE_AMOUNT    = 50000; // TODO 大额提现的判断标准，待定

    public static function getWithdrawalNumPerDay($user_id)
    {
        $sNow = date('Y-m-d');
        $iNum = UserWithdrawal::where('user_id', '=', $user_id)->where('request_time', 'like', date('Y-m-d') . '%')->count();
        return $iNum;
    }
    /**
     * [generateSerialNumber 序列号生成器]
     * @return [String] [序列号]
     * we will change six year later
     */
    public function generateSerialNumber()
    {
        $year_code = array('A','B','C','D','E','F','G','H','I','J');
        $order_sn = $year_code[intval(date('Y'))-2010].
        strtoupper(dechex(date('m'))).date('d').
        substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $order_sn;
    }
    /**
     * [fillWithdrawalData 生成提现记录的数据数组]
     * @param  [Int] $iCardId [银行卡id ]
     * @param  [Int] $iAmount [提现金额 ]
     * @return [Array]          [数据数组]
     */
    public function & compileData($iCardId, $fAmount)
    {
        $oUserBandCard      = new UserUserBankCard;
        $oBankCard          = $oUserBandCard->getUserCardInfoById($iCardId);
        // TODO 提交提现申请时，不计算手续费和实际提现金额，在审核后才将这两个值写入库，相关逻辑挪到后台管理的提现Model中
        // $iTransactionCharge = self::countTransactionCharge($fAmount);
        // $iTransactionAmount = $fAmount - $iTransactionCharge;
        // $oUser = UserUser::find(Session::get('user_id'));

        $data = [
            'serial_number'  => $this->generateSerialNumber(),
            'user_id'        => accessToken::$session['user_id'],
            'username'       => accessToken::$session['username'],
             'user_forefather_ids'      => accessToken::$session['forefather_ids'],
            'request_time'   => Carbon::now()->toDateTimeString(),
            'amount'         => $fAmount,
            'is_large'       => $this->getIsLargeStandard($fAmount),
            'bank_id'        => $oBankCard->bank_id,
            'bank'           => $oBankCard->bank,
            'account'        => $oBankCard->account,
            'account_name'   => $oBankCard->account_name,
            'province'       => $oBankCard->province,
            'branch'         => $oBankCard->branch,
            'branch_address' => $oBankCard->branch_address,
            'status'         => self::WITHDRAWAL_STATUS_NEW,
        ];
        return $data;
    }

    /**
     * TODO 获取大额提现的标准值
     */
    private function getIsLargeStandard($iAmount)
    {
        return (int)($iAmount > self::IS_LARGE_AMOUNT);
    }

    public static function getUserLatestWithdrawalRecord($iCount = 1) {
        $iUserId = Session::get('user_id');
        $aColumns = ['id', 'transaction_amount'];
        $oQuery = self::where('user_id', '=', $iUserId)->where('status', '=', self::WITHDRAWAL_STATUS_SUCCESS);
        $aRecords = isset($oQuery) ? $oQuery->orderBy('updated_at', 'desc')->limit($iCount)->get($aColumns) : [];
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        return $aRecords;
    }

}
