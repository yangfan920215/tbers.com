<?php

class Activity extends BaseModel {
    const CATEGORY_DEPOSIT = 0;
    const CATEGORY_SIGN_UP = 1;
    const CATEGORY_RECOMMEND = 2;
    const CATEGORY_LUCK_BONUS = 3;
    const CATEGORY_GIFT_VOUCHER = 4;
    const CATEGORY_DEPOSIT_COUPONS = 5;
    const CATEGORY_AG_FISHING_BONUS = 6;
    const CATEGORY_DAY_COMMISSION = 7;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    public static $aCategory = [
        self::CATEGORY_DEPOSIT => '存送优惠',
        self::CATEGORY_SIGN_UP => '开户礼金',
        self::CATEGORY_RECOMMEND => '推荐礼金',
        self::CATEGORY_LUCK_BONUS => '转运金',
        self::CATEGORY_GIFT_VOUCHER => '礼金优惠券',
        self::CATEGORY_DEPOSIT_COUPONS => '存送礼金券',
        self::CATEGORY_AG_FISHING_BONUS => 'AG捕鱼金',
        self::CATEGORY_DAY_COMMISSION => '日返水',
    ];

    public static $aEnable = [
        self::STATUS_ENABLE => '是',
        self::STATUS_DISABLE => '否',
    ];

