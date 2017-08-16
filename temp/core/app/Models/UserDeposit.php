<?php

class UserDeposit extends Deposit {

    const ERRNO_DEPOSIT_ERROR_00 = -1800;
    const ERRNO_DEPOSIT_ERROR_01 = -1801;
    const ERRNO_DEPOSIT_ERROR_02 = -1802;
    const ERRNO_DEPOSIT_ERROR_03 = -1803;
    const ERRNO_DEPOSIT_ERROR_04 = -1804;
    const ERRNO_DEPOSIT_ERROR_05 = -1805;
    const ERRNO_DEPOSIT_ERROR_06 = -1806;
    const ERRNO_DEPOSIT_ERROR_07 = -1807;
    const ERRNO_DEPOSIT_ERROR_08 = -1808;
    const ERRNO_DEPOSIT_ERROR_09 = -1809;
    const ERRNO_DEPOSIT_UNKNOWN_ERROR = -1810;
    const ERRNO_DEPOSIT_AMOUNT_OUT_RANGE = -1811;

    public static $columnForList = [
    ];

    /**
     * 生成平台充值订单号
     * @return string
     */
    public static function getDepositOrderNum() {
        return uniqid(mt_rand());
    }

    public static function getUserLatestDepositRecord($iCount = 1) {
        $iUserId = Session::get('user_id');
        $aColumns = ['id', 'amount'];
        $oQuery = self::where('user_id', '=', $iUserId)->where('status', '=', self::DEPOSIT_STATUS_SUCCESS);
        $aRecords = isset($oQuery) ? $oQuery->orderBy('updated_at', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aRecords;
    }

}
