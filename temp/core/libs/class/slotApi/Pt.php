<?php
class Pt implements Api
{
    const CODE_SUCCESS = 0;
    const CODE_BAD_REQUEST = 1;
    const CODE_SYSTEM_UNAVAILABLE = 2;
    const CODE_UNAUTHORIZED_CROSS_MERCHANT = 50;
    const CODE_INVALID_MERCHANT_NAME_OR_CODE = 51;
    const CODE_IP_ADDRESS_IS_BLOCKED = 52;
    const CODE_PLAYER_NOT_EXISTS = 53;
    const CODE_PLAYER_ALREADY_EXISTS = 54;
    const CODE_NOT_SUPPORTED_CURRENCY = 55;
    const CODE_ACCOUNT_BEING_PROCESSED = 56;
    const CODE_LANGUAGE_NOT_SUPPORTED = 62;
    const CODE_REQUIRED_FIELD_EMPTY = 100;
    const CODE_INVALID_MEMBER_CODE = 101;
    const CODE_INVALID_PASSWORD = 102;
    const CODE_INVALID_PRODUCT_TYPE = 103;
    const CODE_INVALID_AMOUNT_FORMAT = 104;
    const CODE_INVALID_EXTERNAL_TRANSACTION_ID = 105;
    const CODE_PLAYER_NOT_ACTIVE = 250;
    const CODE_DEPOSIT_EXCEEDED = 400;
    const CODE_AGENT_NOT_ENOUGH_BALANCE = 401;
    const CODE_OVER_AMOUNT_EXCEEDS_MAX_LIMIT = 402;
    const CODE_OVER_AMOUNT_EXCEEDS_MIN_LIMIT = 403;
    const CODE_BALANCE_NOT_ENOUGH = 404;
    const CODE_DUPLICATED_EXTERNAL_TRANSACTION_ID = 405;
    const CODE_TRANSACTION_STILL_PROGRESS = 406;
    const CODE_TRANSACTION_PROGRESSING = 407;
    const CODE_EXTERNAL_TRANSACTION_ID_NOT_FOUND_IN_SYSTEM = 450;
    const CODE_EXTERNAL_TRANSACTION_ID_NOT_FOUND_IN_PRODUCT = 451;
    const CODE_INVALID_GAME_CODE = 500;
    const CODE_GAME_UNAVAILABLE_IN_PLATFORM = 503;
    const CODE_GAME_UNAVAILABLE_IN_FUN_MODE = 504;

    const PLATFORM_PT = 0;
    const PLATFORM_IMSLOT = 2;

    const LANGUAGE_CN_FOR_PT = 'ZH-CN';
    const LANGUAGE_CN_FOR_IMSLOT = 'ZH';

    public static $aPlatforms = [
        'PT' => self::PLATFORM_PT,
        'IM' => self::PLATFORM_IMSLOT
    ];

    public static $aLanguageCn = [
        self::PLATFORM_PT => self::LANGUAGE_CN_FOR_PT,
        self::PLATFORM_IMSLOT => self::LANGUAGE_CN_FOR_IMSLOT
    ];

    public static $aResponseMsg = [
        self::CODE_SUCCESS => '没有错误。',
        self::CODE_BAD_REQUEST => '非法请求。',
        self::CODE_SYSTEM_UNAVAILABLE => '系统正在处理你的请求，请稍后再试。',
        self::CODE_UNAUTHORIZED_CROSS_MERCHANT => '未经授权的商户号。',
        self::CODE_INVALID_MERCHANT_NAME_OR_CODE => '无效的商户名或商户号。',
        self::CODE_IP_ADDRESS_IS_BLOCKED => 'IP已被屏蔽。',
        self::CODE_PLAYER_NOT_EXISTS => '玩家不存在。',
        self::CODE_PLAYER_ALREADY_EXISTS => '玩家已经存在',
        self::CODE_NOT_SUPPORTED_CURRENCY => '暂不支持该货币。',
        self::CODE_ACCOUNT_BEING_PROCESSED => '正在处理，请稍后再试。',
        self::CODE_LANGUAGE_NOT_SUPPORTED => '暂不支持该语言。',
        self::CODE_REQUIRED_FIELD_EMPTY => '必选参数不能为空。',
        self::CODE_INVALID_MEMBER_CODE => '无效的用户名，用户名长度必须在5-32个字符之间。',
        self::CODE_INVALID_PASSWORD => '无效的密码，密码长度必须在5-40个字符之间。',
        self::CODE_INVALID_PRODUCT_TYPE => '无效的产品类型。',
        self::CODE_INVALID_AMOUNT_FORMAT => '无效的金额格式。',
        self::CODE_INVALID_EXTERNAL_TRANSACTION_ID => '无效的订单号， 长度不能超过100个字符。',
        self::CODE_PLAYER_NOT_ACTIVE => '用户已创建，但无法进入未被激活的游戏。',
        self::CODE_DEPOSIT_EXCEEDED => '正在进行的充值已超出限制。',
        self::CODE_AGENT_NOT_ENOUGH_BALANCE => '代理余额不足。',
        self::CODE_OVER_AMOUNT_EXCEEDS_MAX_LIMIT => '大于最高限额。',
        self::CODE_OVER_AMOUNT_EXCEEDS_MIN_LIMIT => '少于最低限额。',
        self::CODE_BALANCE_NOT_ENOUGH => '余额不足',
        self::CODE_DUPLICATED_EXTERNAL_TRANSACTION_ID => '订单已存在。',
        self::CODE_TRANSACTION_STILL_PROGRESS => '系统正在处理。',
        self::CODE_TRANSACTION_PROGRESSING => '转账正在处理，请稍后进行查询。',
        self::CODE_EXTERNAL_TRANSACTION_ID_NOT_FOUND_IN_SYSTEM => '该订单不存在。',
        self::CODE_EXTERNAL_TRANSACTION_ID_NOT_FOUND_IN_PRODUCT => '该订单不存在。',
        self::CODE_INVALID_GAME_CODE => '无效的游戏代码。',
        self::CODE_GAME_UNAVAILABLE_IN_PLATFORM => '该游戏暂不支持该平台。',
        self::CODE_GAME_UNAVAILABLE_IN_FUN_MODE => '该游戏暂不支持该模式。',
    ];

