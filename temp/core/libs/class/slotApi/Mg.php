<?php
/**
 *
 * MG游戏接入DEMO
 * 为了方便日后问题处理，每个方法请务必添加日志功能
 */
class Mg implements Api
{
    private $host;
    private $token = '';
    private static $_instance;
    private $params;

    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
//        date_default_timezone_set("UTC"); //此项不能省略
        $this->host = SysConfig::readValue('mg_host');
        $this->params = [
            'crId' => SysConfig::readValue('mg_cr_id'), //编号
            'crType' => 'ma', //参照文档设置
            'neId' => SysConfig::readValue('mg_cr_id'), //编号
            'neType' => 'ma', //参照文档设置
            'tarType' => 'm',//参照文档设置
            'currency' => 'CNY',//币种
            'language' => 'zh',//语言
            'casino' => array('enable'=>true),//电子游戏
            'poker' => array('enable'=>false),//扑克
            'j_username' => SysConfig::readValue('mg_j_username'), //后台用户名
            'j_password' => SysConfig::readValue('mg_j_password'),//如果网上更改了帐号密码此项目必须要更改
            'apiusername' => 'apiadmin',//API用户名（固定值）
            'apipassword' => 'apipassword',//API密码（固定值）
            'partnerId' => '88KIOSK',//固定值
        ];
    }

    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }

    function launchGame($sUsername, $sPassword, $sGameCode)
    {
        if (!$sUsername || !$sPassword || !$sGameCode) return 'fail';
        file_put_contents('/tmp/mg_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword."\n\r", FILE_APPEND);
        $aParams = [
            'username' => $sUsername,
            'password' => $sPassword,
            'gameId' => $sGameCode,
            'bankingUrl' => '',
            'lobbyUrl' => '',
            'logoutRedirectUrl' => '',
            'demoMode' => false,
        ];
        $aResponse = $this->loginUrl($aParams);

        if (isset($aResponse['LAUNCHURL'])) {
            file_put_contents('/tmp/mg_launch_game_'.date('Y-m-d'), '['.date('H:i:s').'] '.$aResponse['LAUNCHURL']."\n\r", FILE_APPEND);
            return $aResponse['LAUNCHURL'];
        }

        return 'fail';
    }

    function launchFreeGame($sUsername, $sPassword, $sGameCode)
    {
        if (!$sUsername || !$sPassword || !$sGameCode) return 'fail';
        file_put_contents('/tmp/mg_launch_free_game_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sUsername.' password : '.$sPassword."\n\r", FILE_APPEND);
        $aParams = [
            'username' => $sUsername,
            'password' => $sPassword,
            'gameId' => $sGameCode,
            'bankingUrl' => '',
            'lobbyUrl' => '',
            'logoutRedirectUrl' => '',
            'demoMode' => true,
        ];
        $aResponse = $this->loginUrl($aParams);
        if ($aResponse['STATUS'] != 0) return 'fail';
        file_put_contents('/tmp/mg_launch_free_game_'.date('Y-m-d'), '['.date('H:i:s').'] '.$aResponse['LAUNCHURL']."\n\r", FILE_APPEND);
        header("location: ".$aResponse['LAUNCHURL']);
    }

    public function createPlayer($sUsername, $sPassword)
    {
        $aParams = [
            'username' => $sUsername,
            'password' => $sPassword,
            'ipaddress' => get_client_ip(),
        ];
        $aResponses = $this->memberDetails($aParams);
        if ($aResponses['code'] != 1) {
            $aParams = [
                'username' => $sUsername,
                'password' => $sPassword,
            ];
            $aResponses = $this->createPlayer1($aParams);
            if ($aResponses['code'] != 1) return false;
        }
        return true;
    }

    protected function securityCheck()
    {
        $this->api_url = $this->host.'lps/j_spring_security_check';

        $this->token = '';
        $params = array('j_username'=>$this->params['j_username'],'j_password'=>$this->params['j_password']);

        $ret = $this->sendRequest($params,"spring_security_check");
        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);

            if(!empty($arr['token']) && !empty($arr['id']))
            {
                $this->token = $arr['token'];
                return array("code"=>1,"token"=>$arr['token'],"id"=>$arr['id']);
            }
            else
            {
                return array("code"=>0,"message"=>"Api Authentication Failed");
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }
            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    protected function memberApi($param)
    {
        //优化方案--建议采用REDIS（demo中不实现） 原理：添加判断验证TOKEN是否在有效期--6小时（适用于所有要调用此方法的接口,每次接口调用完成务必要根据返回的时间与TOKEN进行更新）
        $this->api_url = $this->host.'member-api-web/member-api';
        $timestamp = date("Y-m-d H:i:s.z T",strtotime('-8 hour'));

        $xml='<mbrapi-login-call timestamp="%s" apiusername="%s" apipassword="%s" username="%s" password="%s" ipaddress="%s" partnerId="%s" currencyCode="%s"/>';
        $confirmxml = sprintf($xml, $timestamp, $this->params['apiusername'], $this->params['apipassword'], $param['username'], $param['password'], get_client_ip(), $this->params['partnerId'], $this->params['currency']);

        $ret=$this->sendRequest($confirmxml,'',"PUT","XML");

        if($ret['http_info']['http_code'] == 200)
        {
            $xml=$ret['response'];
            $arr=$this->xmlToArray($xml);
            if($arr['STATUS'] == 0)
            {
                return array('code'=>1,'token'=>$arr['TOKEN'],'timestamp'=>$arr['TIMESTAMP']);
            }
            elseif($arr['STATUS'] == 2001)
            {
                return array('code'=>2001,'message'=>'Credential not valid');
            }
            elseif($arr['STATUS'] == 2003)
            {
                return array('code'=>2003,'message'=>'Account not exist');
            }
            else
            {
                return array('code'=>0);
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function createPlayer1($param)
    {
        $param = array(
            'crId' => $this->params['crId'],
            'crType' => $this->params['crType'],
            'neId' => $this->params['neId'],
            'neType' => $this->params['neType'],
            'tarType' => $this->params['tarType'],
            'currency' => $this->params['currency'],
            'language' => $this->params['language'],
            'casino' => $this->params['casino'],
            'poker' => $this->params['poker'],
            'j_username' => $this->params['j_username'],
            'j_password' => $this->params['j_password'],
            'username' => $param['username'],
            'name' => $param['username'],
            'password' => $param['password'],
            'confirmPassword' => $param['password'],
            'email' => $param['username']."@qq.com",
            'mobile' => '1234567890',
        );

        $api_security = $this->securityCheck();

        $this->api_url = $this->host.'lps/secure/network/'.$this->params['neId']."/downline";

        if($api_security['code'] != 1)
        {
            return $api_security;
        }

        $ret=$this->sendRequest($param,'',"PUT","JSON");

        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);

            if($arr['success'] == 'true')
            {
                return array("code"=>1,"success"=>'true',"message"=>$arr['message'],"id"=>$arr['id']);//此返回ID可用于查询余额，请注意保存
            }
            else
            {
                return array("code"=>0,"success"=>'flase',"message"=>$arr['message']);
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }

    }

    public function transfer($param)
    {
        $ret=$this->memberApi($param);

        if($ret['code'] != 1)
        {
            return $ret;
        }

        $this->api_url = $this->host.'member-api-web/member-api';
        $timestamp = date("Y-m-d H:i:s.z T",strtotime('-8 hour'));
        $product = !empty($param['product'])?$param['product']:'casino';
        $operation = !empty($param['operation'])?$param['operation']:'topup';

        $xml='<mbrapi-changecredit-call timestamp="%s" apiusername="%s" apipassword="%s" token="%s" product="%s" operation="%s" amount="%s" tx-id="%s"/>';
        $xmlcon = sprintf($xml,$timestamp, $this->params['apiusername'], $this->params['apipassword'], $ret['token'],$product,$operation,$param['amount'],$param['tx-id']);

        $this->token = '';

        $ret=$this->sendRequest($xmlcon,'',"POST","XML");
        file_put_contents('/tmp/mg_transaction_'.date('Y-m-d'), '['.date('H:i:s').']'.' reponse : '.json_encode($ret)."\n\r", FILE_APPEND);
        if($ret['http_info']['http_code'] == 200)
        {
            $xml=$ret['response'];
            $arr=$this->xmlToArray($xml);
            if($arr['STATUS'] == 0)
            {
                return array('code'=>1,'timestamp'=>$arr['TIMESTAMP'],'token'=>$arr['TOKEN']);
            }
            elseif($arr['STATUS'] == 1004)
            {
                return array('code'=>$arr['STATUS'],'timestamp'=>$arr['TIMESTAMP'],'message'=>'Invalid input detected');
            }
            elseif($arr['STATUS'] == 1001)
            {
                return array('code'=>1001,'message'=>'Api Authentication Failed');
            }
            else
            {
                return array('code'=>$arr['STATUS'],'timestamp'=>$arr['TIMESTAMP'],'token'=>$arr['TOKEN']);
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }

    }

    public function getBalance1($param)
    {
        $arr=$this->securityCheck($param);
        $this->api_url = $this->host.'lps/secure/settings/'.$param['userid'];

        if($arr['code']!=1)
        {
            return $arr;
        }

        $ret=$this->sendRequest('','',"GET");

        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);
            if(!empty($arr['type']))
            {
                return array("code"=>1,"balance"=>$arr['casino']['creditBalance']);
            }
            else
            {
                return array('code'=>0,'message'=>'userid not exists','balance'=>'0.00');
            }
        }
        elseif($ret['http_info']['http_code'] == 401)
        {
            return array('code'=>0,"message"=>'Unauthorized',"http_code"=>$ret['http_info']['http_code']);
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function viewProfile($param)
    {
        $arr=$this->securityCheck($param);
        $this->api_url = $this->host.'lps/secure/network/'.$param['userid'].'/profile';

        if($arr['code']!=1)
        {
            return $arr;
        }

        $ret=$this->sendRequest('','',"GET");

        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);
            return $arr;
        }
        elseif($ret['http_info']['http_code'] == 401)
        {
            return array('code'=>0,"message"=>'Unauthorized',"http_code"=>$ret['http_info']['http_code']);
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function memberDetails($param)
    {
        $ret=$this->memberApi($param);

        if($ret['code'] != 1)
        {
            return $ret;
        }

        $this->api_url = $this->host.'member-api-web/member-api';
        $timestamp = date("Y-m-d H:i:s.z T",strtotime('-8 hour'));

        $xml='<mbrapi-account-call timestamp="%s" apiusername="%s" apipassword="%s" token="%s" />';
        $paramxml = sprintf($xml,$timestamp,$this->params['apiusername'], $this->params['apipassword'],$ret['token']);

        $ret=$this->sendRequest($paramxml,'',"POST","XML");
        file_put_contents('/tmp/mg_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.json_encode($ret)."\n\r", FILE_APPEND);
        if($ret['http_info']['http_code'] == 200)
        {
            $xml=$ret['response'];
            $p=xml_parser_create();
            xml_parse_into_struct($p, $xml, $vals);
            xml_parser_free($p);
            if($vals[0]['attributes']['STATUS'] == 0) //此处可以根据自身需要选择返回
            {
                $arr1=$vals[0]['attributes'];
                $arr2=$vals[2]['attributes'];
                return array('code'=>1,'TIMESTAMP'=>$arr1['TIMESTAMP'],'TOKEN'=>$arr1['TOKEN'],'NETWORK-ENTITY-KEY'=>$arr1['NETWORK-ENTITY-KEY'],'CURRENCY'=>$arr1['CURRENCY'],'TIMEZONE'=>$arr1['TIMEZONE'],'CODE'=>$arr1['CODE'],'POKER-ALIAS'=>$arr1['POKER-ALIAS'],'USERNAME'=>$arr1['USERNAME'],'NAME'=>$arr1['NAME'],'LANGUAGE'=>$arr1['LANGUAGE'],'PRODUCT'=>$arr2['PRODUCT'],'CREDIT-BALANCE'=>$arr2['CREDIT-BALANCE'],'CASH-BALANCE'=>$arr2['CASH-BALANCE']);
            }
            else
            {
                return array('code'=>$vals[0]['attributes']['status']);
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function changePassword($param)
    {
        $ret=$this->memberApi($param);

        if($ret['code'] != 1)
        {
            return $ret;
        }

        $this->api_url = $this->host.'member-api-web/member-api';
        $timestamp = date("Y-m-d H:i:s.z T",strtotime('-8 hour'));

        $xml='<mbrapi-changepassword-call timestamp="%s" apiusername="%s" apipassword="%s" token="%s"><oldpassword>%s</oldpassword><newpassword>%s</newpassword></mbrapi-changepassword-call>';

        $param = sprintf($xml,$timestamp,$this->params['apiusername'], $this->params['apipassword'],$ret['token'],$param['oldpassword'],$param['newpassword']);

        $ret=$this->sendRequest($param,"","POST","XML");

        if($ret['http_info']['http_code'] == 200)
        {
            $xml=$ret['response'];
            $arr=$this->xmlToArray($xml);
            if($arr['STATUS'] == 0)
            {
                return array('code'=>1);
            }
            else
            {
                return array('code'=>$arr['STATUS']);
            }
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }

    }

    private function loginUrl($param)
    {
        $ret=$this->memberApi($param);

        if($ret['code'] != 1)
        {
            return $ret;
        }

        $this->api_url = $this->host.'member-api-web/member-api';
        $timestamp = date("Y-m-d H:i:s.z T",strtotime('-8 hour'));

        $xml='<mbrapi-launchurl-call timestamp="%s" apiusername="%s" apipassword="%s" token="%s" language="%s" gameId="%d" bankingUrl="%s" lobbyUrl="%s" logoutRedirectUrl="%s" demoMode="%s"/>';

        $param = sprintf($xml,$timestamp,$this->params['apiusername'], $this->params['apipassword'],$ret['token'], $this->params['language'],$param['gameId'],$param['bankingUrl'],$param['lobbyUrl'],$param['logoutRedirectUrl'],$param['demoMode']);

        $ret=$this->sendRequest($param,"","POST","XML");

        if($ret['http_info']['http_code'] == 200)
        {
            $xml=$ret['response'];
            $arr=$this->xmlToArray($xml);
            return $arr;
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function transactionHistory($param)
    {
        $arr=$this->securityCheck($param);
        $this->api_url = $this->host.'lps/secure/hortx/'.$param['horid'].'?start='.$param['startTime'].'&end='.$param['endTime'].'&timezone='.$param['timezone'];

        if($arr['code']!=1)
        {
            return $arr;
        }

        $ret=$this->sendRequest('','',"GET");

        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);
            return $arr;
        }
        elseif($ret['http_info']['http_code'] == 401)
        {
            return array('code'=>0,"message"=>'Unauthorized',"http_code"=>$ret['http_info']['http_code']);
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    public function gameHistoty($param)
    {
        $ret=$this->memberApi($param);

        if($ret['code'] != 1)
        {
            return $ret;
        }

        $this->api_url = $this->host.'member-api-web/member-api';
        if($param['type'] == 'Playcheck') //游戏结果核对
        {
            $url ="https://playcheck22.gameassists.co.uk/playcheck/default.asp?serverid=".$param['serverid']."&ul=".$this->params['language']."&usertoken=".$ret['token'];
        }
        if($param['type'] == 'Specific')
        {
            $url = "https://playcheck22.gameassists.co.uk/playcheck/default.asp?applicationID=1001&appmode=OperatorPlayCheckView&serverID=".$param['serverid']."&ul=".$this->params['language']."&usertoken=".$ret['token']."&transactionid=".$param['mgsGameId'];
        }
        header("Location: $url");
    }

    public function getBetLog($param){
        $arr=$this->securityCheck($param);
        $this->api_url = $this->host.'lps/secure/agenttx/'.$this->params['crId'].'?start='.$param['start'].'&end='.$param['end'];

        if($arr['code']!=1)
        {
            return $arr;
        }

        $ret=$this->sendRequest('','',"GET");

        if($ret['http_info']['http_code'] == 200)
        {
            $arr=json_decode($ret['response'],true);
            return $arr;
        }
        elseif($ret['http_info']['http_code'] == 401)
        {
            return array('code'=>0,"message"=>'Unauthorized',"http_code"=>$ret['http_info']['http_code']);
        }
        else
        {
            if(!empty($ret['errno']))
            {
                return array('code'=>$ret['errno'],'message'=>$ret['error']);
            }

            return array('code'=>0,"message"=>'http_code:'.$ret['http_info']['http_code']);
        }
    }

    private function sendRequest($param,$api_type="",$request_method="POST",$datatype="JSON")
    {
        $ch=curl_init();
        $header = array();
        if($datatype == "XML")
        {
            $header[] = "Content-Type:text/xml";
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
        }

        if($datatype=="JSON" && $api_type == "")
        {
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
            $header[] ="Content-Type:application/json";
            $header[] = "Content-length:".strlen(json_encode($param));
        }

        if($datatype=="JSON" && $api_type != "")
        {
            $header[] ="X-Requested-With:X-Api-Client";
            $header[] ="X-Api-Call:X-Api-Client";
        }

        if(!empty($this->token))
        {
            $header[] ="X-Api-Auth:".$this->token;
        }

        curl_setopt($ch,CURLOPT_URL,$this->api_url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        //curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        if($request_method=="PUT")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        }
        elseif($request_method=="POST")
        {
            curl_setopt($ch,CURLOPT_POST,1);
        }
        elseif($request_method=="GET")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }
        if($api_type=="spring_security_check" && $datatype=="JSON" )
        {
            curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($param));
        }
        elseif($datatype=="JSON")
        {
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($param));
        }
        elseif($datatype=="XML")
        {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
        }
        file_put_contents('/tmp/mg_'.date('Y-m-d'), '['.date('H:i:s').'] request param : '.json_encode($param).' request url : '.$this->api_url."\n\r", FILE_APPEND);
        $output=curl_exec($ch);
        $arr_return['http_info'] = curl_getinfo($ch);
        $arr_return['response']=$output;
        $arr_return['errno'] = curl_errno($ch);
        $arr_return['error'] = curl_error($ch);
        $exec=curl_close($ch);
        file_put_contents('/tmp/mg_'.date('Y-m-d'), '['.date('H:i:s').'] response : '.json_encode($arr_return)."\n\r", FILE_APPEND);
        return $arr_return;

    }

    private function xmlToArray($param)
    {
        $p=xml_parser_create();
        xml_parse_into_struct($p, $param, $vals, $index);
        xml_parser_free($p);
        return $vals[0]['attributes'];
    }

    function transaction($sLoginName, $sPassword, $sBillNo, $iType, $fCredit)
    {
        $aParams = [
            'username' => $sLoginName,
            'password' => $sPassword,
            'product' => 'casino',
            'amount' => $fCredit,
            'tx-id' => $sBillNo
        ];
        if ($iType == "IN") $aParams['operation'] = 'topup';
        else $aParams['operation'] = 'withdraw';
        $aReponse = $this->transfer($aParams);
        file_put_contents('/tmp/mg_transaction_'.date('Y-m-d'), '['.date('H:i:s').']'.' loginName : '.$sLoginName.', billNo : '.$sBillNo.', type : '.$iType.', credit : '.$fCredit."\n\r", FILE_APPEND);
        if ($aReponse['code'] == 1) return true;
        else return false;
    }

    function getBalance($sLoginName, $sPassword)
    {
        $aResponse = $this->memberDetails(['username' => $sLoginName, 'password' => $sPassword, 'ipaddress' => get_client_ip()]);
        file_put_contents('/tmp/mg_get_balance_'.date('Y-m-d'), '['.date('H:i:s').'] username : '.$sLoginName.' response : '.json_encode($aResponse)."\n\r", FILE_APPEND);
        if ($aResponse['code']  == 1) return ['status' => 1, 'balance' => $aResponse['CREDIT-BALANCE']];
        else return ['status' => 0];
    }

    function resetPassword($sUsername, $sNewPassword, $sOldPassword = '')
    {
        $aResponse = $this->changePassword([
            'username' => $sUsername,
            'password' => $sOldPassword,
            'ipaddress' => get_client_ip(),
            'oldpassword' => $sOldPassword,
            'newpassword' => $sNewPassword,
        ]);
        if ($aResponse['code']  == 1) return true;
        return false;
    }
}
