<?php

# 链接开户管理

class RegisterLink extends BaseModel {

    protected $table = 'register_links';
    protected $softDelete = false;
    protected $fillable = [
        'is_admin',
        'user_id',
        'username',
        'is_agent',
        'keyword',
        'note',
        'channel',
        'click_count',
        'first_deposit_count',
        'agent_qqs',
        'created_count',
        'url',
        'status',
    ];
    public static $resourceName = 'RegisterLink';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'channel',
        'username',
        'created_count',
        'is_agent',
        'is_tester',
        'status',
        'created_at',
        'expired_at',
    ];

    /**
     * [$aNoNeedSortColumns 部分展现的字段是格式化后的值，不能用它们做排序]
     * @var [type]
     */
    public static $aNoNeedSortColumns = ['status_formatted'];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'aStatuses',
            // 'is_agent' => 'aIsAgents',
    ];

    /**
     * 编辑框字段配置
     * @var array
     */
    public static $htmlTextAreaColumns = [
        'commission_sets',
    ];

    const STATUS_IN_USE = 0;
    const STATUS_CLOSED = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_VALID_FOREVER = 3;

    public static $aStatuses = [
        self::STATUS_IN_USE => 'In use',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_EXPIRED => 'expired',
        self::STATUS_VALID_FOREVER => 'valid forever',
    ];
    // public static $aIsAgents = ['No', 'Yes'];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'is_agent' => 'formatted_is_agent',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'updated_at' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required|between:4,32',
        'valid_days' => 'integer',
        'is_admin' => 'in:0,1',
        'is_agent' => 'in:0,1',
        'is_tester' => 'in:0,1',
        'keyword' => 'required',
        'note' => 'max:100',
        'channel' => 'max:50',
        'agent_qqs' => 'max:50',
        'created_count' => 'integer',
        'url' => 'required|max:255',
        'status' => 'in:0,1',
    ];

    public function users() {
        return $this->belongsToMany('User', 'register_link_users', 'register_link_id', 'user_id')->withTimeStamps();
    }

    protected function getFriendlyCreatedAtAttribute() {
        return friendly_date($this->created_at);
    }

    protected function getFriendlyExpiredAtAttribute() {

        if($this->status == RegisterLink::STATUS_CLOSED){
            return '关闭';
        }
        $iStatus = UserRegisterLink::getRegisterLinkByPrizeKeyword($this->keyword);

        if(is_null($this->expired_at) && $iStatus){
            return '永久有效';
        }
        if($iStatus){
            return '正常';
        }
        return '过期';
            //return friendly_date($this->expired_at);
    }

    protected function getValidDatesAttribute() {

    }

    public static function getActiveLink($id) {
        return self::where('id', '=', $id)
                        ->where('status', '=', 0)
                        ->first();
    }

    protected function getFormattedStatusAttribute() {
        if ($this->status == self::STATUS_IN_USE) {
            if ($this->expired_at) {
                if (Carbon::now()->toDateTimeString() > $this->expired_at) {
                    $sStatus = self::STATUS_EXPIRED;
                } else {
                    $sStatus = self::STATUS_IN_USE;
                }
            } else {
                $sStatus = self::STATUS_VALID_FOREVER;
            }
        } else {
            $sStatus = self::STATUS_CLOSED;
        }
        return __('_registerlink.' . strtolower(Str::slug(self::$aStatuses[$sStatus])));
    }

    protected function getFormattedIsAgentAttribute() {
        $sType = $this->is_agent;
        if ($this->is_admin) $sType = User::TYPE_TOP_AGENT;
        return __('_user.' . strtolower(Str::slug(User::$aUserTypes[$sType])));
    }

    protected function getFirstDepositCountAttribute(){
        $aRegisterLinkUsers = RegisterLinkUser::doWhere([
            'register_link_id' => ['=', $this->id]
        ])->get(['user_id']);

        $aUserIds = [];
        foreach ($aRegisterLinkUsers as $oRegisterLinkUser) {
            $aUserIds[] = $oRegisterLinkUser->user_id;
        }

        if(!empty($aUserIds)){
            $iCount = User::doWhere([
                'id' => ['in', $aUserIds],
                'first_deposit_amount' => ['>', 0]
            ])->count();
        }else{
            $iCount=0;
        }
        return $iCount;


    }

}
