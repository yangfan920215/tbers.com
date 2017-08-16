<?php

use libs\DbTool;

class Account extends BaseModel {

    const ERRNO_LOCK_FAILED = -120;
    const RELEASE_DEAD_LOCK_NONE = 0;
    const RELEASE_DEAD_LOCK_RUNNING = 1;
    const RELEASE_DEAD_LOCK_SUCCESS = 2;
    const RELEASE_DEAD_LOCK_FAILED = 3;

    public static $releaseDeadLockMessages = [
        self::RELEASE_DEAD_LOCK_NONE => 'Unlocked',
        self::RELEASE_DEAD_LOCK_RUNNING => 'The Locker is Still Runing',
        self::RELEASE_DEAD_LOCK_SUCCESS => 'Released',
        self::RELEASE_DEAD_LOCK_FAILED => 'Unlock Failed!!!',
    ];

    const LOCK_NORMAL = 0;
    const LOCK_TRANSFER_OUT = 1;
    const LOCK_TRANSFER_IN = 2;
    const LOCK_TRANSFER_BOTH = 3;

    public static $aPlatformLock = [
        self::LOCK_NORMAL => 'normal',
        self::LOCK_TRANSFER_OUT => 'transfer out',
        self::LOCK_TRANSFER_IN => 'transfer in',
        self::LOCK_TRANSFER_BOTH => 'both',
    ];


    public static $resourceName = 'Account';
    public static $amountAccuracy = 2;

    public static $ignoreColumnsInView = [
        'id',
        'user_id',
        'locked',
        'status',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
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
    ];
    public static $listColumnMaps = [
        'total' => 'total_formatted',
        'balance' => 'balance_formatted',
        'pt' => 'pt_formatted',
        'im' => 'im_formatted',
        'ag' => 'ag_formatted',
        'hb' => 'hb_formatted',
        'mg' => 'mg_formatted',
        'frozen' => 'frozen_formatted',
        'available' => 'available_formatted',
        'withdrawable' => 'withdrawable_formatted',
    ];
    public static $viewColumnMaps = [
        'balance' => 'balance_formatted',
        'frozen' => 'frozen_formatted',
        'available' => 'available_formatted',
        'withdrawable' => 'withdrawable_formatted',
        'pt' => 'pt_formatted',
        'im' => 'im_formatted',
        'ag' => 'ag_formatted',
        'hb' => 'hb_formatted',
        'mg' => 'mg_formatted',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'username' => 'required',
        'balance' => 'numeric',
        'pt' => 'numeric',
        'im' => 'numeric',
        'ag' => 'numeric',
        'hb' => 'numeric',
        'mg' => 'numeric',
        'frozen' => 'numeric',
        'available' => 'numeric',
        'withdrawable' => 'numeric',
        'status' => 'required|integer',
        'locked' => 'integer',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'total',
        'balance',
        'pt',
        'pt_lock',
        'im',
        'im_lock',
        'ag',
        'ag_lock',
        'hb',
        'hb_lock',
        'mg',
        'mg_lock',
        'frozen',
        'available',
        'withdrawable',
        'status',
        'locked',
    ];

    const WALLET_MAIN = 1;
    const WALLET_PT = 2;
    const WALLET_IM = 3;
    const WALLET_AG = 4;
    const WALLET_HB = 5;
    const WALLET_MG = 6;

    public static $aWallets = [
        self::WALLET_MAIN => '主钱包',
        self::WALLET_PT => 'PT钱包',
        self::WALLET_IM => 'IM钱包',
        self::WALLET_AG => 'AG钱包',
        self::WALLET_HB => 'HB钱包',
        self::WALLET_MG => 'MG钱包',
    ];

    public static $aWalletForPlatform = [
        Slot::PLATFORM_PT => self::WALLET_PT,
        Slot::PLATFORM_IM => self::WALLET_IM,
        Slot::PLATFORM_AG => self::WALLET_AG,
        Slot::PLATFORM_MAIN => self::WALLET_MAIN,
        Slot::PLATFORM_HB => self::WALLET_HB,
        Slot::PLATFORM_MG => self::WALLET_MG,
    ];

    public static $aPlatformId = [
        self::WALLET_PT => Slot::PLATFORM_PT,
        self::WALLET_IM => Slot::PLATFORM_IM,
        self::WALLET_AG => Slot::PLATFORM_AG,
        self::WALLET_MAIN => Slot::PLATFORM_MAIN,
        self::WALLET_HB => Slot::PLATFORM_HB,
        self::WALLET_MG => Slot::PLATFORM_MG,
    ];

    public static $aPlatform = [
        self::WALLET_PT => 'Pt',
        self::WALLET_IM => 'Im',
        self::WALLET_AG => 'Ag',
        self::WALLET_MAIN => '',
        self::WALLET_HB => 'Hb',
        self::WALLET_MG => 'Mg',
    ];