    public static $resourceName = 'Activity';

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'category',
        'formatted_wallet',
        'is_enable',
        'join_number',
        'success_number',
        'total_send_bonus',
        'validity_period',
    ];
    public static $listColumnMaps = [
        'join_number' => 'friendly_join_number',
        'success_number'=>'friendly_success_number',
        'total_send_bonus' => 'friendly_total_send_bonus',
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
        'name' => 'required|max:50',
        'category' => 'required|in:0,1,2,3,4,5,6',
        'type' => 'required|in:0,1,2',
        'wallet' => '',
        'rule_id' => 'required',
        'start_at' => '',
        'end_at' => '',
        'is_single' => 'in:0,1',
        'is_display_in_promo' => 'in:0,1',
        'is_enable' => 'in:0,1',
        'note' => '',
    ];
    protected $table = 'activities';
    public static $htmlSelectColumns = [
        'rule_id' => 'aRules',
        'wallet' => 'aWallets',
        'type' => 'aTypes',
        'category' => 'aCategory',
    ];



    protected $fillable = [
        'id',
        'name',
        'category',
        'type',
        'wallet',
        'rule_id',
        'start_at',
        'end_at',
        'is_single',
        'is_display_in_promo',
        'is_enable',
        'note',
        'join_number',
        'success_number',
        'total_send_bonus',
        'created_at',
        'updated_at',
    ];

    protected function getFriendlyJoinNumberAttribute() {
        $iCount = ActivityReport::where('activity_id',$this->id)->count();

        return $iCount;
    }
    protected function getFriendlySuccessNumberAttribute() {
        $iCount = ActivityReport::where('activity_id',$this->id)->where('unlock_at','<>','')->count();

        return $iCount;
    }
    protected function getFriendlyTotalSendBonusAttribute() {
        $iSum = ActivityReport::where('activity_id',$this->id)->where('unlock_at','<>','')->sum('bonus');
         return $iSum;
    }

    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($bOrderByTitle = true, $aRuleList = [1,2]) {
        $aColumns = [ 'id', static::$titleColumn, 'rule_id', 'type'];
        $sOrderColumn = $bOrderByTitle ? static::$titleColumn : 'id';
        $oModels = self::whereIn('rule_id', $aRuleList)->orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id]['name'] = $oModel->{static::$titleColumn};
            $data[$oModel->id]['type'] = $oModel->type;
        }
        return $data;
    }

    public static function getBonus($oActivityRule, $fAmount = 0){
        $fBonus = $fAmount * $oActivityRule->rate / 100;
        $fBonus = $fBonus >= $oActivityRule->bonus_max ? $oActivityRule->bonus_max : $fBonus;
        return $fBonus;
    }

    public static function isDeposit($iPlatform, $iRuleType, $oUser){
        $aTypes = [];
        switch ($iPlatform){
            case Slot::PLATFORM_PT :
                $aTypes = TransactionType::$aTypesForTransferInPT;break;
            case Slot::PLATFORM_IM :
                $aTypes = TransactionType::$aTypesForTransferInIM;break;
            case Slot::PLATFORM_AG :
                $aTypes = TransactionType::$aTypesForTransferInAG;break;
            case Slot::PLATFORM_HB :
                $aTypes = TransactionType::$aTypesForTransferInHB;break;
        }
        $aConditions = [
            'user_id' => ['=', $oUser->id],
            'type_id' => ['in', $aTypes]
        ];
        $oQuery = Transaction::doWhere($aConditions);
        if($iRuleType == ActivityRule::TYPE_FIRST_DEPOSIT){
            return $oQuery->count() > 0 ? false : true;
        }elseif($iRuleType == ActivityRule::TYPE_SECOND_DEPOSIT){
            $bSecond = $oQuery->count() > 0 ? true : false;
            if($bSecond){
                return $oQuery->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d H:i:s')])->count() > 0 ? false : true;
            }else
                return false;
        }
    }

    public static function checkPromoExists($oActivity){
        if ($oActivity->is_single){
            $iType = $oActivity->type;
            $iWallet = $oActivity->wallet;
            $iUserId = Session::get('user_id');
            switch ($iType) {
                case ActivityRule::TYPE_FIRST_DEPOSIT :
                case ActivityRule::TYPE_SECOND_DEPOSIT :
                    return self::isDepositExists($iWallet, $iUserId, $iType, $oActivity->id);
                    break;
                default :
                    return self::isPromosExists($oActivity, $iUserId);
                    break;
            }
        } else {
            return false;
        }
    }

    private static function isDepositExists($iWallet, $iUserId, $iType, $iActivity){
        $aTypes = [
            Account::WALLET_PT => TransactionType::TYPE_TRANSFER_TO_PT,
            Account::WALLET_IM => TransactionType::TYPE_TRANSFER_TO_IM,
            Account::WALLET_AG => TransactionType::TYPE_TRANSFER_TO_AG,
            Account::WALLET_HB => TransactionType::TYPE_TRANSFER_TO_HB,
            Account::WALLET_MG => TransactionType::TYPE_TRANSFER_TO_MG,
        ];
        $aCondition = [
            'wallet' => ['=', $iWallet],
            'category' => ['!=', Activity::CATEGORY_SIGN_UP],
        ];
        $oUser = User::find($iUserId);
        $oQuery = ActivityReport::doWhere($aCondition)->whereRaw('( user_id = '.$iUserId.' or receive_ip = "'.get_client_ip().'" or register_ip = "'.$oUser->register_ip.'")');
        $aTransactions = Transaction::where('user_id', $iUserId)->where('type_id', $aTypes[$iWallet])->where('is_join_activity', 0);
        if ($iType == ActivityRule::TYPE_SECOND_DEPOSIT) {

            $aTransactions = $aTransactions->where('created_at', '>=', date('Y-m-d 00:00:00'))
                ->where('created_at', '<=', date('Y-m-d 23:59:59'));
            $oQuery = $oQuery->where('type', ActivityRule::TYPE_SECOND_DEPOSIT)
                ->where('created_at', '>=', date('Y-m-d 00:00:00'))
                ->where('created_at', '<=', date('Y-m-d 23:59:59'));
        } elseif($iType == ActivityRule::TYPE_FIRST_DEPOSIT) {
            $oQuery = $oQuery->whereIn('type', [ActivityRule::TYPE_FIRST_DEPOSIT, ActivityRule::TYPE_SECOND_DEPOSIT]);
        }
        if($iWallet == Account::WALLET_PT) file_put_contents('/tmp/debug_activity', $oQuery->toSql().' wallet : '.$iWallet.' activity_id : '.$iActivity.' user_id : '.$iUserId.' receive_ip : '.get_client_ip()."\n\r", FILE_APPEND);
        $iCount = $oQuery->count();
        if($iWallet == Account::WALLET_PT) file_put_contents('/tmp/debug_activity', $aTransactions->toSql().' user_id : '.$iUserId.' type_id : '.$aTypes[$iWallet]."\n\r", FILE_APPEND);
        $iTransactionsCount = $aTransactions->count();
        return $iTransactionsCount || $iCount;
    }

    private static function isPromosExists($oActivity, $iUserId){
        return $oActivityReport = ActivityReport::doWhere([
            'category' => ['=', $oActivity->category],
            'activity_id' => ['=', $oActivity->id],
            'wallet' => ['=', $oActivity->wallet],
            'user_id' => ['=', $iUserId],
        ])->orWhere('receive_ip', get_client_ip())->count();
    }

    protected function getValidityPeriodAttribute() {
        $sValidityPeriod = '长期';
        if ($this->end_at && $this->start_at) $sValidityPeriod = $this->start_at.'-'.$this->end_at;
        return $sValidityPeriod;
    }

    protected function getFormattedWalletAttribute() {
        $sWallet = '';
        if ($this->wallet) {
            $aWallet = explode(',', $this->wallet);
            $aWalletName = [];
            foreach ($aWallet as $iWallet) {
                $aWalletName[] = Account::$aWallets[$iWallet];
            }
            $sWallet = implode(',', $aWalletName);
        }
        return $sWallet;
    }
}
