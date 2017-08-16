<?php

class UserBankCard extends BaseModel {

    const ERRNO_CARD_LOCKED = -2100;
    const ERRNO_CARD_OPERATION_STEP_ERROR = -2101;
    const ERRNO_VALID_FAILED = -2102;
    const ERRNO_NO_CARD = -2103;
    const ERRNO_BIND_CARD_FAILED = -2104;
    const ERRNO_MODIFY_CARD_FAILED = -2105;
    const ERRNO_MODIFY_CARD_SUCCESS = -2106;
    const ERRNO_BIND_CARD_SUCCESS = -2107;
    const ERRNO_LOCK_CARD_SUCCESS = -2108;
    const ERRNO_LOCK_CARD_FAILED = -2109;
    const ERRNO_UNLOCK_CARD_FORBIDDEN = -2110;
    const ERRNO_MISSING_BANK_CARD = -2111;
    const ERRNO_DELETE_SUCCESS = -2112;
    const ERRNO_OUT_MAX_CARD_NUM = -2113;
    const ERRNO_VALID_SUCCESS = -2114;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_bank_cards';
    public static $resourceName = 'UserBankCard';
    protected static $cacheLevel = 1;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = true;
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'account' => 'display_account_no',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'parent_user_id',
        'parent_username',
        'user_forefather_ids',
        'user_forefathers',
        'bank_id',
        'bank',
        'province_id',
        'province',
        'city_id',
        'city',
//        'town_id',
//        'town',
        'branch',
        'branch_address',
        'account_name',
        'account',
        'account_confirmation',
        'status',
        'islock',
        'is_agent',
        'is_tester',
        'locker',
        'lock_time',
        'unlocker',
        'unlock_time',
        'deleted_at',
    ];
    public static $mobileColumns = [
        'id',
        'account_name',
        'account',
        'bank_id',
        'bank',
        'province_id',
        'province',
        'city_id',
        'city',
//        'town_id',
//        'town',
        'branch',
        'islock',
    ];
    public static $columnForList = [
        'username',
        'account_name',
        'account',
        'bank',
        'province',
        'city',
//        'town',
        'branch',
        'status',
        'islock',
//        'locker',
        'lock_time',
//        'unlocker',
        'unlock_time',
        'created_at',
        'updated_at',
    ];
    // 帐号反查功能展示列
    public static $columnForUserSearchList = [
        'parent_username',
        'username',
        'account_name',
        'account',
        'bank',
        'province',
        'city',
        'branch',
        'status',
        'created_at',
        'lock_time',
        'unlock_time',
    ];

    const LOCKED = 1; // user's bankcards locked status
    const UNLOCKED = 0;
    const STATUS_NOT_IN_USE = 0;
    const STATUS_IN_USE = 1;
    const STATUS_DELETED = 2;
    const STATUS_LOCKED = 3;

    public static $validStatuses = [
        self::STATUS_NOT_IN_USE => 'Not In Use',
        self::STATUS_IN_USE => 'In Use',
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_LOCKED => 'Locked',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        // 'user_id'  => 'aUsers',
        'bank_id' => 'aBanks',
        'province_id' => 'aProvinces',
        'city_id' => 'aCities',
        'status' => 'aStatus',
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $rules = [
        'user_id' => 'required|integer|min:1',
        // 'username'          => 'alpha_num|custom_first_character|between:6,16',
        'bank_id' => 'required|integer|min:1',
        // 'bank'              => 'required|max:50',
        'province_id' => 'integer|min:1',
        // 'province'          => 'between:0,20',
        'city_id' => 'integer|min:1',
//        'town_id' => 'integer|min:1',
        // 'city'              => 'between:0,20',
        'branch' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
        'branch_address' => 'between:1,100',
        'account_name' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
        'account' => 'required|regex:/^[0-9]*$/|between:16,19|confirmed',
        'account_confirmation' => 'required|regex:/^[0-9]*$/|between:16,19',
        'status' => 'in:0,1,2',
        'islock' => 'in:0,1',
        'is_agent' => 'in:0,1',
        'is_tester' => 'in:0,1',
        'locker' => 'integer',
        'lock_time' => 'date',
        'unlocker' => 'integer',
        'unlock_time' => 'date',
    ];
    public $autoPurgeRedundantAttributes = true;
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = ['bank', 'province', 'city'];
    public static $ignoreColumnsInEdit = ['id', 'user_id', 'parent_user_id', 'parent_username', 'user_forefather_ids', 'user_forefathers', 'locker', 'lock_time', 'unlocker', 'unlock_time', 'islock', 'is_agent', 'is_tester', 'status'];
    public static $ignoreColumnsInView = ['id', 'user_id', 'parent_user_id', 'user_forefather_ids', 'bank_id', 'province_id', 'city_id'];

    protected function beforeValidate() {
        if (intval($this->bank_id)) {
            $oBank = Bank::find($this->bank_id);
            $this->bank = $oBank->name;
        }
        if (intval($this->province_id)) {
            $oProvince = District::find($this->province_id);
            $this->province = $oProvince ? $oProvince->name : '';
        }
        if (intval($this->city_id)) {
            $oCity = District::find($this->city_id);
            $this->city = $oCity ? $oCity->name : '';
        }
        if (!$this->branch_address) {
            $this->branch_address = $this->province . $this->city . $this->town . $this->branch;
        }
        if (intval($this->user_id) && (!$this->parent_user_id || !$this->parent_username || !$this->user_forefather_ids || !$this->user_forefathers || !$this->is_agent
//                || !$this->is_tester
            )) {
            $oUser = User::find($this->user_id);
            if (!$this->username) {
                $this->username = $oUser->username;
            }
            if (!$this->parent_user_id) {
                $this->parent_user_id = $oUser->parent_id;
            }
            if (!$this->parent_username) {
                $this->parent_username = $oUser->parent;
            }
            if (!$this->user_forefather_ids) {
                $this->user_forefather_ids = $oUser->forefather_ids;
            }
            if (!$this->user_forefathers) {
                $this->user_forefathers = $oUser->forefathers;
            }
            if (!$this->is_agent) {
                $this->is_agent = $oUser->is_agent;
            }
//            if (!$this->is_tester) {
//                $this->is_tester = $oUser->is_tester;
//            }
        }
        if(!$this->checkBankCard()) {
            $this->validationErrors->add('account', UserUserBankCard::$customMessages['account.unique']);
            return false;
        }
        return parent::beforeValidate();
    }
    
    private function checkBankCard(){
        if($this->id)
            $r = DB::select("select count(users.id) as exist_card from user_bank_cards left join users on users.id = user_bank_cards.user_id where user_bank_cards.account = '".$this->account."' and users.blocked=0 and user_bank_cards.id !=".$this->id);
        else
            $r = DB::select("select count(users.id) as exist_card from user_bank_cards left join users on users.id = user_bank_cards.user_id where user_bank_cards.account = '".$this->account."' and users.blocked=0 and user_bank_cards.user_id !=".$this->user_id);
        
        if($r[0]->exist_card) return false;
        return true;
    }
    /**
     * [getUserAccounts 根据用户id获取该用户的银行卡]
     * @param  [Integer] $iUserId [用户id]
     * @return [Array]          [银行卡数组]
     */
    public static function getUserAccounts($iUserId) {
        $aColumns = ['id', 'bank_id', 'account_name', 'account', 'province', 'branch', 'branch_address'];
        $aUserAccounts = UserBankCard::where('user_id', '=', $iUserId)->get($aColumns);
        $data = [];
        // pr((int)isset($data[$aUserAccounts[0]->bank_id]));exit;
        foreach ($aUserAccounts as $account) {
            if (!isset($data[$account->bank_id]) || !is_array($data[$account->bank_id])) {
                $data[$account->bank_id] = [];
            }
            $data[$account->bank_id][] = $account->toArray();
        }
        return $data;
    }

    public static function getAllAccountNames($user_id) {
        $aAccounts = UserBankCard::all(['account']);
        $data = [];
        foreach ($aAccounts as $account) {
            $data[$account->account] = $account->account;
        }
        return $data;
    }

    protected static function getUserCards($iUserId) {
        // $iUserId or $iUserId = Session::get('user_id');
        $iStatus = self::STATUS_DELETED;
        $oQuery = UserUserBankCard::where('user_id', '=', $iUserId)->where('status', '<>', $iStatus)->whereNull('deleted_at');
        return $oQuery;
    }

    /**
     * [getUserCardsInfo get user's binded cards info]
     * @return [Int] [cards info]
     */
    public static function getUserCardsInfo($iUserId, $aColumns = null) {
        // if (!$columns || (is_array($columns) && !count($columns))) $columns = [self::$titleColumn];
        // $aColumns = array_merge(['account'], $columns);
        $aColumns or $aColumns = ['id', 'account'];
        // $iUserId = Session::get('user_id');
        $oQuery = self::getUserCards($iUserId);
        $aInfo = $oQuery->get($aColumns);
        return $aInfo;
    }

    /**
     * [setLockStatus 设置用户银行卡为锁定状态]
     * @param [Int] $iCardId [用户银行卡id]
     * @param [Int] $iStatus [锁定/解锁状态值]
     */
    public static function setLockStatus($iCardId, $iStatus, $iLockerId) {
        if (!$iLockerId || !$iCardId)
            return false;
        $oCard = self::find($iCardId);
        // $iLockerId or $iLockerId = Session::get('admin_user_id');
        if (!$oCard)
            return false;
        $oCard->islock = $iStatus;
        if ($iStatus == self::LOCKED) {
            $oCard->locker = $iLockerId;
            $oCard->lock_time = Carbon::now()->toDateTimeString();
        } else {
            $oCard->unlocker = $iLockerId;
            $oCard->unlock_time = Carbon::now()->toDateTimeString();
        }
        $oCard->account_confirmation = $oCard->account;
        // pr($oCard->toArray());exit;
        return $oCard->save();
    }

    protected function getFormattedStatusAttribute() {
        if ($this->attributes['status'] == self::STATUS_IN_USE && $this->attributes['islock']) {
            return __('_userbankcard.' . strtolower(Str::slug(static::$validStatuses[self::STATUS_LOCKED])));
        } else {
            return __('_userbankcard.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
        }
    }

    protected function getDisplayAccountNoAttribute() {
        return substr($this->attributes['account'], -4);
    }
    
     
}
