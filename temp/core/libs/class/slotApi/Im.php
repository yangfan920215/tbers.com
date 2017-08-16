<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-11-16
 * Time: 上午8:43
 */
class Im  implements Api
{

    const PLATFORM_PT = 0;
    const PLATFORM_IMSLOT = 2;
    protected $sHost;
    protected $iProductType;
    protected $sLanguage;
    private static $_instance;
    private $sMerchantCode;
    private $sMerchantName;

    private function __construct(){
        $this->sHost = SysConfig::readValue('im_host');
        $this->iProductType = self::PLATFORM_IMSLOT;
        $this->sLanguage = 'ZH';
        $this->sMerchantCode = SysConfig::readValue('im_merchant_code');
        $this->sMerchantName = SysConfig::readValue('im_merchant_name');
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

    function createPlayer($sMemberCode, $sPassword, $sCurrency = "CNY")
    {
        $sAction = '/player/createplayer';
        $aParam = [
            'membercode' => $sMemberCode,
            'password' => $sPassword,
            'currency' => $sCurrency
        ];

        $aResponses = $this->execApi($sAction, 'POST', $aParam);
        file_put_contents('/tmp/im_create_player', ' username : '.$sMemberCode.'--->'.json_encode($aResponses).' count:'.strlen($sPassword)."\n\r", FILE_APPEND);
        if ($aResponses['Code'] != 0) {
            file_put_contents('/tmp/im_create_player', 'create fail, username : '.$sMemberCode."\n\r", FILE_APPEND);
            return false;
        }
        return true;
    }

    function getBalance($sMemberCode, $sPassword)
    {
        $sAction = '/account/getbalance/membercode/'.$sMemberCode.'/producttype/'.$this->iProductType;
        $aResponse = $this->execApi($sAction, 'GET');
        file_put_contents('/tmp/im_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Code'] == 0) return ['status' => 1, 'balance' => $aResponse['Balance']];
        else return ['status' => 0];
    }

    function resetPassword($sMemberCode, $sPassword, $sOldPassword = '')
    {
        $sAction = '/player/resetpassword';

        $aParam = [
            'membercode' => $sMemberCode,
            'password' => $sPassword,
        ];

        $aResponse = $this->execApi($sAction, 'PUT', $aParam);
        if ($aResponse['Code'] == 0) return true;
        return false;
    }

