<?php
/**
 * 账变类型
 */
class TransactionType extends BaseModel {
    const TYPE_DEPOSIT                 = 1;
    const TYPE_WITHDRAW                = 2;
    const TYPE_TRANSFER_IN             = 3;
    const TYPE_TRANSFER_OUT            = 4;
    const TYPE_FREEZE_FOR_WITHDRAWAL   = 9;
    const TYPE_UNFREEZE_FOR_WITHDRAWAL = 10;
    const TYPE_DEPOSIT_FEE_BACK        = 16;
    const TYPE_WITHDRAW_FEE            = 17;
    const TYPE_DEPOSIT_BY_ADMIN        = 18;
    const TYPE_WITHDRAW_BY_ADMIN       = 19;
    const TYPE_SEND_BONUS              = 20;
    const TYPE_CANCEL_BONUS            = 21;
    const TYPE_SETTLING_CLAIMS         = 22;
    const TYPE_DEPOSIT_COMMISSION      = 24;
    const TYPE_TURNOVER_COMMISSION     = 25;
    const TYPE_PROFIT_COMMISSION       = 26;
    const TYPE_VIOLATION_CLAIMS        = 27;
    const TYPE_FREEZE_FOR_GUARANTEE    = 29;
    const TYPE_VOUCHER_DEPOSIT = 35;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_LOSS = 36;
    const TYPE_ACTIVITY_PROMOTION = 39;
    const TYPE_SETTLING_CLAIMS_FOR_PT = 43;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_PT = 44;
    const TYPE_BONUS_FOR_PT = 47;
    const TYPE_SETTLING_CLAIMS_FOR_IM = 48;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_IM = 49;
    const TYPE_BONUS_FOR_IM = 51;
    const TYPE_BONUS_FOR_AG = 53;
    const TYPE_SETTLING_CLAIMS_FOR_AG = 54;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_AG = 55;
    const TYPE_TRANSFER_TO_PT = 56;
    const TYPE_TRANSFER_TO_IM = 57;
    const TYPE_TRANSFER_TO_AG = 58;
    const TYPE_AG_TO_BALANCE = 59;
    const TYPE_IM_TO_BALANCE = 60;
    const TYPE_PT_TO_BALANCE = 61;
    const TYPE_SEND_BONUS_TO_BONUS_WALLET = 68;
    const TYPE_CANCEL_BONUS_FROM_BONUS_WALLET = 69;
    const TYPE_UNLOCK_BONUS_FROM_BONUS_WALLET = 70;
    const TYPE_SETTLING_CLAIMS_FOR_HB = 72;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_HB = 73;
    const TYPE_BONUS_FOR_HB = 74;
    const TYPE_TRANSFER_TO_HB = 75;
    const TYPE_HB_TO_BALANCE = 76;
    const TYPE_SETTLING_CLAIMS_FOR_MG = 78;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_MG = 79;
    const TYPE_BONUS_FOR_MG = 80;
    const TYPE_TRANSFER_TO_MG = 81;
    const TYPE_MG_TO_BALANCE = 82;
    const TYPE_DAY_COMMISSION = 83;
    const TYPE_REDUCE = 84;
    const TYPE_REDUCE_PT = 85;
    const TYPE_REDUCE_IM = 86;
    const TYPE_REDUCE_AG = 87;
    const TYPE_REDUCE_HB = 88;
    const TYPE_REDUCE_MG = 89;
    const TYPE_THIRD_DEPOSIT = 90;