    public static $aTransactionType = [
        "IN" => [
            self::WALLET_PT => TransactionType::TYPE_TRANSFER_TO_PT,
            self::WALLET_IM => TransactionType::TYPE_TRANSFER_TO_IM,
            self::WALLET_AG => TransactionType::TYPE_TRANSFER_TO_AG,
            self::WALLET_MAIN => '',
            self::WALLET_HB => TransactionType::TYPE_TRANSFER_TO_HB,
            self::WALLET_MG => TransactionType::TYPE_TRANSFER_TO_MG,
        ],
        "OUT" => [
            self::WALLET_PT => TransactionType::TYPE_PT_TO_BALANCE,
            self::WALLET_IM => TransactionType::TYPE_IM_TO_BALANCE,
            self::WALLET_AG => TransactionType::TYPE_AG_TO_BALANCE,
            self::WALLET_MAIN => '',
            self::WALLET_HB => TransactionType::TYPE_HB_TO_BALANCE,
            self::WALLET_MG => TransactionType::TYPE_MG_TO_BALANCE,
        ]
    ];


    public static $aBonusTransactionType = [
        self::WALLET_PT => TransactionType::TYPE_BONUS_FOR_PT,
        self::WALLET_IM => TransactionType::TYPE_BONUS_FOR_IM,
        self::WALLET_AG => TransactionType::TYPE_BONUS_FOR_AG,
        self::WALLET_MAIN => '',
        self::WALLET_HB => TransactionType::TYPE_BONUS_FOR_HB,
        self::WALLET_MG => TransactionType::TYPE_BONUS_FOR_MG,
    ];

    protected $table = 'accounts';

//    public static $columns = include(app_path() . '/lang/en/basic.php');

    protected function getTotalFormattedAttribute() {
        $this->total = $this->im + $this->pt + $this->ag + $this->available + $this->hb + $this->mg;
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

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen');
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available');
    }

    /**
     * [getWithdrawableFormattedAttribute User's withdrawal money is the min value between available and withdrawal]
     * @return [Float] [Formatted number]
     */
    protected function getWithdrawableFormattedAttribute() {
        $this->attributes['withdrawable'] = $this->attributes['withdrawable'] >= $this->attributes['total'] ? $this->attributes['total'] : $this->attributes['withdrawable'];
        return number_format($this->attributes['withdrawable'], static::$amountAccuracy);
    }

