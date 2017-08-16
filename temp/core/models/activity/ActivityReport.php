<?php

class ActivityReport extends BaseModel {

    const STATUS_NORMAL = 0;
    const STATUS_FINISH = 1;
    const STATUS_CANCEL = 2;
    const STATUS_UNDONE = 3;
    const STATUS_SEND = 4;
    const STATUS_DONE = 5;
    const STATUS_UNNORMAL = 6;
    const STATUS_EXPIRED = 7;
    const STATUS_DEPOSIT_UNDONE = 8;
    const STATUS_DEPOSIT_DONE = 9;
    const STATUS_WAITING_RECEIVE = 10;
    const STATUS_RECEIVED = 11;
    const STATUS_ADMIN_UNLOCK = 12;

    public static $validStatuses = [
        self::STATUS_NORMAL => 'normal',
        self::STATUS_FINISH => 'finish',
        self::STATUS_CANCEL => 'cancel',
        self::STATUS_UNDONE => 'undone',
        self::STATUS_SEND => 'send',
        self::STATUS_DONE => 'done',
        self::STATUS_UNNORMAL => 'unnormal',
        self::STATUS_EXPIRED => 'expired',
        self::STATUS_DEPOSIT_UNDONE => 'deposit undone',
        self::STATUS_DEPOSIT_DONE => 'deposit done',
        self::STATUS_WAITING_RECEIVE => 'waiting receive',
        self::STATUS_RECEIVED => 'received',
        self::STATUS_ADMIN_UNLOCK => 'admin unlock',
    ];

    public static $resourceName = 'ActivityReport';

    /**
     * title field
     * @var string
     */
//    public static $titleColumn = 'name';

    public static $totalColumnsAllPages = ['amount', 'bonus', 'finished_turnover'];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'category',
        'activity_id',
        'activity_rule_id',
        'activity_name',
        'wallet',
        'platform',
        'type',
        'user_id',
        'username',
        'amount',
        'bonus',
        'multiple',
        'total_turnover',
        'finished_turnover',
        'unlock_balance',
        'start_time',
        'end_time',
        'status',
        'is_receive',
        'signup_at',
        'recommender_id',
        'recommender',
        'finish_at',
        'date_range',
        'code',
        'receive_ip',
        'receive_at',
        'unlock_at',
        'transfer_in',
        'finish_deposit_at',
        'register_ip',
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
//        'platform' => '',
//        'name' => 'required|max:50',
//        'status' => 'in:0,1',
//        'rule_id' => '',
    ];
    protected $table = 'activity_reports';
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'type' => 'aTypes',
        'wallet' => 'aWallets'
    ];



    protected $fillable = [
        'id',
        'category',
        'activity_id',
        'activity_rule_id',
        'activity_name',
        'wallet',
        'platform',
        'type',
        'user_id',
        'username',
        'amount',
        'bonus',
        'multiple',
        'total_turnover',
        'finished_turnover',
        'unlock_balance',
        'start_time',
        'end_time',
        'status',
        'is_receive',
        'signup_at',
        'recommender_id',
        'recommender',
        'finish_at',
        'date_range',
        'code',
        'receive_ip',
        'receive_at',
        'unlock_at',
        'transfer_in',
        'finish_deposit_at',
        'register_ip',
    ];

    protected function getFormattedStatusAttribute() {
        return __('_activityreport.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }
    protected function getFormattedWalletAttribute() {
        if (!$this->attributes['wallet']) return '';
        return Account::$aWallets[$this->attributes['wallet']];
    }

    protected function getFormattedCategoryAttribute() {
        if (!$this->attributes['category']) return '';
        return Activity::$aCategory[$this->attributes['category']];
    }

    protected function getIsAgentAttribute() {
        $oUser = User::find($this->user_id);
        if(!$oUser)
            return '';
        return __('_activityreport.' . strtolower(Str::slug(User::$aUserTypes[$oUser->is_agent])));
    }

    protected function getLoginIpAttribute() {
        $oUser = User::find($this->user_id);
        if(!$oUser)
            return '';
        return $oUser->login_ip;
    }

    protected function getSignupAtAttribute(){
        $oUser = User::find($this->user_id);
        if(!$oUser)
            return '';
        return $oUser->register_at;
    }
}