    public static $aTypeForPlatform = [
        self::TYPE_SETTLING_CLAIMS_FOR_PT => 'system,pt',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_PT => 'system,pt',
        self::TYPE_BONUS_FOR_PT => 'system,pt',
        self::TYPE_SETTLING_CLAIMS_FOR_IM  => 'system,im',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_IM  => 'system,im',
        self::TYPE_BONUS_FOR_IM  => 'system,im',
        self::TYPE_BONUS_FOR_AG  => 'system,ag',
        self::TYPE_SETTLING_CLAIMS_FOR_AG  => 'system,ag',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_AG  => 'system,ag',
        self::TYPE_BONUS_FOR_HB  => 'system,hb',
        self::TYPE_SETTLING_CLAIMS_FOR_HB  => 'system,hb',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_HB  => 'system,hb',
        self::TYPE_BONUS_FOR_MG  => 'system,mg',
        self::TYPE_SETTLING_CLAIMS_FOR_MG  => 'system,mg',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_MG  => 'system,mg',
        self::TYPE_TRANSFER_TO_PT  => 'main,pt',
        self::TYPE_TRANSFER_TO_IM  => 'main,im',
        self::TYPE_TRANSFER_TO_AG  => 'main,ag',
        self::TYPE_TRANSFER_TO_HB  => 'main,hb',
        self::TYPE_TRANSFER_TO_MG  => 'main,mg',
        self::TYPE_AG_TO_BALANCE  => 'ag,main',
        self::TYPE_IM_TO_BALANCE  => 'im,main',
        self::TYPE_PT_TO_BALANCE  => 'pt,main',
        self::TYPE_HB_TO_BALANCE  => 'hb,main',
        self::TYPE_MG_TO_BALANCE  => 'mg,main',
    ];

    public static $aTypesForTransferInPT = [
        self::TYPE_SETTLING_CLAIMS_FOR_PT,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_PT,
        self::TYPE_BONUS_FOR_PT,
        self::TYPE_TRANSFER_TO_PT,
    ];
    public static $aTypesForTransferInIM = [
        self::TYPE_SETTLING_CLAIMS_FOR_IM,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_IM,
        self::TYPE_BONUS_FOR_IM,
        self::TYPE_TRANSFER_TO_IM,
    ];
    public static $aTypesForTransferInAG = [
        self::TYPE_BONUS_FOR_AG,
        self::TYPE_SETTLING_CLAIMS_FOR_AG,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_AG,
        self::TYPE_TRANSFER_TO_AG,
    ];
    public static $aTypesForTransferInHB = [
        self::TYPE_BONUS_FOR_HB,
        self::TYPE_SETTLING_CLAIMS_FOR_HB,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_HB,
        self::TYPE_TRANSFER_TO_HB,
    ];

    public static $aTypesForTransferInMg = [
        self::TYPE_BONUS_FOR_MG,
        self::TYPE_SETTLING_CLAIMS_FOR_MG,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_MG,
        self::TYPE_TRANSFER_TO_MG,
    ];

    public static $aDepositTypes = [
        self::TYPE_DEPOSIT,
        self::TYPE_DEPOSIT_BY_ADMIN,
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_LOSS,
        self::TYPE_VIOLATION_CLAIMS,
        self::TYPE_THIRD_DEPOSIT,
    ];
    public static $aWithdrawTypes = [
        self::TYPE_WITHDRAW,
//        self::TYPE_WITHDRAW_BY_ADMIN,
        self::TYPE_TRANSFER_OUT,
    ];
    public static $aCommissionTypes = [
        self::TYPE_DAY_COMMISSION,
    ];
    public static $aBonusTypes = [
        self::TYPE_BONUS_FOR_AG,
        self::TYPE_BONUS_FOR_PT,
        self::TYPE_BONUS_FOR_IM,
        self::TYPE_BONUS_FOR_HB,
        self::TYPE_BONUS_FOR_MG,
    ];
    public static $aTransferTypes = [
        self::TYPE_TRANSFER_IN,
        self::TYPE_TRANSFER_OUT,
    ];

    public static $aDepostTypeForWallet = [
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_PT => 'Pt',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_IM => 'Im',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_AG => 'Ag',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_HB => 'Hb',
        self::TYPE_DEPOSIT_BY_ADMIN_FOR_MG => 'Mg'
    ];

