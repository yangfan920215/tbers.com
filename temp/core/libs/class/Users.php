<?php
namespace libs;

use UserUserBankCard;
use UserUser;
use Account;
use ActivityReport;
use SysConfig;
use User;
use Slot;
use UserBetReport;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * 用户操作类
 * Class Users
 * @package libs
 */
class Users {
    public $uid;

    public $oUser;
    
    public function __construct($uid)
    {
        $this->uid = $uid;
        $this->oUser = UserUser::find($this->uid);
    }

    /**
     * 获取玩家钱包余额
     * @param $iWallet
     * @return string
     */
    public function getBalance($iWallet){
        $oAccount = Account::getAccountInfoByUserId($this->uid);

        //
        $aResponse = $this->updateLockByBalance($iWallet, $this->oUser, $oAccount);
        if (!isset($aResponse['status']) || !isset($aResponse['balance'])){
            return false;
        }

        return $aResponse['balance'];
    }

    /**
     *
     * @param $iWallet
     * @param $oUser
     * @param $oAccount
     * @return array
     */
    private function updateLockByBalance($iWallet, & $oUser, & $oAccount){
        // 获取钱包key，例如pt
        $sPlatform = Account::$aPlatform[$iWallet];

        // 主钱包不用获取
        if (!$sPlatform) {
            return ['status' => 0, 'balance' => $oAccount->available];
        }

        $sWallet = strtolower($sPlatform);

        // 查看改渠道的玩家是否存在
        if(!$oUser->checkPlayerExists($sPlatform)){
            return ['status' => 0, 'balance' => $oAccount->$sWallet];
        }

        // 获取该渠道商的单例
        $oApi = $sPlatform::getInstance();

        $sUsernameField = strtolower($sPlatform).'_username';
        $sPasswordField = strtolower($sPlatform).'_password';

        // 调用渠道API查询玩家余额
        $aResponse = $oApi->getBalance($oUser->$sUsernameField, $oUser->$sPasswordField);

        // 如果查询失败，直接使用当前保存数据作为其余额展现
        if (!$aResponse || !$aResponse['status'] || !isset($aResponse['status']) || $aResponse['status'] == 0 || !isset($aResponse['balance'])) {
            return ['status' => 0, 'balance' => $oAccount->$sWallet];
        }

        // 得到第三方渠道的余额
        $fBalance = $aResponse['balance'];
        $oAccount->$sWallet = $fBalance;

        $sPlatformLockField = strtolower($sPlatform . '_lock');

        //  判断用户是否被锁定
        if ($oAccount->$sPlatformLockField == Account::LOCK_TRANSFER_OUT) {
            // 取出玩家查询出最近的一条活动记录
            $oActivityReport = ActivityReport::doWhere([
                'user_id' => ['=', $oUser->id],
                'wallet' => ['=', $iWallet],
            ])->orderBy('id', 'desc')->first();

            if ($fBalance <= SysConfig::readValue('unlock_platform_wallet_min')) {
                $oAccount->$sPlatformLockField = Account::LOCK_NORMAL;
                if ($oActivityReport) {
                    $oActivityReport->unlock_at = date('Y-m-d H:i:s');
                    if ($oActivityReport->save()) {
                        file_put_contents('/tmp/wallet_unlock_by_lt3', '[' . date('Y-m-d H:i:s') . '] user_id:' . $oUser->id . ' '. ' username : '. $oUser->username.' ' . $sWallet . '钱包余额:' . $fBalance . ' 钱包最低解锁金额:' . SysConfig::readValue('unlock_platform_wallet_min') . "\n\r", FILE_APPEND);
                    }
                }
            } else {
                if($oActivityReport){
                    $this->checkActivityStatus($oActivityReport);
                }
            }
        }

        if($oAccount->save()) {
            return ['status' => 1, 'balance' => $fBalance];
        }
    }