    protected function getTrueWithdrawableAttribute() {
        $this->attributes['withdrawable'] = $this->attributes['withdrawable'] >= $this->attributes['total'] ? $this->attributes['total'] : $this->attributes['withdrawable'];
        return formatNumber($this->attributes['withdrawable'], static::$amountAccuracy);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getAccountInfoByUserId($mUserID, $aColunms = ['*']) {
        if (!$mUserID)
            return false;
        if (is_array($mUserID)) {
            return self::whereIn('user_id', $mUserID)->get($aColunms);
        } else {
            return self::where('user_id', '=', $mUserID)->first($aColunms);
        }
    }

    /**
     * 获取指定用户的可用余额
     * @param int $user_id
     * @return float
     */
    public static function getAvaliable($user_id) {
        $oAccount = self::getAccountInfoByUserId($user_id,['available']);
        return number_format($oAccount->available, 6, '.', '');
    }

    public function checkBalance() {
        return $this->balance == number_format($this->frozen + $this->available, static::$amountAccuracy, '.', '');
    }

    /**
     * Lock Account
     * @param int $id
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lock($id, & $iLocker)
    {
        $iThreadId = DbTool::getDbThreadId();

        $iCount = self::where('id', '=', $id)->where('locked', '=', 0)->update(['locked' => $iThreadId]);

        if ($iCount > 0)
        {
            $iLocker = $iThreadId;
            return self::find($id);
        } 
        else
        {
            self::addReleaseLockTask($id);
        }

        return false;
    }

    /**
     * Lock Account By User ID
     * @param int $iUserId
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lockByUserId($iUserId, & $iLocker) {
        $iThreadId = DbTool::getDbThreadId();
        $iCount = self::where('user_id', '=', $iUserId)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
        if ($iCount > 0) {
            $iLocker = $iThreadId;
            return self::where('user_id', '=', $iUserId)->get()->first();
        }
        return false;
    }

    /**
     * 一次性对多个账户加锁
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function lockManyOfUsers($aUserIds, & $iLocker) {
        if (empty($aUserIds)) {
            return false;
        }
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
//        pr($aUserIds);
//        pr($iCount);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = self::whereIn('user_id', $aUserIds)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
//        pr($iLockedCount);
//        exit;
        if ($iLockedCount == $iCount) {
            $iLocker = $iThreadId;
            return self::whereIn('user_id', $aUserIds)->get();
        }
        return false;
    }

    /**
     * 一次性解锁多个账户
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function unlockManyOfUsers($aUserIds, $iLocker) {
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = self::whereIn('user_id', $aUserIds)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        return $iLockedCount == $iCount;
    }

    /**
     * Unlock Account
     * @param int $id
     * @param int $iLocker
     * @param bool $bReturnObject
     * @return Account|boolean
     */
    public static function unLock($id, & $iLocker, $bReturnObject = true) {
        if (empty($iLocker))
            return true;
        $iCount = self::where('id', '=', $id)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        if ($iCount > 0) {
            $iLocker = 0;
            return $bReturnObject ? self::find($id) : true;
        }
        return false;
    }

    /**
     * [getLockedAccounts Get all locked accounts]
     * @return [Object Array] [Locked accounts array]
     */
    public static function getLockedAccounts() {
        return self::where('locked', '>', 0)->get(['id', 'locked']);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getUserIdsByAvailable($fFromAccount, $fToAccount) {
        if (!empty($fFromAccount) && !empty($fToAccount)) {
            $aConditions['available'] = [ 'between', [$fFromAccount, $fToAccount]];
        } else if (!empty($fFromAccount)) {
            $aConditions['available'] = [ '>=', $fFromAccount];
        } else if (!empty($fToAccount)) {
            $aConditions['available'] = [ '<=', $fToAccount];
        }
        $aUserIds = [];
        if (isset($aConditions)) {
            $aColumns = ['id', 'user_id'];
            $oQuery = self::doWhere($aConditions);
            $aAccounts = $oQuery->get($aColumns);
            foreach ($aAccounts as $oAccount) {
                $aUserIds[] = $oAccount->user_id;
            }
        }
        return $aUserIds;
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getUserIdsByTotalBalance($fFromAccount, $fToAccount) {
        if (!empty($fFromAccount) && !empty($fToAccount)) {
            $aConditions['total_balance'] = [ 'between', [$fFromAccount, $fToAccount]];
        } else if (!empty($fFromAccount)) {
            $aConditions['total_balance'] = [ '>=', $fFromAccount];
        } else if (!empty($fToAccount)) {
            $aConditions['total_balance'] = [ '<=', $fToAccount];
        }
        $aUserIds = [];
        if (isset($aConditions)) {
            $aColumns = ['id', 'user_id'];
            $oQuery = self::doWhere($aConditions);
            $aAccounts = $oQuery->get($aColumns);
            foreach ($aAccounts as $oAccount) {
                $aUserIds[] = $oAccount->user_id;
            }
        }
        return $aUserIds;
    }

    /**
     * 获取实际可提现余额，即账户余额和可提现余额中较小的金额
     * @return float
     */
    public function getWithdrawableAmount() {
        $this->attributes['withdrawable'] = $this->attributes['withdrawable'] >= $this->attributes['total'] ? $this->attributes['total'] : $this->attributes['withdrawable'];
        return $this->attributes['withdrawable'];
    }

    /**
     * 强制解锁，用于解开未及时解开的锁。
     * 强烈提示：本方法不检查加锁者是否是当前进程，因此，需特别小心！！
     * @param int $id
     * @param int $iLocker
     * @return int
     *      self;:RELEASE_DEAD_LOCK_NONE: 未锁定
     *      self;:RELEASE_DEAD_LOCK_RUNNING：加锁的进程仍在运行中
     *      self;:RELEASE_DEAD_LOCK_SUCCESS：解锁成功
     *      self::RELEASE_DEAD_LOCK_FAILED：解锁失败
     */
    public static function releaseDeadLock($id, $iLocker = null) {
        !is_null($iLocker) or $iLocker = self::getLocker($id);
        if (!$iLocker) {
            return self::RELEASE_DEAD_LOCK_NONE;
        }
        $aDbThreads = DbTool::getDbThreads();
        if (!in_array($iLocker, $aDbThreads)) {
            return self::unLock($id, $iLocker, false) ? self::RELEASE_DEAD_LOCK_SUCCESS : self::RELEASE_DEAD_LOCK_FAILED;
        }
        return self::RELEASE_DEAD_LOCK_RUNNING;
    }

    /**
     * 返回加锁者
     * @param int $id
     * @return int | false
     */
    private static function getLocker($id) {
        if (empty($id)) {
            return false;
        }
        $oAccount = self::where('id', '=', $id)->get(['locked'])->first();
        return is_object($oAccount) ? $oAccount->getAttribute('locked') : false;
    }

    /**
     * 向队列增加解锁任务
     * @param int $id
     * @return bool
     */
    public static function addReleaseLockTask($id) {
        return BaseTask::addTask('ReleaseDeadAccountLock', ['id' => $id], 'account');
    }

    public function setWithdrawable($fAddAmount) {
        $this->withdrawable += $fAddAmount;
        return $this->save();
    }

}