    public static $aReduceTypeForWallet = [
        self::TYPE_REDUCE_PT => 'Pt',
        self::TYPE_REDUCE_IM => 'Im',
        self::TYPE_REDUCE_AG => 'Ag',
        self::TYPE_REDUCE_HB => 'Hb',
        self::TYPE_REDUCE_MG => 'Mg'
    ];

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'transaction_types';
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'parent_id',
        'fund_flow_id',
        'description',
        'cn_title',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'available',
        'frozen',
        'withdrawable',
        'credit',
        'debit',
        'reverse_type'
    ];

    public static $resourceName = 'TransactionType';
    public static $titleColumn = 'description';

    public static $ignoreColumnsInEdit = [];
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'description',
        'cn_title',
        'fund_flow_id',
        'total',
        'balance',
        'pt',
        'im',
        'ag',
        'hb',
        'mg',
        'available',
        'frozen',
        'withdrawable',
        'credit',
        'debit',
        'reverse_type'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'fund_flow_id' => 'aFundFlows',
        'total' => 'aFundActions',
        'balance' => 'aFundActions',
        'pt' => 'aFundActions',
        'im' => 'aFundActions',
        'ag' => 'aFundActions',
        'hb' => 'aFundActions',
        'mg' => 'aFundActions',
        'available' => 'aFundActions',
        'frozen' => 'aFundActions',
        'withdrawable' => 'aFundActions',
        'reverse_type' => 'aTransactionTypes'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'credit' => 'desc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'parent_id';

    public static $rules = [
        'description'    => 'required|max:30',
        'cn_title'      => 'required|max:30',
        'fund_flow_id'   => 'required|integer',
        'reverse_type'  => 'integer',
        'total'        => 'required|in:1,0,-1',
        'balance'        => 'required|in:1,0,-1',
        'pt'        => 'required|in:1,0,-1',
        'im'        => 'required|in:1,0,-1',
        'ag'        => 'required|in:1,0,-1',
        'hb'        => 'required|in:1,0,-1',
        'mg'        => 'required|in:1,0,-1',
        'available'      => 'required|in:1,0,-1',
        'frozen'         => 'required|in:1,0,-1',
        'withdrawable'   => 'required|integer',
        'credit'         => 'in:0,1',
        'debit'          => 'in:0,1',
    ];

    protected function beforeValidate(){
        if (!$this->fund_flow_id){
            return false;
        }
        if (!$oFundFlow = FundFlow::find($this->fund_flow_id)){
            return false;
        }
        $this->total = $oFundFlow->total;
        $this->balance = $oFundFlow->balance;
        $this->pt = $oFundFlow->pt;
        $this->im = $oFundFlow->im;
        $this->ag = $oFundFlow->ag;
        $this->hb = $oFundFlow->hb;
        $this->mg = $oFundFlow->mg;
        $this->available = $oFundFlow->available;
        $this->frozen = $oFundFlow->frozen;
//        $this->withdrawable = $oFundFlow->withdrawable;
        $this->credit or $this->credit = 0;
        $this->debit or $this->debit = 0;
        $this->reverse_type or $this->reverse_type = null;
        return parent::beforeValidate();
    }

    public static function getAllTransactionTypes()
    {
        $aColumns = ['id', 'description','cn_title'];
        $aTransactionTypes = self::all($aColumns);
        return $aTransactionTypes;
    }

    public static function getAllTransactionTypesArray()
    {
        $data = [];
        $aTransactionTypes = self::getAllTransactionTypes();
        foreach ($aTransactionTypes as $oTransactionType) {
            $data[$oTransactionType->id] = $oTransactionType->description;
        }
        return $data;
    }

    public static function getFieldsOfAllTransactionTypesArray()
    {
        $data = [];
        $aTransactionTypes = self::getAllTransactionTypes();
        foreach ($aTransactionTypes as $oTransactionType) {
            $data[$oTransactionType->id] = $oTransactionType->cn_title;
        }
        return $data;
    }

    protected function getFriendlyDescriptionAttribute(){
        return __('_transactiontype.' . strtolower(Str::slug($this->attributes[ 'description' ])));
    }
    protected function getTransactionTypeByTypeId($type_id){
        if($data=self::find($type_id)){
            return $data;
        }
        return false;

    }
}