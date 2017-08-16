<?php


require_once '../libs/class/nusoap.php';
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-10-20
 * Time: 下午1:11
 */
class Hb implements Api
{
    //正式
    private $sBrandId;
    private $sApiKey;
    private $sPlayerHostAddress;
    private $sSoapUrl;
    private $sHaBaLaunchUrl;
    //测试
//    private $sBrandId = 'd2ab7795-86a1-e611-80d5-000d3a802d1d';
//    private $sApiKey = '04998D77-BDFE-444E-8B3C-0B7B0DBA470B';
//    private $sPlayerHostAddress = 'http://tiger.user';
//    private $sSoapUrl = 'https://ws-test.insvr.com/hosted.asmx?WSDL';
//    private $sHaBaLaunchUrl = 'https://app-test.insvr.com';
    private static $_instance;
    private static $sToken;
    private function __construct(){
        $this->sBrandId = SysConfig::readValue('hb_brand_id');
        $this->sApiKey = SysConfig::readValue('hb_api_key');
        $this->sPlayerHostAddress = SysConfig::readValue('hb_player_host_address');
        $this->sSoapUrl = SysConfig::readValue('hb_soap_url');
        $this->sHaBaLaunchUrl = SysConfig::readValue('hb_launch_url');
    }

    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }

    public function launchGame($sUsername, $sPassword, $sGameCode){
        if (!$sUsername || !$sPassword || !$sGameCode) return 'fail';
        file_put_contents('/tmp/hb_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword.' url : '.$this->getLaunchGameUrl($sGameCode, $sUsername, $sPassword)."\n\r", FILE_APPEND);
        return $this->getLaunchGameUrl($sGameCode, $sUsername, $sPassword);
    }

    public function launchFreeGame($sUsername, $sPassword, $sGameCode){
        if (!$sUsername || !$sPassword || !$sGameCode) return 'fail';
        file_put_contents('/tmp/hb_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword.' url : '.$this->getLaunchGameUrl($sGameCode, $sUsername, $sPassword, 'fun')."\n\r", FILE_APPEND);
        header("location: ".$this->getLaunchGameUrl($sGameCode, $sUsername, $sPassword, 'fun'));
    }

    public function createPlayer($sUsername, $sPassword)
    {
        $aResponses = $this->queryPlayer($sUsername, $sPassword);
        if (!$aResponses['Found']){
            $aResponses = $this->loginOrCreatePlayer($sUsername, $sPassword);
            if(!$aResponses['PlayerCreated']) return false;
        }
        return true;
    }

    private function getLaunchGameUrl($sBrandGameId, $sUsername, $sPassword, $sMode = 'real', $sCurrencyCode = 'CNY', $sLocale = 'zh-CN'){
        if (!self::$sToken) {
            $aLoginOrCreatePlayer = $this->loginOrCreatePlayer($sUsername, $sPassword, $sCurrencyCode);
            self::$sToken = $aLoginOrCreatePlayer['Token'];
        }

        return $this->sHaBaLaunchUrl.'/play?brandid='.$this->sBrandId.'&keyname='.$sBrandGameId.'&token='.self::$sToken.'&mode='.$sMode.'&locale='.$sLocale.'&lobbyurl='.$this->sPlayerHostAddress;
    }

    public function viewGraphicalGameResult($sGameInstanceId, $sLocale = 'zh-CN'){
        $sHash = strtolower($sGameInstanceId.$this->sBrandId.$this->sApiKey);
        return $this->sHaBaLaunchUrl.'/games/historybrandid='.$this->sBrandId.'&gameinstanceid='.$sGameInstanceId.'&hash='.$sHash.'&locale='.$sLocale.'&viewType=game';
    }

    public function viewPlayerHistory($sUsername, $sDtStartUTC = '', $sDtEndUTC = '', $sKeyName = '', $sLocale = 'zh-CN'){
        $sHash = strtolower($sUsername.$this->sBrandId.$this->sApiKey);
        $sViewPlayerHistoryUrl = $this->sHaBaLaunchUrl.'/games/historybrandid='.$this->sBrandId.'&username='.$sUsername.'&hash='.$sHash.'&locale='.$sLocale.'&viewType=player';
        if ($sDtStartUTC) $sViewPlayerHistoryUrl .= $sDtStartUTC;
        if ($sDtEndUTC) $sViewPlayerHistoryUrl .= $sDtEndUTC;
        if ($sKeyName) $sViewPlayerHistoryUrl .= $sKeyName;
        return $sViewPlayerHistoryUrl;
    }

    public function getJackpots(){
        $oParam = (object)[];
        return $this->sendRequest('GetJackpots', $oParam);
    }

    public function getJackpotGameLink(){
        $oParam = (object)[];
        return $this->sendRequest('GetJackpotGameLink', $oParam);
    }

    public function reportJackpotContribution($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];
        return $this->sendRequest('ReportJackpotContribution', $oParam);
    }

    public function reportJackpotContributionPerGame($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];
        return $this->sendRequest('ReportJackpotContributionPerGame', $oParam);
    }

    public function getGames(){
        $oParam = (object)[];
        $aResponses = $this->sendRequest('GetGames', $oParam);
        return $aResponses['Games']['GameClientDbDTO'];
    }

    public function loginOrCreatePlayer($sUsername, $sPassword, $sCurrencyCode = 'CNY'){
        $oParam = (object)[
            'Username' => $sUsername,
            'Password' => $sPassword,
            'CurrencyCode' => $sCurrencyCode,
        ];
        return $this->sendRequest('LoginOrCreatePlayer', $oParam);
    }

    public function updatePlayerPassword($sUsername, $sNewPassword){
        $oParam = (object)[
            'Username' => $sUsername,
            'NewPassword' => $sNewPassword,
        ];
        return $this->sendRequest('UpdatePlayerPassword', $oParam);
    }

    function transaction($sLoginName, $sPassword, $sBillNo, $iType, $fCredit)
    {
        $aResponse = '';
        if ($iType == "IN") {
            $aResponse = $this->depositPlayerMoney($sLoginName, $sPassword, $fCredit, $sBillNo);
        }elseif($iType == "OUT"){
            $aResponse = $this->withdrawPlayerMoney($sLoginName, $sPassword, $fCredit, $sBillNo);
        }
        file_put_contents('/tmp/hb_transaction_'.date('Y-m-d'), '['.date('H:i:s').']'.' loginName : '.$sLoginName.', billNo : '.$sBillNo.', type : '.$iType.', credit : '.$fCredit.'reponse : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Success'] == 'true') return true;
        else return false;
    }

    public function depositPlayerMoney($sUsername, $sPassword, $fAmount, $sRequestId, $sCurrencyCode = 'CNY'){
        $oParam = (object)[
            'Username' => $sUsername,
            'Password' => $sPassword,
            'CurrencyCode' => $sCurrencyCode,
            'Amount' => $fAmount,
            'RequestId' => $sRequestId
        ];

        return $this->sendRequest('DepositPlayerMoney', $oParam);
    }

    public function withdrawPlayerMoney($sUsername, $sPassword, $fAmount, $sRequestId, $bWithdrawAll = false, $sCurrencyCode = 'CNY'){
        $oParam = (object)[
            'Username' => $sUsername,
            'Password' => $sPassword,
            'CurrencyCode' => $sCurrencyCode,
            'Amount' => $fAmount * -1,
            'RequestId' => $sRequestId,
            'WithdrawAll' => $bWithdrawAll
        ];

        return $this->sendRequest('WithdrawPlayerMoney', $oParam);
    }

    public function queryTransfer($sRequestId){
        $oParam = (object)[
            'RequestId' => $sRequestId,
        ];

        return $this->sendRequest('QueryTransfer', $oParam);
    }

    public function queryPlayer($sUsername, $sPassword){
        $oParam = (object)[
            'Username' => $sUsername,
            'Password' => $sPassword,
        ];

        return $this->sendRequest('QueryPlayer', $oParam);
    }

    public function getBonusAvailablePlayer($sUsername){
        $oParam = (object)[
            'Username' => $sUsername,
        ];

        return $this->sendRequest('GetBonusAvailablePlayer', $oParam);
    }

    public function applyBonusToPlayer($sUsername, $sCode, $bReplaceActiveCoupon = false){
        $oParam = (object)[
            'Username' => $sUsername,
            'Code' => $sCode,
            'ReplaceActiveCoupon' => $bReplaceActiveCoupon,
        ];

        return $this->sendRequest('ApplyBonusToPlayer', $oParam);
    }

    public function logoutPlayer($sUsername, $sPassword){
        $oParam = (object)[
            'Username' => $sUsername,
            'Password' => $sPassword,
        ];

        return $this->sendRequest('LogoutPlayer', $oParam);
    }

    public function getPlayerTransferTransactions($sUsername, $sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'Username' => $sUsername,
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetPlayerTransferTransactions', $oParam);
    }

    public function getPlayerGameResults($sUsername, $sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'Username' => $sUsername,
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetPlayerGameResults', $oParam);
    }

    public function getPlayerStakePayoutSummary($sUsername, $sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'Username' => $sUsername,
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetPlayerStakePayoutSummary', $oParam);
    }

    public function reportGameOverviewPlayer($sUsername, $sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'Username' => $sUsername,
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('ReportGameOverviewPlayer', $oParam);
    }

    public function reportPlayerStakePayout($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('ReportPlayerStakePayout', $oParam);
    }

    public function reportGameOverviewBrand($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('ReportGameOverviewBrand', $oParam);
    }

    public function getBrandCompletedGameResults($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];
        $aResponse = $this->sendRequest('GetBrandCompletedGameResults', $oParam);
        return $aResponse;
    }

    public function getGroupCompletedGameResults($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetGroupCompletedGameResults', $oParam);
    }

    public function getBrandTransferTransactions($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetBrandTransferTransactions', $oParam);
    }

    public function getGroupTransferTransactions($sDtStartUTC = '20161104000000', $sDtEndUTC = ''){
        if(!$sDtEndUTC) $sDtEndUTC = date('YmdHis');
        $oParam = (object)[
            'DtStartUTC' => $sDtStartUTC,
            'DtEndUTC' => $sDtEndUTC,
        ];

        return $this->sendRequest('GetGroupTransferTransactions', $oParam);
    }

    private function sendRequest($sAction, $oParam){
        file_put_contents('/tmp/hb_'.date('Y-m-d'), '['.date('H:i:s').'] request param : '.json_encode($oParam).' request url : '.$sAction."\n\r", FILE_APPEND);
        $oClient = new SoapClient($this->sSoapUrl, array("connection_timeout" => 30));
        $oParam->BrandId = $this->sBrandId;
        $oParam->APIKey = $this->sApiKey;
        $oParam->PlayerHostAddress = $this->sPlayerHostAddress;
        $aResponse = $oClient->$sAction(array("req" => $oParam));
        file_put_contents('/tmp/hb_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        $aResponse = json_decode(json_encode($aResponse), true);
        return $aResponse[$sAction.'Result'];
    }

    function getBalance($sLoginName, $sPassword)
    {
        $aResponse = $this->queryPlayer($sLoginName, $sPassword);
        file_put_contents('/tmp/hb_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sLoginName.' response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Found']) return ['status' => 1, 'balance' => $aResponse['RealBalance']];
        return ['status' => 0];
    }

    function resetPassword($sUsername, $sNewPassword, $sOldPassword = '')
    {
        $aResponse = $this->updatePlayerPassword($sUsername, $sNewPassword);
        file_put_contents('/tmp/hb_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Success']) return true;
        return false;
    }
}
