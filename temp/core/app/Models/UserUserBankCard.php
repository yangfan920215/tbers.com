<?php

class UserUserBankCard extends UserBankCard {

    // user create or edit bankcard info action step status, commented but keep the code for tips
    // const VALIDATE  = 0;
    // const GENERATE  = 1;
    // const CONFIRM   = 2;
    // const RESULT    = 3;
    // const SUCCEED   = 4;
    // const FAILED    = 5;
    // 用户平台列表字段
    public static $columnForList = ['bank', 'account', 'updated_at', 'status'];
    // protected $fillable = [
    //     'user_id',
    //     'username',
    //     'parent_user_id',
    //     'parent_username',
    //     'user_forefather_ids',
    //     'user_forefathers',
    //     'bank_id',
    //     'bank',
    //     'province_id',
    //     'province',
    //     'city_id',
    //     'city',
    //     'branch',
    //     'branch_address',
    //     'account_name',
    //     'account',
    //     'status',
    //     'islock',
    //     'is_agent',
    //     'is_tester',
    //     'locker',
    //     'lock_time',
    //     'unlocker',
    //     'unlock_time',
    // ];
    protected $isAdmin = false;
    public static $customMessages = [
        'bank_id.integer'               => '请选择开户银行',
        'province_id.integer'           => '请选择开户银行区域省份',
        'city_id.integer'               => '请选择开户银行区域城市',
        'branch.required'               => '请填写支行名称',
        'branch.regex'                  => '支行名称必须由字母，数字或汉字组成',
        'branch.between'                => '支行名称长度必须介于 :min - :max 之间',
        'account_name.required'         => '请填写开户人姓名',
        'account_name.regex'            => '开户人姓名必须由字母，数字或汉字组成',
        'account_name.between'          => '开户人姓名长度必须介于 :min - :max 之间',
        'account.between'               => '银行账号由16位或19位数字组成',
        'account.confirmed'             => '银行账号两次输入不一致',
        'account.required'              => '请填写银行账号',
        'account.unique'                => '银行账号已存在',
        'account_confirmation.between'  => '确认银行账号由16位或19位数字组成',
        'account_confirmation.required' => '请填写确认银行账号',
    ];

    protected function beforeValidate() {

        $this->account = str_replace(' ', '', $this->account);
        $this->account_confirmation = str_replace(' ', '', $this->account_confirmation);
        $this->user_id = Session::get('user_id');
        $this->username = Session::get('username');
        // pr($this->toArray());exit;
        return parent::beforeValidate();
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

    // protected static function getUserCards($iUserId = null)
    // {
    //     $iUserId or $iUserId = Session::get('user_id');
    //     $iStatus = self::STATUS_DELETED;
    //     $oQuery = UserUserBankCard::where('user_id', '=', $iUserId)->where('status', '<>', $iStatus);
    //     return $oQuery;
    // }
    /**
     * [getUserCardsNum get user's binded cards count]
     * @return [Int] [cards num]
     */
    public static function getUserCardsNum($iUserId = null) {
        $iUserId or $iUserId = Session::get('user_id');
        $oQuery = self::getUserCards($iUserId);
        return $oQuery->count();
    }
    /**
     * [getUserCardInfoById 根据绑定的银行卡id查询可用的用户银行卡信息]
     * @param  [Integer] $iCardId [绑定的银行卡id]
     * @return [Object]          [银行卡号信息]
     */
    public function getUserCardInfoById($iCardId) {
        $oQuery = UserUserBankCard::where('id', '=', $iCardId)->where('status', '=', self::STATUS_IN_USE);
        return $oQuery->first();
    }
    /**
     * [getUserBankCardAccount 根据银行卡号查询可用的用户银行卡信息]
     * @param  [String] $sAccount [银行卡号]
     * @return [Object]           [银行卡号信息]
     */
    public static function getUserBankCardAccount($sAccount) {
        $oQuery = UserUserBankCard::where('account', '=', $sAccount)->where('status', '=', self::STATUS_IN_USE);
        return $oQuery->first();
    }

    /**
     * [getUserCardsLockStatus 获取用户银行卡的锁定状态]
     * @return [Int] [0: 未锁定, 1: 锁定 ]
     */
    public static function getUserCardsLockStatus() {
        $aColumns = ['islock'];
        $iUserId = Session::get('user_id');
        $oQuery = self::getUserCards($iUserId);
        $aLockStatus = $oQuery->get($aColumns)->toArray();
        // pr($aLockStatus);exit;
        $aLocked = [];
        foreach ($aLockStatus as $iLockStatus) {
            if ($iLockStatus['islock'] == self::LOCKED) {
                $aLocked[] = $iLockStatus;
            }
        }
        return count($aLocked);
    }

}
