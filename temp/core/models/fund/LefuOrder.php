<?php
class LefuOrder extends BaseModel
{

    protected $table = 'lefu_orders';

    protected $fillable = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'lefu_order_num',
        'detail',
        'request_time',
        'response_time',
        'lefu_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $columnForList = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'lefu_order_num',
        'detail',
        'request_time',
        'response_time',
        'lefu_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $rules = [

    ];

    /**
     * 充值请求
     * @var int
     */
    const DEPOSIT_REQUEST = 1;

    /**
     * 充值响应
     * @var int
     */
    const DEPOSIT_RESPONSE = 2;

    /**
     * 充值确认
     * @var int
     */
    const DEPOSIT_APPROVE = 3;

    /**
     * 异常推送
     * @var int
     */
    const EXCEPTION_PUSH = 4;

    /**
     * 提现请求
     * @var int
     */
    const WITHDRAW_REQUEST = 5;

    /**
     * 提现状态确认
     * @var int
     */
    const WITHDRAW_APPROVE = 6;

    /**
     * 提现结果确认
     * @var int
     */
    const WITHDRAW_RESULT_APPROVE = 7;

    //提现状态
    const WITHDRAWAL_ORDER_STATUS_NEW		= 0;
    const WITHDRAWAL_ORDER_STATUS_SUBMITED  = 1;
    const WITHDRAWAL_ORDER_STATUS_BACKED	= 2;
    const WITHDRAWAL_ORDER_STATUS_SUCCESS	= 3;
    const WITHDRAWAL_ORDER_STATUS_PART		= 4;
    const WITHDRAWAL_ORDER_STATUS_FAIL		= 5;

    const WITHDRAWAL_ORDER_RETURN_ERROR     = 10;
    const WITHDRAWAL_ORDER_PROCESSING       = 9;


    public $ValidStatuses=array(
        self::WITHDRAWAL_ORDER_STATUS_NEW 	   => '待提交',
        self::WITHDRAWAL_ORDER_STATUS_SUBMITED   => '已提交',
        self::WITHDRAWAL_ORDER_STATUS_BACKED	   => '已返回',
        self::WITHDRAWAL_ORDER_STATUS_SUCCESS    => '成功',
        self::WITHDRAWAL_ORDER_STATUS_PART	   => '部分处理',
        self::WITHDRAWAL_ORDER_STATUS_FAIL  	   => '失败'
    );

    /**
     * CLi向MC发起审核用户订单创建
     * @param array $aWithdraw 提现记录列表数组
     * @return status list
     */
    public function _doWithdrawOrderCliProcess($aWithdraw = array(), $bIsWeiXin = 0)
    {
        if ((date('H') < 9 || date('H') > 21)) return BaseTask::TASK_RESTORE;
          if(empty($aWithdraw))
         {
             $this->status = self::WITHDRAWAL_ORDER_STATUS_NEW;
             $aWithdraw = $this->getLefuOrderList();
         }

        $url = Sysconfig::readValue('lefu_request_url').'/api/quickdraw';
        $post_data = array();
        $post_data['partner'] = $bIsWeiXin ? SysConfig::readValue('lefu_weixin_partner') : SysConfig::readValue('lefu_partner');
        $post_data['input_charset'] = 'utf-8';
        $post_data['sign_type'] = 'SHA1WITHRSA';
        $data['service'] = 'pay';
        $data['partner'] = $bIsWeiXin ? SysConfig::readValue('lefu_weixin_partner') : SysConfig::readValue('lefu_partner');
        $data['out_trade_no'] = $aWithdraw['serial_number'];
        $data['amount_str'] = trim($aWithdraw['amount']);

        $aSupportBanks = [
            25 => 'ICBC',
            27 => 'ABC',
            26 => 'CCB',
            30 => 'BOCM',
            28 => 'BOC',
            29 => 'CMB',
            40 => 'PSBC',
            38 => 'HXB',
            37 => 'CIB',
            34 => 'CGB',
            32 => 'CITIC',
            31 => 'CMBC',
        ];
        if (!isset($aSupportBanks[$aWithdraw['bank_id']])) return BaseTask::TASK_RESTORE;
        $data['bank_sn'] = $aSupportBanks[$aWithdraw['bank_id']];
        $data['bank_site_name'] = $aWithdraw['branch_address'];
        $data['bank_account_name'] = $aWithdraw['account_name'];
        $data['bank_account_no'] = $aWithdraw['account'];
        $data['bus_type'] = 11;
        $data['bank_mobile_no'] = 13500000000;
        $aCities = [
            '北京' => [
                'province' => '北京市',
                'city' => '北京市',
            ],
            '上海' => [
                'province' => '上海市',
                'city' => '上海市',
            ],
            '天津' => [
                'province' => '天津市',
                'city' => '天津市',
            ],
            '重庆' => [
                'province' => '重庆市',
                'city' => '重庆市',
            ],
            '广西' => [
                'province' => '广西自治区',
                'city' => '南宁市',
            ],
            '内蒙古' => [
                'province' => '内蒙古自治区',
                'city' => '呼和浩特市',
            ],
            '西藏' => [
                'province' => '西藏自治区',
                'city' => '拉萨市',
            ],
            '宁夏' => [
                'province' => '宁夏自治区',
                'city' => '银川市',
            ],
            '新疆' => [
                'province' => '新疆自治区',
                'city' => '乌鲁木齐市',
            ],
            '广东' => [
                'province' => '广东省',
                'city' => '广州市',
            ],
            '福建' => [
                'province' => '福建省',
                'city' => '厦门市',
            ],
            '浙江' => [
                'province' => '浙江省',
                'city' => '杭州市',
            ],
            '江苏' => [
                'province' => '江苏省',
                'city' => '南京市',
            ],
            '山东' => [
                'province' => '山东省',
                'city' => '青岛市',
            ],
            '山西' => [
                'province' => '山西省',
                'city' => '太原市',
            ],
            '辽宁' => [
                'province' => '辽宁省',
                'city' => '沈阳市',
            ],
            '吉林' => [
                'province' => '吉林省',
                'city' => '吉林市',
            ],
            '黑龙江' => [
                'province' => '黑龙江省',
                'city' => '哈尔滨市',
            ],
            '河北' => [
                'province' => '河北省',
                'city' => '石家庄市',
            ],
            '河南' => [
                'province' => '河南省',
                'city' => '郑州市',
            ],
            '四川' => [
                'province' => '四川省',
                'city' => '广元市',
            ],
            '陕西' => [
                'province' => '陕西省',
                'city' => '西安市',
            ],
            '湖北' => [
                'province' => '湖北省',
                'city' => '武汉市',
            ],
            '湖南' => [
                'province' => '湖南省',
                'city' => '长沙市',
            ],
            '江西' => [
                'province' => '江西省',
                'city' => '南昌市',
            ],
            '云南' => [
                'province' => '云南省',
                'city' => '昆明市',
            ],
            '安徽' => [
                'province' => '安徽省',
                'city' => '合肥市',
            ],
            '海南' => [
                'province' => '海南省',
                'city' => '海口市',
            ],
            '贵州' => [
                'province' => '贵州省',
                'city' => '贵阳市',
            ],
            '甘肃' => [
                'province' => '甘肃省',
                'city' => '兰州市',
            ],
            '青海' => [
                'province' => '青海省',
                'city' => '西宁市',
            ]
        ];
        $data['bank_province'] = $aCities[$aWithdraw['province']]['province'];
        $data['bank_city'] = $aCities[$aWithdraw['province']]['city'];
        $data['user_agreement'] = 1;
        $data['return_url'] = SysConfig::readValue('lefu_return_url');
        ksort($data);
        $sPath   =   dirname(app_path())."/config/lefu/rsa_private_key.pem";  //私钥地址
        $sPrivateKey= file_get_contents($sPath);
        $sData = self::arrayToUrl($data);
        $sSign = self::sign($sData, $sPrivateKey);
        $post_data['sign'] = $sSign;

        $post_data['request_time'] = date('YmdHis');

        $sPath   =   dirname(app_path())."/config/lefu/lefu_public_key.pem";  //平台公钥
        $sLefuPublicKey = file_get_contents($sPath);
        $post_data['content'] = self::encrypt_public($sData, $sLefuPublicKey);
        ksort($post_data);
        @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s').' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);

        $w = $aWithdraw;

        $aWithdrawInfo = Withdrawal::find($w['id']);

        $check_status = array(2,9,10); //未处理和异常返回和处理中

        if(empty($aWithdrawInfo) && !in_array($aWithdrawInfo['status'], $check_status))
        {
            return BaseTask::TASK_RESTORE;
            die;
        }
        //平台订单号
        $company_order_num					= $w['serial_number'];

        $orderData['withdrawal_id'] 		= $w['id'];
        $orderData['company_order_num'] 	= $company_order_num;
        $orderData['detail'] 				= $w['id'];
        $orderData['request_time'] 			= date("Y-m-d H:i:s");
        $orderData['status'] 				= self::WITHDRAWAL_ORDER_STATUS_NEW;
        $orderData['created'] 				= date("Y-m-d H:i:s");
        $orderData['amount'] 				= $w['amount'];
        $orderData['card_num'] 				= $w['account'];
        $orderData['card_name'] 			= $w['account_name'];

        $oUser = UserUser::find($w['user_id']);
        $orderData['company_user'] 			= $oUser->user_flag;

        //thk 订单ID
        $iWithdrawId = $w['id'];

        //插入初始化的订单表
        $oLefuOrder = new LefuOrder();
        $oLefuOrder->fill($orderData);
        $oLefuOrder->save();

        //针对订单失败的情况
        if( in_array($aWithdrawInfo['status'], array(10)) )
        {
            @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s').' send request url : '.$url.' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);
            $aCheckResult = self::doPostRequest($url, $post_data);
            $aCheckResult = json_decode($aCheckResult, true);
            @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s').' response data : '.json_encode($aCheckResult)."\n\r", FILE_APPEND);
            if($aCheckResult['is_succ'] == "T")
            {
                //更新订单表内容
                $aWithdrawOrderData = array(
                    'response_time' 	=> date('Y-m-d H:i:s'),
                    'company_order_num'  => $company_order_num,
                    'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
                );
                Withdrawal::find($iWithdrawId)->update($aWithdrawOrderData);
            }
            return BaseTask::TASK_RESTORE;
            die;
        }
        @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s').' send request url : '.$url.' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);
        $aCheckResult = '';
        $n = 1;
        while(!$aCheckResult && $n <= 10){
            $aCheckResult = self::doPostRequest($url, $post_data);
            $n ++;
        }
        $aCheckResult = json_decode($aCheckResult, true);
        @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s').' response data : '.json_encode($aCheckResult)."\n\r", FILE_APPEND);

        if($aCheckResult['is_succ'] == "T"){
            $aWithdrawOrderData = array(
                'response_time' 	=> date('Y-m-d H:i:s'),
                'company_order_num'  => $company_order_num,
                'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
            );

            $aWithdrawData = array(
                'request_time'       => date('Y-m-d H:i:s'),
                'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
            );
            $oLefuOrder->update($aWithdrawOrderData);
            Withdrawal::find($iWithdrawId)->update($aWithdrawData);
            return BaseTask::TASK_SUCCESS;
        }else{
            $status = self::WITHDRAWAL_ORDER_RETURN_ERROR;
            Withdrawal::find($iWithdrawId)->update(['status'=>$status,'error_msg'=>$aCheckResult['fault_reason'],'request_time'=>date('Y-m-d H:i:s')]);
            return BaseTask::TASK_RESTORE;
        }
        return BaseTask::TASK_RESTORE;
        die;
    }

    public static function doPostRequest($url, $data) {
        $php_errormsg = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        if (empty($response)){
            @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s')." Problem with $url, $php_errormsg, params : ".json_encode($data)."\n\r", FILE_APPEND);
        }else{
            @file_put_contents('/tmp/lefu', date('Y-m-d H:i:s')." success: $url, $php_errormsg, response : ".json_encode($response)."\n\r", FILE_APPEND);
        }
        return $response;
    }

    public function getLefuOrderList( $iWithdrawalId = NULL)
    {
        if(empty($iWithdrawalId))
        {
            Withdrawal::where("withdrawal_id",'=',$iWithdrawalId);
        }

        $iCurrentStatus = $this->status;
        self::where('status','=',$iCurrentStatus);

        return LefuOrder::get();
    }

    public function deductUserFund($user_id,$mc_amount=0,$amount_freeze,$part_pay = FALSE)
    {
        //account ID
        $o_account    = Account::getAccountInfoByUserId($user_id);
        $account_id   = $o_account->id;
        $o_user = User::find($user_id);

        $return = false;
        if($mc_amount != 0 )
        {
            $iReturn_part           = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
            $iReturn                = Transaction::addTransaction($o_user,$o_account,TransactionType::TYPE_WITHDRAW,$mc_amount);
            $amount_need_frezze     = $amount_freeze;
            $iReturn_part   == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true :  $return = false;
            $iReturn        == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true : $return = false;
        }else{
            $iReturn1 = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
            $iReturn1 == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true : $return = false;
        }

        return $return;
    }

    public function getLefuOrderInfo($aWithdraw){
        $url = SysConfig::readValue('lefu_request_url').'/api/gateway';
        $post_data = array();
        $post_data['service'] = 'find_trade';
        $post_data['partner'] = Sysconfig::readValue('lefu_partner');
        $post_data['out_trade_no'] = $aWithdraw['serial_number'];
        $data['partner'] = Sysconfig::readValue('lefu_partner');
        $data['input_charset'] = 'UTF-8';
        $data['sign_type'] = 'SHA1WITHRSA';
        ksort($post_data);
        $sPath   =   dirname(app_path())."/config/lefu/rsa_private_key.pem";  //私钥地址
        $sPrivateKey= file_get_contents($sPath);
        $sData = self::arrayToUrl($post_data);
        @file_put_contents('/tmp/lefu_query', $sData . "\r\n", FILE_APPEND);
        $sSign = self::sign($sData, $sPrivateKey);
        $data['sign'] = $sSign;
        $data['sign'] = 'SHA1WITHRSA';
        $data['request_time'] = date('YmdHis');
        $sPath   =   dirname(app_path())."/config/lefu/lefu_public_key.pem";  //平台公钥
        $sLefuPublicKey = file_get_contents($sPath);
        $data['content'] = self::encrypt_public($sData, $sLefuPublicKey);
        $aResponse = self::doPostRequest($url, $data);
        @file_put_contents('/tmp/lefu_query', json_encode($aResponse) . "\r\n", FILE_APPEND);
        $aResponse = json_decode($aResponse, TRUE);
        return $aResponse;
    }

    public static function encrypt_public($data, $publicKey) {
        $crypto = '';

        foreach (str_split($data, 117) as $chunk) {

            openssl_public_encrypt($chunk, $encryptData, $publicKey);

            $crypto .= $encryptData;
        }

        return base64_encode($crypto);
    }

    public static function arrayToUrl($array = []) {
        ksort($array);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($array as $k => $v) {
            if (empty($k) || empty($v)) {
                continue;
            }
            if ($i == 0) {
                $stringToBeSigned .= "$k" . "=" . "$v";
            } else {
                $stringToBeSigned .= "&" . "$k" . "=" . "$v";
            }
            $i++;
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    public static function sign($data, $privateKey) {
        openssl_sign($data, $sign, $privateKey);
        return base64_encode($sign);
    }

    static function verify($data, $sign, $publibKey)
    {
        $publibKey = openssl_get_publickey($publibKey);
        $sign = base64_decode($sign);
        $result = (bool)openssl_verify($data, $sign, $publibKey);
        if ($result != true) {
            return false;
        }
        return true;
    }

    static function decrypted_private($data, $privateKey) {

        $crypto = '';

        foreach (str_split(base64_decode($data), 128) as $chunk) {

            openssl_private_decrypt($chunk, $decryptData, $privateKey);

            $crypto .= $decryptData;
        }

        return $crypto;
    }

}
