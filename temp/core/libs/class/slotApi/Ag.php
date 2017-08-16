<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-10-20
 * Time: 下午1:11
 */
class Ag implements Api
{
    const AC_TYPE_TRY = 0;
    const AC_TYPE_REAL = 1;

    private $sApiGiUrl;
    private $sApiGciUrl;
    private $sCAgent;
    private $sMd5Key;
    private $sDesKey;
    private $iAcType;
    private static $_instance;
    private function __construct(){
        $this->sApiGiUrl = SysConfig::readValue('ag_api_gi_url');
        $this->sApiGciUrl = SysConfig::readValue('ag_api_gci_url');
        $this->sCAgent = SysConfig::readValue('ag_c_agent');
        $this->sMd5Key = SysConfig::readValue('ag_md5_key');
        $this->sDesKey = SysConfig::readValue('ag_des_key');
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

    public function launchGame($sUsername, $sPassword, $sGameCode = '') {
        file_put_contents('/tmp/ag_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword."\n\r", FILE_APPEND);
        if (!$sUsername || !$sPassword) return 'fail';
//        echo "<form method='post' action='".$this->forwardGame($sUsername, $sPassword)."' id='form'></form><script language='JavaScript' type='text/JavaScript'>form.submit();</script>";
        if('Fishing' == $sGameCode){
            $sGameCode = 6;
        }
        file_put_contents('/tmp/ag_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] '.$this->forwardGame($sUsername, $sPassword, 1, $sGameCode)."\n\r", FILE_APPEND);
        return $this->forwardGame($sUsername, $sPassword, 1, $sGameCode);
    }

    public function launchFreeGame($sUsername, $sPassword, $sGameCode) {
        file_put_contents('/tmp/ag_launch_free_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword."\n\r", FILE_APPEND);
        if (!$sUsername || !$sPassword) return 'fail';
        $sUsername = $sUsername.'_try';
        if('Fishing' == $sGameCode){
            $sGameCode = 6;
        }
        file_put_contents('/tmp/ag_launch_free_game_'.date('Y-m-d'), '['.date('H:i:s').'] '.$this->forwardGame($sUsername, $sPassword, 0, $sGameCode)."\n\r", FILE_APPEND);
//        echo "<form method='post' action='".$this->forwardGame($sUsername, $sPassword, 0)."' id='form'></form><script language='JavaScript' type='text/JavaScript'>form.submit();</script>";
        header("location: ".$this->forwardGame($sUsername, $sPassword, 0, $sGameCode));
    }

    public function createPlayer($sUsername, $sPassword)
    {
        $aResponses = $this->checkOrCreateGameAccount($sUsername.'_try', $sPassword, 0);
        file_put_contents('/tmp/ag_create_player_try'.date('Y-m-d'), '['.date('H:i:s').']'.', loginName : '.$sUsername.'_try'.', password : '.$sPassword.' reponse : '.json_encode($aResponses)."\n\r", FILE_APPEND);
        $aResponses = $this->checkOrCreateGameAccount($sUsername, $sPassword, 1);
        file_put_contents('/tmp/ag_create_player'.date('Y-m-d'), '['.date('H:i:s').']'.', loginName : '.$sUsername.', password : '.$sPassword.' reponse : '.json_encode($aResponses)."\n\r", FILE_APPEND);
        if ($aResponses['info'] != 0) {
            return false;
        }
        return true;
    }

    public function transaction($sLoginName, $sPassword, $sBillNo, $iType, $fCredit, $sCur = 'CNY'){
        $aResponses = $this->prepareTransferCredit($sLoginName, $sPassword, $sBillNo, $iType, $fCredit, $sCur);
        if($aResponses['@attributes']['info'] == 0){
            $aResponses = $this->transferCreditConfirm($sLoginName, $sPassword, $sBillNo, $iType, $fCredit, 1, $sCur);
            if ($aResponses['@attributes']['info'] == 0) {
                file_put_contents('/tmp/ag_transaction_success_'.date('Y-m-d'), '['.date('H:i:s').']'.' msg : '.$aResponses['@attributes']['msg'].', loginName : '.$sLoginName.', billNo : '.$sBillNo.', type : '.$iType.', credit : '.$fCredit."\n\r", FILE_APPEND);
                return true;
            }else{
                file_put_contents('/tmp/ag_transaction_error_'.date('Y-m-d'), '['.date('H:i:s').']'.' msg : '.$aResponses['@attributes']['msg'].', loginName : '.$sLoginName.', billNo : '.$sBillNo.', type : '.$iType.', credit : '.$fCredit."\n\r", FILE_APPEND);
                return false;
            }
        }
    }

    public function checkOrCreateGameAccount($sLoginName, $sPassword, $iAcType = 1, $sOddType = 'A', $sCur = 'CNY'){
        $aParams = [
            'cagent' => $this->sCAgent,
            'loginname' => $sLoginName,
            'method' => 'lg',
            'actype' => $iAcType,
            'password' => $sPassword,
            'oddtype' => $sOddType,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGiUrl.'/doBusiness.do';
        $aResponses = $this->sendRequest($sUrl, $aParams);
        if($aResponses['http_info']['http_code'] == 200) {
            $aResponses = $this->xmlToArray($aResponses['response']);
            return $aResponses['@attributes'];
        } else {
            return false;
        }

    }

    public function getBalance($sLoginName, $sPassword, $sCur = 'CNY'){
        $aParams = [
            'cagent' => $this->sCAgent,
            'loginname' => $sLoginName,
            'method' => 'gb',
            'actype' => self::AC_TYPE_REAL,
            'password' => $sPassword,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGiUrl.'/doBusiness.do';
        $aResponses = $this->sendRequest($sUrl, $aParams);
        file_put_contents('/tmp/ag_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sLoginName.' request url : '.$sUrl.' params : '.json_encode($aParams).' response : '.json_encode($aResponses)."\n\r", FILE_APPEND);
        if($aResponses['http_info']['http_code'] == 200) {
            $aResponses = $this->xmlToArray($aResponses['response']);
            if($aResponses['@attributes']['msg']) return ['status' => 0];
            else {
                if (!$aResponses['@attributes']['info']) $aResponses['@attributes']['info'] = 0;
                return ['status' => 1, 'balance' => $aResponses['@attributes']['info']];
            }
        } else {
            return ['status' => 0];
        }
    }

    private function prepareTransferCredit($sLoginName, $sPassword, $sBillNo, $iType, $fCredit, $sCur = 'CNY'){
        $aParams = [
            'cagent' => $this->sCAgent,
            'method' => 'tc',
            'loginname' => $sLoginName,
            'billno' => $sBillNo,
            'type' => $iType,
            'credit' => $fCredit,
            'actype' => self::AC_TYPE_REAL,
            'password' => $sPassword,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGiUrl.'/doBusiness.do';
        $aResponses = $this->sendRequest($sUrl, $aParams);
        if($aResponses['http_info']['http_code'] == 200) {
            return $aResponses = $this->xmlToArray($aResponses['response']);
        } else {
            return false;
        }
    }

    private function transferCreditConfirm($sLoginName, $sPassword, $sBillNo, $iType, $fCredit, $iFlag, $sCur = 'CNY'){
        $aParams = [
            'cagent' => $this->sCAgent,
            'loginname' => $sLoginName,
            'method' => 'tcc',
            'billno' => $sBillNo,
            'type' => $iType,
            'credit' => $fCredit,
            'actype' => self::AC_TYPE_REAL,
            'flag' => $iFlag,
            'password' => $sPassword,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGiUrl.'/doBusiness.do';
        $aResponses = $this->sendRequest($sUrl, $aParams);
        if($aResponses['http_info']['http_code'] == 200) {
            return $aResponses = $this->xmlToArray($aResponses['response']);
        } else {
            return false;
        }
    }

    private function queryOrderStatus($sBillNo, $sCur = 'CNY'){
        $aParams = [
            'cagent' => $this->sCAgent,
            'billno' => $sBillNo,
            'method' => 'qos',
            'actype' => self::AC_TYPE_REAL,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGiUrl.'/doBusiness.do';
        $aResponses = $this->sendRequest($sUrl, $aParams);
        if($aResponses['http_info']['http_code'] == 200) {
            return $aResponses = $this->xmlToArray($aResponses['response']);
        } else {
            return false;
        }
    }

    private function forwardGame($sLoginName, $sPassword, $iAcType = 1, $iGameType = 6, $sDm = 'www.qupai88.com', $iLang = 1, $sOddType = 'A', $sCur = 'CNY'){
        $aYearCode = array('A','B','C','D','E','F','G','H','I','J');
        $sSid = $aYearCode[intval(date('Y'))-2010].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        $aParams = [
            'cagent' => $this->sCAgent,
            'loginname' => $sLoginName,
            'actype' => $iAcType,
            'password' => $sPassword,
            'dm' => $sDm,
            'sid' => $this->sCAgent.$sSid,
            'lang' => $iLang,
            'gameType' => $iGameType,
            'oddtype' => $sOddType,
            'cur' => $sCur
        ];
        $sUrl = $this->sApiGciUrl.'/forwardGame.do';
        $sParams = http_build_query($aParams, null, '/\\\\\\\\/');
        $sParams = $this->encrypt($sParams);
        $sKey = md5($sParams.$this->sMd5Key);
        $sUrl = $sUrl.'?params='.$sParams.'&key='.$sKey;
        return $sUrl;
    }

    private function sendRequest($sUrl, $aParams){
        $sParams = http_build_query($aParams, null, '/\\\\\\\\/');
        $sParams = $this->encrypt($sParams);
        $sKey = md5($sParams.$this->sMd5Key);
        $sUrl = $sUrl.'?params='.$sParams.'&key='.$sKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WEB_LIB_GI_'.$this->sCAgent);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        file_put_contents('/tmp/ag_'.date('Y-m-d'), '['.date('H:i:s').'] request param : '.json_encode($aParams).' request url : '.$sUrl."\n\r", FILE_APPEND);
        $html = curl_exec($ch);
        $arr_return['http_info'] = curl_getinfo($ch);
        $arr_return['response']=$html;
        $arr_return['errno'] = curl_errno($ch);
        $arr_return['error'] = curl_error($ch);
        curl_close($ch);
        file_put_contents('/tmp/ag_'.date('Y-m-d'), '['.date('H:i:s').'] '.$html."\n\r".json_encode($arr_return)."\n\r", FILE_APPEND);
        return $arr_return;
    }

    private function encrypt($input) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = $this->sDesKey;
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return preg_replace("/\s*/", '',$data);
    }

    private function decrypt($encrypted) {
        $encrypted = base64_decode($encrypted);
        $key =$this->sDesKey;
        $td = mcrypt_module_open('des','','ecb','');//使用 MCRYPT_DES 算法,cbc 模式
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理
        $decrypted = mdecrypt_generic($td, $encrypted);
        //解密
        mcrypt_generic_deinit($td);
        //结束
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }

    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    private function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }
    private function xmlToArray($sXml){
        return json_decode(json_encode(simplexml_load_string($sXml)),TRUE);
    }

    function resetPassword($sUsername, $sNewPassword, $sOldPassword = '')
    {
        return true;
    }
}