    /**
     * 检查活动流水状态
     * @param $oActivityReport
     * @return bool
     */
    private function checkActivityStatus($oActivityReport){
        $iWallet = $oActivityReport->wallet;
        $sUsername = $oActivityReport->username;
        $sReceiveAt = $oActivityReport->receive_at;
        $fTotalBet = $oActivityReport->total_turnover;

        $oUser = User::where('id', $oActivityReport->user_id)->first();
        if (!$oUser) {
            $this->writeLog($sUsername.' 用户不存在');
            return false;
        }

        $oAccount = Account::getAccountInfoByUserId($oUser->id);
        if (!$oAccount) {
            $this->writeLog($sUsername.'账户不存在');
            return false;
        }
        $aFinishBets = [];
        if (Cache::has('finish_bet'))
        {
            $aFinishBets = Cache::get('finish_bet');//此缓存主要解决活动流水跨天的问题，因为通过getReport方法获取回来的数据不能跨天
        }
        $this->writeLog('已完成流水数据 : '.json_encode($aFinishBets));
        if (date('Y-m-d') != date('Y-m-d', strtotime($sReceiveAt))) $sReceiveAt = date('Y-m-d 00:00:00');

        $aResponses = getReport(strtolower(Account::$aPlatform[$iWallet]), $sReceiveAt, date('Y-m-d H:i:s'), $sUsername, '', 'platform,player,game', 'checkActivityStatus');
        if (empty($aResponses)){
            return false;
        }
        $fAvailableBet = 0;
        $fNotAvailableBet = 0;
        $sPlatform = strtolower(Account::$aPlatform[$iWallet]);
        foreach ($aResponses as $aResponse) {
            $sGameCode = $aResponse['gamename'];
            $sPlatform = $aResponse['platform'];
            if ($sPlatform == 'pt') {
                preg_match('/(.*)\((.*)\)/', $aResponse['gamename'], $matches);
                $sGameCode = trim($matches[2]);
            }
            $oSlot = Slot::doWhere([
                'game_code' => ['=', $sGameCode],
                'platform' => ['=', $sPlatform],
                'is_day_commission' => ['=', 1],
                'is_enable' => ['=', 1],
            ])->first();
            if (!$oSlot && $sPlatform != 'ag') {
                $fNotAvailableBet += $aResponse['bet'];
                $this->writeLog('用户名 ： '.$sUsername.' 游戏名为 ： '.$aResponse['gamename'].'的投注不是有效流水, 金额：'.$aResponse['bet']);
                continue;
            }

            $this->writeLog('用户名 ： '.$sUsername.' 无效流水总金额：'.$fNotAvailableBet);
            $fAvailableBet += $aResponse['bet'];
            $this->writeLog('用户名 ： '.$sUsername.' 有效效流水总金额：'.$fAvailableBet);
        }

        $aFinishBets[$sUsername][strtolower(Account::$aPlatform[$iWallet])][date('Ymd')] = $fAvailableBet;
        $fAvailableBet = array_sum($aFinishBets[$sUsername][strtolower(Account::$aPlatform[$iWallet])]);

        if ($fAvailableBet >= $fTotalBet) {
            DB::beginTransaction();
            $oActivityReport->unlock_at = date('Y-m-d H:i:s');
            $oActivityReport->finish_at = date('Y-m-d H:i:s');
            $oActivityReport->status = ActivityReport::STATUS_FINISH;
            $oActivityReport->finished_turnover = $fAvailableBet;

            if ($bSuccActivityReport = $oActivityReport->save()){
                $this->writeLog($sUsername.' '.$sPlatform.'活动报表更新成功！');
            }

            $sLock = $sPlatform.'_lock';
            $oAccount->$sLock = Account::LOCK_NORMAL;
            if ($bSuccAccount = $oAccount->save()){
                $this->writeLog($sUsername.' '.$sPlatform.'解锁成功！');
            }

            if($bSuccActivityReport && $bSuccAccount){
                DB::commit();
                unset($aFinishBets[$sUsername][strtolower(Account::$aPlatform[$iWallet])]);
            } else {
                DB::rollback();
            }
        }
        Cache::forever('finish_bet', $aFinishBets);
    }

    protected function writeLog($msg, $sFileName = 'unlock') {
        !is_array($msg) or $msg = var_export($msg, true);
        // file_put_contents('/tmp/bet', $msg . "\n", FILE_APPEND);
        $sFile = implode(DIRECTORY_SEPARATOR, ['/tmp', date("YmdH")]);
        if (!file_exists($sFile)) {
            @mkdir($sFile, 0777, true);
        }
        file_put_contents($sFile . '/'.$sFileName, $msg . "\n", FILE_APPEND);
    }



    /**
     * 获取玩家最近玩过的游戏
     * @param $uid
     * @return mixed
     */
    public function getUgames(){
        return UserBetReport::getUgamesByUid($this->uid);
    }

    /**
     * 用户是否设置资金密码
     * @return bool
     */
    public function isSetFundPassword(){
        return isset($this->oUser->fund_password) && $this->oUser->fund_password !== '' ? true : false;
    }

    public function isBlockFund(){
        return $this->oUser->blocked == UserUser::BLOCK_FUND_OPERATE ? true : false;
    }


    /**
     * 获取用户绑定的银行卡信息
     * @return array
     */
    public function getBankCards(){
        return UserUserBankCard::getUserCards($this->uid)->get()->toArray();
    }


}