    function transaction($sMemberCode, $sPassword, $sExternalTransactionId, $iType, $fAmount)
    {
        $sAction = '/chip/createtransaction';
        $iSign = $iType == "IN" ? 1 : -1;
        $aParam = [
            'membercode' => $sMemberCode,
            'amount' => $fAmount * $iSign,
            'externaltransactionid' => $sExternalTransactionId,
            'producttype' => $this->iProductType,
        ];
        $aResponse = $this->execApi($sAction, 'POST', $aParam);
        file_put_contents('/tmp/im_transaction_'.date('Y-m-d'), '['.date('H:i:s').']'.'params : '.json_encode($aParam).' reponse : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Code'] == 0) return true;
        else return false;
    }

    function launchGame($sUsername, $sPassword, $sGameCode)
    {
        file_put_contents('/tmp/im_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword."\n\r", FILE_APPEND);
        $aResponse = $this->launchGame1($sUsername, $sGameCode);
        if($this->iProductType == self::PLATFORM_PT){
            return $aResponse;
        }
        if ($aResponse['Code'] != 0 || empty($aResponse) || !isset($aResponse['GameUrl'])){
            return 'fail';
        }
        file_put_contents('/tmp/im_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] '.$aResponse['GameUrl']."\n\r", FILE_APPEND);
        return $aResponse['GameUrl'];
    }

    function launchFreeGame($sUsername, $sPassword, $sGameCode)
    {
        $aResponse = $this->launchFreeGame1($sGameCode);
        if($this->iProductType == self::PLATFORM_PT)
            return $aResponse;
        if ($aResponse['Code'] != 0) return 'fail';
        header("location: ".$aResponse['GameUrl']);
    }

    /**
     * 进入游戏API
     * @param $sMemberCode 玩家用户名
     * @param $sGameCode 游戏编码
     * @param $sIpAddress ip地址
     */
    public function launchGame1($sMemberCode, $sGameCode){
        $sAction = '/game/launchgame';

        $aParam = [
            'membercode' => $sMemberCode,
            'gamecode' => $sGameCode,
            'language' => $this->sLanguage,
            'ipaddress' => get_client_ip(),
            'producttype' => $this->iProductType,
        ];
        $aResponses = $this->execApi($sAction, 'POST', $aParam);
        return $aResponses;
    }

    /**
     * 试玩接口
     * @param $sGameCode 游戏编码
     * @param $sIpAddress ip地址
     */
    public function launchFreeGame1($sGameCode){
        $sAction = '/game/launchfreegame';

        $aParam = [
            'gamecode' => $sGameCode,
            'language' => $this->sLanguage,
            'ipaddress' => get_client_ip(),
            'producttype' => $this->iProductType,
        ];
        $aResponses = $this->execApi($sAction, 'POST', $aParam);

        return $aResponses;
    }

    /**
     * 手机进入游戏API
     * @param $sMemberCode
     * @param $sGameCode
     * @param $sIpAddress
     */
    public function launchMobileGame($sMemberCode, $sGameCode){
        $sAction = '/game/launchmobilegame';

        $aParam = [
            'membercode' => $sMemberCode,
            'gamecode' => $sGameCode,
            'language' => $this->sLanguage,
            'ipaddress' => get_client_ip(),
            'producttype' => $this->iProductType,
        ];

        $aResponse = $this->execApi($sAction, 'POST', $aParam);

        if($aResponse['Code'] != 0) return false;
        return $aResponse['GameUrl'];
    }

    /**
     * 手机试玩
     * @param $sGameCode 游戏编码
     * @param $sIpAddress ip地址
     */
    public function launchFreeMobileGame($sGameCode){
        $sAction = '/game/launchfreemobilegame';

        $aParam = [
            'gamecode' => $sGameCode,
            'language' => $this->sLanguage,
            'ipaddress' => get_client_ip(),
            'producttype' => $this->iProductType,
        ];

        return $this->execApi($sAction, 'POST', $aParam);
    }

    /** 所有投注记录
     * @param $sStartDate
     * @param $sEndDate
     * @param $sCurrency
     * @return mixed
     */
    public function getAllBetLog($sStartDate, $sEndDate, $sCurrency = 'CNY'){
        $sAction = '/report/getbetlog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/page/1'.'/producttype/'.$this->iProductType.'/currency/'.$sCurrency;
        return $this->execApi($sAction, 'GET');
    }

    /**
     * 检查存取状态
     * @param $sMemberCode 玩家用户名
     * @param $sExternaltransactionId 订单号
     */
    public function checkTransaction($sMemberCode, $sExternalTransactionId){
        $sAction = '/chip/checktransaction/membercode/'.$sMemberCode.'/externaltransactionid/'.$sExternalTransactionId.'/producttype/'.$this->iProductType;
        return $this->execApi($sAction, 'GET');
    }

    /**
     * 检查玩家是否存在
     * @param $sMemberCode 玩家用户名
     */
    public function checkPlayerExists($sMemberCode){
        $sAction = '/player/checkplayerexists/membercode/'.$sMemberCode;
        return $this->execApi($sAction, 'GET');
    }

    /** 获取累计奖金
     * @param string $sCurrency
     * @return mixed
     */
    public function getJackpotList($sCurrency = 'CNY'){
        $sAction = '/casino/getjackpotlist/currency/'.$sCurrency.'/producttype/'.$this->iProductType;
        return $this->execApi($sAction, 'GET');
    }

    public function execApi($sAction, $sRequestMethod, $aParam = []){
        $this->sApiUrl = $this->sHost.$sAction;

        $aResponse = $this->sendRequest($aParam, '', $sRequestMethod);

        if($aResponse['http_info']['http_code'] == 200){
            return $aResponse['response'];
        }else{
            return false;
        }
    }

    private function sendRequest($aParam, $sApiType="", $sRequestMethod="POST", $sDataType="JSON"){
        $ch=curl_init();
        $header = [];
        $header[] = "merchantname:".$this->sMerchantName;
        $header[] = "merchantcode:".$this->sMerchantCode;
        if($sDataType == "XML")
        {
            $header[] = "Content-Type:text/xml";
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
        }

        if($sDataType=="JSON" && $sApiType == "")
        {
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
            $header[] ="Content-Type:application/json";
            $header[] = "Content-length:".strlen(json_encode($aParam));
        }

        if($sDataType=="JSON" && $sApiType != "")
        {
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
        }

        curl_setopt($ch,CURLOPT_URL,$this->sApiUrl);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        //curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        if($sRequestMethod=="PUT")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        }
        elseif($sRequestMethod=="POST")
        {
            curl_setopt($ch,CURLOPT_POST,1);
        }
        elseif($sRequestMethod=="GET")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }
        if($sDataType=="JSON")
        {
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($aParam));
        }
        elseif($sDataType=="XML")
        {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$aParam);
        }
        file_put_contents('/tmp/im_'.date('Y-m-d'), '['.date('H:i:s').'] request param : '.json_encode($aParam).' request url : '.$this->sApiUrl."\n\r", FILE_APPEND);
        $output=curl_exec($ch);
        file_put_contents('/tmp/im_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.$output."\n\r", FILE_APPEND);
        $arr_return['http_info'] = curl_getinfo($ch);
        $arr_return['response']=json_decode($output, true);
        $arr_return['errno'] = curl_errno($ch);
        $arr_return['error'] = curl_error($ch);
        $exec=curl_close($ch);
        return $arr_return;
    }
}