    private $sHost;
    private $sApiUrl;
    private $iProductType;
    private $sMerchantCode;
    private $sMerchantName;
    private $sLanguage;
    private static $_instance;

    private function __construct(){
        $this->sHost = SysConfig::readValue('pt_host');
        $this->iProductType = self::PLATFORM_PT;
        $this->sLanguage = 'ZH-CN';
        $this->sMerchantCode = SysConfig::readValue('pt_merchant_code');
        $this->sMerchantName = SysConfig::readValue('pt_merchant_name');
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

    /**
     * 创建新的玩家
     * @param $sMemberCode 玩家用户名
     * @param $sPassword 玩家密码
     * @param string $sCurrency 货币
     */
    public function createPlayer($sMemberCode, $sPassword, $sCurrency = "CNY"){
        $sAction = '/player/createplayer';
        $aParam = [
            'membercode' => $sMemberCode,
            'password' => $sPassword,
            'currency' => $sCurrency
        ];

        $aResponses = $this->execApi($sAction, 'POST', $aParam);
        file_put_contents('/tmp/pt_create_player', ' username : '.$sMemberCode.'--->'.json_encode($aResponses).' count:'.strlen($sPassword)."\n\r", FILE_APPEND);
        if(!$aResponses) return false;
        if ($aResponses['Code'] != 0) {
            file_put_contents('/tmp/pt_create_player', 'create fail, username : '.$sMemberCode."\n\r", FILE_APPEND);
            return false;
        }
        return true;
    }

    /**
     * 检查玩家是否存在
     * @param $sMemberCode 玩家用户名
     */
    public function checkPlayerExists($sMemberCode){
        $sAction = '/player/checkplayerexists/membercode/'.$sMemberCode;
        return $this->execApi($sAction, 'GET');
    }

    /**
     * 查询玩家余额
     * @param $sMemberCode 玩家用户名
     */
    public function getBalance($sMemberCode, $sPassword){
        $sAction = '/account/getbalance/membercode/'.$sMemberCode.'/producttype/'.$this->iProductType;
        $aResponse = $this->execApi($sAction, 'GET');
        file_put_contents('/tmp/pt_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sMemberCode.' response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Code'] == 0) return ['status' => 1, 'balance' => $aResponse['Balance']];
        else return ['status' => 0];
    }

    /**
     * 重置密码
     * @param $sMemberCode 玩家用户名
     * @param $sPassword 新密码
     */
    public function resetPassword($sMemberCode, $sPassword, $sOldPassword = ''){
        $sReponse  = $this->execApi('/player/checkplayerexists/membercode/'.$sMemberCode, 'GET');
        if(!$sReponse) return false;
        if($sReponse['Code'] == 53) {
            if (!$this->createPlayer($sMemberCode, $sPassword)) return false;
            else return true;
        }


        $sAction = '/player/resetpassword';

        $aParam = [
            'membercode' => $sMemberCode,
            'password' => $sPassword,
        ];

        $aResponse = $this->execApi($sAction, 'PUT', $aParam);
        if ($aResponse['Code'] == 0) return true;
        return false;
    }

    /** 删除玩家会话
     * @param $sMemberCode
     * @return mixed
     */
    public function killSession($sMemberCode){
        $sAction = '/player/killsession';

        $aParam = [
            'membercode' => $sMemberCode,
            'producttype' => $this->iProductType,
        ];

        return $this->execApi($sAction, 'PUT', $aParam);
    }

    /** 验证玩家身份
     * @param $sMemberCode
     * @param $sPassword
     * @return mixed
     */
    public function authenticatePlayer($sMemberCode, $sPassword){
        $sAction = '/player/authenticateplayer';

        $aParam = [
            'membercode' => $sMemberCode,
            'password' => $sPassword,
        ];

        return $this->execApi($sAction, 'PUT', $aParam);
    }


    public function checkPlayerToken($sMemberCode, $sToken){
        $sAction = '/player/checkplayertoken/membercode/'.$sMemberCode.'/token/'.$sToken.'/producttype/'.$this->iProductType;
        return $this->execApi($sAction, 'GET');
    }

    /**
     * 存取筹码
     * @param $sMemberCode 玩家用户名
     * @param $fAmount 金额 负数提款，正数充值
     * @param $sExternalTransactionId 订单号 $sLoginName, $sPassword, $sBillNo, $iType, $fCredit
     */
    public function transaction($sMemberCode, $sPassword, $sExternalTransactionId, $iType, $fAmount){
        $sAction = '/chip/createtransaction';
        $iSign = $iType == "IN" ? 1 : -1;
        $aParam = [
            'membercode' => $sMemberCode,
            'amount' => $fAmount * $iSign,
            'externaltransactionid' => $sExternalTransactionId,
            'producttype' => $this->iProductType,
        ];
        $aResponse = $this->execApi($sAction, 'POST', $aParam);
        file_put_contents('/tmp/pt_transaction_'.date('Y-m-d'), '['.date('H:i:s').']'.'params : '.json_encode($aParam).' reponse : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['Code'] == 0) return true;
        else return false;
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

    /** 所有投注记录
     * @param $sStartDate
     * @param $sEndDate
     * @param $sCurrency
     * @return mixed
     */
    public function getAllBetLog($sStartDate, $sEndDate, $sCurrency = 'CNY'){
        $sAction = '/report/getbetlog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/producttype/'.$this->iProductType.'/currency/'.$sCurrency;
        return $this->execApi($sAction, 'GET');
    }

    /** 玩家投注记录
     * @param $sStartDate
     * @param $sEndDate
     * @param $sMemberCode
     * @return mixed
     */
    public function getBetLogByMemberCode($sStartDate, $sEndDate, $sMemberCode){
        $sAction = '/report/getbetlog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/membercode/'.$sMemberCode.'/producttype/'.$this->iProductType;
        return $this->execApi($sAction, 'GET');
    }

    /** 玩家某页投注记录
     * @param $sStartDate
     * @param $sEndDate
     * @param $sMemberCode
     * @param $iPage
     * @return mixed
     */
    public function getBetLogWithPageByMemberCode($sStartDate, $sEndDate, $sMemberCode, $iPage){
        $sAction = '/report/getbetlog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/producttype/'.$this->iProductType.'/membercode/'.$sMemberCode.'/page/'.$iPage;
        return $this->execApi($sAction, 'GET');
    }

    /** 某货币某页投注记录
     * @param $sStartDate
     * @param $sEndDate
     * @param $iPage
     * @param $sCurrency
     * @return mixed
     */
    public function getBetLogWithPageByCurrency($sStartDate, $sEndDate, $iPage, $sCurrency){
        $sAction = '/report/getbetlog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/producttype/'.$this->iProductType.'/currency/'.$sCurrency.'/page/'.$iPage;
        return $this->execApi($sAction, 'GET');
    }

    /** 支出PT回扣
     * @param $sMemberCode
     * @param $fAmount
     * @param $sExternaltranid
     * @param $sStartDate
     * @param $sEndDate
     * @param $sGameCode
     * @param $iVipLevel
     * @param string $sCurrencyCode
     * @return mixed
     */
    public function payRebate($sMemberCode, $fAmount, $sExternaltranid, $sStartDate, $sEndDate, $sGameCode, $iVipLevel, $sCurrencyCode = 'CNY'){
        $sAction = '/rebate/payrebate';

        $aParam = [
            'membercode' => $sMemberCode,
            'amount' => $fAmount,
            'externaltranid' => $sExternaltranid,
            'startdate' => $sStartDate,
            'enddate' => $sEndDate,
            'gamecode' => $sGameCode,
            'viplevel' => $iVipLevel,
            'currencycode' => $sCurrencyCode
        ];

        return $this->execApi($sAction, 'POST', $aParam);
    }

    /** 扣除PT回扣
     * @param $sMemberCode
     * @param $fAmount
     * @param $sExternaltranid
     * @param $sStartDate
     * @param $sEndDate
     * @param $sGameCode
     * @param $iVipLevel
     * @param string $sCurrencyCode
     * @return mixed
     */
    public function clearRebate($sMemberCode, $fAmount, $sExternaltranid, $sStartDate, $sEndDate, $sGameCode, $iVipLevel, $sCurrencyCode = 'CNY'){
        $sAction = '/rebate/payrebate';

        $aParam = [
            'membercode' => $sMemberCode,
            'amount' => $fAmount,
            'externaltranid' => $sExternaltranid,
            'startdate' => $sStartDate,
            'enddate' => $sEndDate,
            'gamecode' => $sGameCode,
            'viplevel' => $iVipLevel,
            'currencycode' => $sCurrencyCode
        ];

        return $this->execApi($sAction, 'POST', $aParam);
    }

    /** 批量支出PT回扣
     * @param $sStartDate
     * @param $sEndDate
     * @param $sGameCode
     * @param $iVipLevel
     * @param string $sCurrencyCode
     * @param $aParams
     * @return mixed
     */
    public function massPayRebate($sStartDate, $sEndDate, $sGameCode, $iVipLevel, $sCurrencyCode = 'CNY', $aParams){
        $sAction = '/rebate/payrebate';

        $aParam = [
            'startdate' => $sStartDate,
            'enddate' => $sEndDate,
            'gamecode' => $sGameCode,
            'viplevel' => $iVipLevel,
            'currencycode' => $sCurrencyCode,
            'param' => $aParams //
            //$aParams = [
            //  ['membercode' => membercode, 'amount' => amount, 'externaltranid' => externaltranid],
            //  ['membercode' => membercode, 'amount' => amount, 'externaltranid' => externaltranid],
            //  ...
            //]
        ];

        return $this->execApi($sAction, 'POST', $aParam);
    }

    /** 玩家回扣日志
     * @param $sStartDate
     * @param $sEndDate
     * @param $sMemberCode
     * @param $iPage
     * @return mixed
     */
    public function getRebateLog($sStartDate, $sEndDate, $sMemberCode, $iPage){
        $sAction = '/report/getrebatelog/startdate/'.$sStartDate.'/enddate/'.$sEndDate.'/membercode/'.$sMemberCode.'/page/'.$iPage.'/producttype/'.$this->iProductType;
        return $this->execApi($sAction, 'GET');
    }

    /** 游戏票
     * @param $sMemberCode
     * @param $sIpAddress
     * @return mixed
     */
    public function getGameTicket($sMemberCode){
        $sAction = '/game/getgameticket/membercode/'.$sMemberCode.'/ipaddress/'.get_client_ip().'/producttype/'.$this->iProductType;
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
        if($this->iProductType == self::PLATFORM_PT){
            $aResponses['GameUrl'] = 'http://cache.download.banner.mightypanda88.com/casinoclient.html?game='.$sGameCode.'&language='.$this->sLanguage;
//            file_put_contents('/tmp/keryx', "\n\rdate : \n\r\t".date('Y-m-d H:i:s')."\n\r GameUrl : \n\r\t".$aResponses['GameUrl']."\n\r", FILE_APPEND);
        }
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
        header("location: ".$aResponse['GameUrl']);
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
        file_put_contents('/tmp/pt_'.date('Y-m-d'), '['.date('H:i:s').'] request param : '.json_encode($aParam).' request url : '.$this->sApiUrl."\n\r", FILE_APPEND);
        $output=curl_exec($ch);
        file_put_contents('/tmp/pt_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.$output."\n\r", FILE_APPEND);
        $arr_return['http_info'] = curl_getinfo($ch);
        $arr_return['response']=json_decode($output, true);
        $arr_return['errno'] = curl_errno($ch);
        $arr_return['error'] = curl_error($ch);
        $exec=curl_close($ch);
        return $arr_return;
    }

    function launchGame($sUsername, $sPassword, $sGameCode)
    {
        $aResponse = $this->launchGame1($sUsername, $sGameCode);
        if($this->iProductType == self::PLATFORM_PT){
            $aResponse['game_code'] = $sGameCode;
            return $aResponse;
        }
        if ($aResponse['Code'] != 0 && empty($aResponse) && !isset($aResponse['gameurl'])) return 'fail';
        header("location: ".$aResponse['gameurl']);
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
        if($this->iProductType == self::PLATFORM_PT){
            $aResponses['GameUrl'] = 'http://cache.download.banner.mightypanda88.com/casinoclient.html?mode=offline&affiliates=1&game='.$sGameCode.'&language='.$this->sLanguage.'&currency=CNY';
            $aResponses['Code'] = 0;
        }

        return $aResponses;
    }

    function launchFreeGame($sUsername, $sPassword, $sGameCode)
    {
        header("location: http://cache.download.banner.mightypanda88.com/casinoclient.html?mode=offline&affiliates=1&game=$sGameCode&language=$this->sLanguage&currency=CNY");
    }
}