<?php

/**
 * 账变模型
 */
class Transaction extends BaseModel {

    protected $table = 'transactions';
    protected $softDelete = false;
    protected $fillable = [
        'serial_number',
        'user_id',
        'username',
        'user_forefather_ids',
        'account_id',
        'type_id',
        'is_income',
        'description',
        'amount',
        'note',
        'previous_total',
        'previous_balance',
        'previous_pt',
        'previous_im',
        'previous_ag',
        'previous_hb',
        'previous_mg',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'frozen',
        'available',
        'withdrawable',
        'tag',
        'admin_user_id',
        'administrator',
        'ip',
        'proxy_ip',
        'is_join_activity',
    ];
    public static $resourceName = 'Transaction';
    public static $amountAccuracy = 2;
    public static $mobileColumns = [
        'id',
        'serial_number',
        'created_at',
        'type_id',
        'amount',
        'available',
        'is_income',
    ];

//    public static $totalColumnsAllPages = [
//        'amount'
//    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'serial_number',
        'created_at',
        'username',
        'user_forefather_ids',
        'description',
        'amount',
        'total',
        'available',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'note',
        'ip',
        'administrator'
    ];
    public static $totalColumns = [
        'amount',
    ];
    public static $listColumnMaps = [
        'description' => 'friendly_description',
        'amount' => 'amount_formatted',
        'available' => 'available_formatted',
        'is_tester' => 'formatted_is_tester',
        'serial_number' => 'serial_number_short',
        'user_forefather_ids' => 'user_forefather_ids_formatted'
    ];
    public static $viewColumnMaps = [
        'is_tester' => 'formatted_is_tester',
        'description' => 'friendly_description',
        'amount' => 'amount_formatted',
        'available' => 'available_formatted',
        'frozen' => 'frozen_formatted',
        'total' => 'total_formatted',
        'balance' => 'balance_formatted',
        'pt' => 'pt_formatted',
        'im' => 'im_formatted',
        'ag' => 'ag_formatted',
        'hb' => 'hb_formatted',
        'mg' => 'mg_formatted',
        'withdrawable' => 'withdrawable_formatted',
        'previous_available' => 'previous_available_formatted',
        'previous_frozen' => 'previous_frozen_formatted',
        'previous_total' => 'previous_total_formatted',
        'previous_balance' => 'previous_balance_formatted',
        'previous_pt' => 'previous_pt_formatted',
        'previous_im' => 'previous_im_formatted',
        'previous_ag' => 'previous_ag_formatted',
        'previous_hb' => 'previous_hb_formatted',
        'previous_mg' => 'previous_mg_formatted',
        'previous_withdrawable' => 'previous_withdrawable_formatted',
        'serial_number' => 'serial_number',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [

    ];
    public static $ignoreColumnsInView = [
        'account_id',
        'user_id',
        'user_forefather_ids',
        'type_id',
        'is_income',
        'admin_user_id',
        'previous_total',
        'previous_balance',
        'previous_pt',
        'previous_im',
        'previous_ag',
        'previous_hb',
        'previous_mg',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'frozen',
        'withdrawable',
        'safekey',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $rules = [
        'type_id' => 'required|integer',
        'is_income' => 'required|in:0,1',
        'serial number' => 'max:20',
        'username' => 'max:16',
        'amount' => 'numeric',
        'previous_total' => 'numeric',
        'previous_balance' => 'numeric',
        'previous_pt' => 'numeric',
        'previous_im' => 'numeric',
        'previous_ag' => 'numeric',
        'previous_hb' => 'numeric',
        'previous_mg' => 'numeric',
        'previous_frozen' => 'numeric',
        'previous_available' => 'numeric',
        'previous_withdrawable' => 'numeric',
        'total' => 'numeric',
        'balance' => 'numeric',
        'pt' => 'numeric',
        'im' => 'numeric',
        'ag' => 'numeric',
        'hb' => 'numeric',
        'mg' => 'numeric',
        'frozen' => 'numeric',
        'withdrawable' => 'numeric',
        'available' => 'numeric',
        'ip' => 'ip',
        'proxy_ip' => 'ip',
        'note' => 'max:100',
        'tag' => 'max:30',
        'admin_user_id' => 'integer',
        'administrator' => 'max:16',
        'description' => 'required|max:50',
        'is_join_activity' => 'in:0,1',
    ];

    const ERRNO_CREATE_SUCCESSFUL = -100;
    const ERRNO_CREATE_ERROR_DATA = -101;
    const ERRNO_CREATE_ERROR_SAVE = -102;
    const ERRNO_CREATE_ERROR_BALANCE = -103;
    const ERRNO_CREATE_LOW_BALANCE = -104;

    protected function getLinkAttribute($sLink){
        if (empty($sLink)){
            $sLink = '#';
        }
        return $sLink;
    }


    public static function makeSeriesNumber($iUserId) {
        return md5($iUserId . microtime(true) . mt_rand());
    }

    protected function beforeValidate() {
        $this->serial_number = self::makeSeriesNumber($this->user_id);
        $this->makeSafeKey();
        return parent::beforeValidate();
    }

    private static function compileData($oUser, $oAccount, $iTypeId, $fAmount, & $aNewBalance, & $aExtraData = []) {
        $oTransactionType = TransactionType::find($iTypeId);
        $fAmount = formatNumber($fAmount, static::$amountAccuracy);

        $aAttributes = [
            'user_id' => $oUser->id,
            'amount' => $fAmount,
            'type_id' => $iTypeId,
            'is_income' => $oTransactionType->credit,
            'previous_frozen' => $oAccount->frozen,
            'previous_total' => $oAccount->available + $oAccount->pt + $oAccount->im + $oAccount->ag + $oAccount->hb + $oAccount->mg,
            'previous_balance' => $oAccount->balance,
            'previous_pt' => $oAccount->pt,
            'previous_im' => $oAccount->im,
            'previous_ag' => $oAccount->ag,
            'previous_hb' => $oAccount->hb,
            'previous_mg' => $oAccount->mg,
            'previous_available' => $oAccount->available,
            'previous_withdrawable' => $oAccount->withdrawable,
            'frozen' => $oAccount->frozen,
            'total' => $oAccount->available + $oAccount->pt + $oAccount->im + $oAccount->ag + $oAccount->hb + $oAccount->mg,
            'balance' => $oAccount->balance,
            'pt' => $oAccount->pt,
            'im' => $oAccount->im,
            'ag' => $oAccount->ag,
            'hb' => $oAccount->hb,
            'mg' => $oAccount->mg,
            'available' => $oAccount->available,
            'withdrawable' => $oAccount->withdrawable,
            'user_forefather_ids' => $oUser->forefather_ids,
            'account_id' => $oAccount->id,
            'username' => $oUser->username,
            'description' => $oTransactionType->description,
        ];

        if (isset($aExtraData['clent_ip'])) {
            $aAttributes['ip'] = $aExtraData['client_ip'];
        }
        if (isset($aExtraData['proxy_ip'])) {
            $aAttributes['proxy_ip'] = $aExtraData['proxy_ip'];
        }

        !isset($aExtraData['admin_user_id']) or $aAttributes['admin_user_id'] = $aExtraData['admin_user_id'];
        !isset($aExtraData['administrator']) or $aAttributes['administrator'] = $aExtraData['administrator'];
        !isset($aExtraData['note']) or $aAttributes['note'] = $aExtraData['note'];
        $aAttributes['is_join_activity'] = isset($aExtraData['is_join_activity']) ? $aExtraData['is_join_activity'] : 0;

        // deal amount
        $aSubAccounts = ['total','pt','im','ag','hb','mg','balance', 'available', 'frozen', 'withdrawable'];
//        pr($aAttributes);
        foreach ($aSubAccounts as $sField) {
            if (!$oTransactionType->$sField) {
                continue;
            }
            $aAttributes[$sField] += $oTransactionType->$sField * $fAmount;
            $aNewBalance[$sField] = $aAttributes[$sField];
        }
        $aAttributes['withdrawable'] >= 0 or $aNewBalance['withdrawable'] = $aAttributes['withdrawable'] = 0;
        return $aAttributes;
    }

    public function makeSafeKey() {
        $aFields = [
            'user_id',
            'type_id',
            'account_id',
            'amount',
            'description',
            'amount',
            'admin_user_id',
            'ip',
            'proxy_ip'
        ];
        $aData = [];
        foreach ($aFields as $sField) {
            $aData[] = $this->$sField;
        }
        return $this->safekey = md5(implode('|', $aData));
    }

    protected function setAmountAttribute($fAmount) {
        $this->attributes['amount'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setTotalAttribute($fAmount) {
        $this->attributes['total'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setBalanceAttribute($fAmount) {
        $this->attributes['balance'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPtAttribute($fAmount) {
        $this->attributes['pt'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setImAttribute($fAmount) {
        $this->attributes['im'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setAgAttribute($fAmount) {
        $this->attributes['ag'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setHbAttribute($fAmount) {
        $this->attributes['hb'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setMgAttribute($fAmount) {
        $this->attributes['mg'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setAvailableAttribute($fAmount) {
        $this->attributes['available'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setFrozenAttribute($fAmount) {
        $this->attributes['frozen'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setWithdrawableAttribute($fAmount) {
        $this->attributes['withdrawable'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousTotalAttribute($fAmount) {
        $this->attributes['previous_total'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousBalanceAttribute($fAmount) {
        $this->attributes['previous_balance'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousPtAttribute($fAmount) {
        $this->attributes['previous_pt'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousImAttribute($fAmount) {
        $this->attributes['previous_im'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousAgAttribute($fAmount) {
        $this->attributes['previous_ag'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousHbAttribute($fAmount) {
        $this->attributes['previous_hb'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousMgAttribute($fAmount) {
        $this->attributes['previous_mg'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousAvailableAttribute($fAmount) {
        $this->attributes['previous_available'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousFrozenAttribute($fAmount) {
        $this->attributes['previous_frozen'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setPreviousWithdrawableAttribute($fAmount) {
        $this->attributes['previous_withdrawable'] = $fAmount ? formatNumber($fAmount, static::$amountAccuracy) : 0;
    }

    protected function setSerialNumberAttribute($sSerialNumber) {
        $this->attributes['serial_number'] = strtoupper($sSerialNumber);
    }

    protected function getAmountFormattedAttribute() {
        return ($this->is_income ? '+' : '-') . $this->getFormattedNumberForHtml('amount');
    }

    protected function getDirectAmountAttribute() {
        return ($this->is_income ? '' : '-') . formatNumber($this->attributes['amount'], static::$amountAccuracy);
    }

    protected function getSerialNumberShortAttribute() {
        return substr($this->serial_number, 0, 4) . '...';
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available');
    }

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen');
    }

    protected function getTotalFormattedAttribute() {
        return $this->getFormattedNumberForHtml('total');
    }

    protected function getBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('balance');
    }

    protected function getPtFormattedAttribute() {
        return $this->getFormattedNumberForHtml('pt');
    }

    protected function getImFormattedAttribute() {
        return $this->getFormattedNumberForHtml('im');
    }

    protected function getAgFormattedAttribute() {
        return $this->getFormattedNumberForHtml('ag');
    }

    protected function getHbFormattedAttribute() {
        return $this->getFormattedNumberForHtml('hb');
    }

    protected function getMgFormattedAttribute() {
        return $this->getFormattedNumberForHtml('mg');
    }

    protected function getWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawable');
    }

    protected function getPreviousTotalFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_total');
    }

    protected function getPreviousBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_balance');
    }

    protected function getPreviousPtFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_pt');
    }

    protected function getPreviousImFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_im');
    }

    protected function getPreviousAgFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_ag');
    }

    protected function getPreviousHbFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_hb');
    }

    protected function getPreviousFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_frozen');
    }

    protected function getPreviousAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_available');
    }

    protected function getPreviousWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_withdrawable');
    }

    protected function getUpdatedAtDayAttribute() {
        return substr($this->updated_at, 5, 5);
    }

    protected function getUpdatedAtTimeAttribute() {
        $sTime = explode(' ', $this->updated_at);
        return $sTime[1];
    }

    /**
     * 增加新的账变
     * @param User      $oUser
     * @param Account   $oAccount
     * @param int      $iTypeId
     * @param float     $fAmount
     * @param array     $aExtraData
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public static function addTransaction($oUser, $oAccount, $iTypeId, $fAmount, $aExtraData = [], &$sSerialNumber = '') {
        if ($fAmount == 0) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }
        if (!$aAttributes = self::compileData($oUser, $oAccount, $iTypeId, $fAmount, $aNewBalance, $aExtraData)) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }

        $oNewTransaction = new Transaction($aAttributes);
        if (!$oNewTransaction->save()) {
            return self::ERRNO_CREATE_ERROR_SAVE;
        }
        $sSerialNumber = $oNewTransaction->serial_number;
        $oAccount->fill($aNewBalance);

        if (!$oAccount->save()) {
            return self::ERRNO_CREATE_ERROR_BALANCE;
        }

        if(in_array($iTypeId,TransactionType::$aTransferTypes) && $aExtraData['related_user_id']){
            DB::table('transactions_related_users')->insert(
                ['transaction_id'=>$oNewTransaction->id,
                    'related_user_id'=>$aExtraData['related_user_id'],
                    'related_user_name'=>$aExtraData['related_user_name']
                ]
            );
        }
        return self::ERRNO_CREATE_SUCCESSFUL;
    }

    protected function getFriendlyDescriptionAttribute() {
        return __('_transactiontype.' . strtolower(Str::slug($this->attributes['description'])));
    }

    /**
     * 反转，即进行逆操作
     *
     * @param Account $oAccount
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public function reverse($oAccount) {
        $oType = TransactionType::find($this->type_id);
        if (empty($oType) || empty($oType->reverse_type)) {
            return true;
        }
        $oUser = User::find($this->user_id);
        $aExtractData = $this->getAttributes();
        unset($aExtractData['id']);
        return self::addTransaction($oUser, $oAccount, $oType->reverse_type, $this->amount, $aExtractData);
    }

    protected function getUserForefatherIdsFormattedAttribute() {
        if ($this->user_forefather_ids) {
            $aIds = explode(',', $this->user_forefather_ids);
            $user = User::find($aIds[(count($aIds) - 1)]);
            if (is_object($user)) {
                return $user->username;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

}
