<?php

namespace App\Http\Controllers;

use libs\accessToken;
use UserUser;
use User;
use Account;
use UserUserBankCard;
use SysConfig;
use Bank;
use Mbank;
use UserDeposit;
use Withdrawal;
use UserWithdrawal;
use Transaction;
use TransactionType;
use LefuOrder;
use UserOnline;
use Slot;

use Illuminate\Http\Request;
use Validator;
use DB;
use libs\Users as _users;
use libs\MyCurl;
use libs\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * 是否需要验证用户资金密码（如有需要可改为配置方式）
     * @var boolean
     */
    protected $bCheckFundPassword = false;

    protected $errorFiles = ['system', 'fund', 'account', 'suggestion'];

    /**
     * 充值响应验证规则
     * @var array
     */
    private $depositApiRules = [
        'bank_card_num' => 'RequiredIf:mode,1',
        'bank_acc_name' => 'RequiredIf:deposit_mode,1',
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'email' => 'RequiredIf:mode,2',
        'company_order_num' => 'required|between:1,64',
        'datetime' => 'required|date',
        'key' => 'required|between:32,32',
        'note' => 'RequiredIf:deposit_mode,1',
        'mownecum_order_num' => 'required|between:1,64',
        'status' => 'required|in:0,1',
        'error_msg' => 'RequiredIf:status,0',
        'mode' => ['required', 'in:0,1,2'],
        'issuing_bank_address' => '',
        'break_url' => ['RequiredIf:deposit_mode,2', 'RequiredIf:deposit_mode,3', 'between:1,1000', 'url'],
        'deposit_mode' => 'required|in:1,2,3',
        'collection_bank_id' => ['RequiredIf:deposit_mode,1', 'integer'],
    ];

    /**
     * 下发主钱包信息和渠道钱包配置
     * @param Request $request
     */
    public function agent(Request $request){
        if (null == $request->input('agent_id')){
            return retJson(1, [], '渠道ID缺失!');
        }

        $aWallets = Account::$aWallets;

        if (null == $aWallets[$request->input('agent_id')]){
            return retJson(1, [], '渠道ID不存在!');
        }



        // 根据用户ID查询用户信息
        $iUserId = $request->token_to_user->user_id;
        $_users = new _users($iUserId);

        $aWallet = [];
        foreach (Account::$aWallets as $iWallet => $sWallet){
            $aWallet[$iWallet - 1]['wallet_id'] = $iWallet;
            $aWallet[$iWallet - 1]['name'] = $sWallet;
        }

        $data['wallet'] = array();

        $wallet_name_conf = config('controller.wallet.name');

        $data['wallet'][] = array(
            'id'=>$request->input('agent_id'),
            'name'=>isset($wallet_name_conf[$request->input('agent_id')]) ? $wallet_name_conf[$request->input('agent_id')] : '**钱包',
            // 获取用户在各个钱包中的金钱
            'value'=>sprintf("%.2f",substr(sprintf("%.3f", $_users->getBalance($request->input('agent_id'))), 0, -1))
        );

        return retJson(0, $data, '');
    }

    /**
     * 下发用户钱包信息
     * @param Request $request
     * @return string
     */
    public function index(Request $request){
        // 返回数据
        $data = array();

        // 根据用户ID查询用户信息
        $iUserId = $request->token_to_user->user_id;
        $_users = new _users($iUserId);

        // 判断用户是否设置资金密码
        if (!$_users->isSetFundPassword()){
            return retJson('5', [], '未设置资金密码!');
        }
        // 重新计算账户总金额
        //$oAccount->total = $pt + $im + $ag + $hb + $mg + $ava;
        //$oAccount->save();

        // 获取用户绑定的银行卡信息
        $data['cards'] = array();
        foreach ($_users->getBankCards() as $bankCard) {
            $data['cards'][] = $bankCard;
        }
        $aWallets = array();
        foreach (Account::$aWallets as $aWalletId =>$aWallet) {
            $aWallets[] = array(
                'id' => $aWalletId,
                'name' => $aWallet,
            );
        }
        
        // 渠道信息
        $data['agentList'] = $aWallets;

        return retJson(0, $data);
    }

    /**
     * 查询用户资金密码和绑定银行卡
     * @param Request $request
     * @return bool
     */
    public function paycheck(Request $request){
        $iUserId = $request->token_to_user->user_id;

        $_users = new _users($iUserId);

        $data['pcLink'] = 'http://www.qupai88.com';

        // 未设置资金密码
        if (!$_users->isSetFundPassword()){
            return retJson('5', $data, '未设置资金密码');
        }

        $num = count($_users->getBankCards());

        if (intval($num) < 1){
            return retJson('6', $data, '未绑定银行卡');
        }

        return retJson(0);
    }

    /**
     * 下发支付列表
     * @param Request $request
     */
    public function payList(Request $request){
        $iUserId = $request->token_to_user->user_id;
        $_users = new _users($iUserId);

        // 判断用户是否设置资金密码
        if (!$_users->isSetFundPassword()){
            return retJson(5);
        }

        // 用户是否被禁止充值
        if ($_users->isBlockFund()) {
            return retJson(7);
        }

        // 是否开放充值
        if (SysConfig::readValue('prohibited_withdraw')) {
            return retJson(8);
        }


        $data['pList'] = array(
/*            array(
                'key'=>'way-img-netbank',
                'name'=>'网银',
                'deposit_mode'=>1,
                'child'=>$this->payBanks(1),
            ),*/
            array(
                'key'=>'way-img-quick',
                'name'=>'网银快捷',
                'deposit_mode'=>2,
                'child'=>$this->payBanks(2),
            ),
            array(
                'key'=>'way-img-weixin',
                'name'=>'微信',
                'deposit_mode'=>2,
                'bank_code'=>'WEIXIN',
                'child'=>$this->payBanks(3),
            ),
/*            array(
                'key'=>'way-img-alipay',
                'name'=>'支付宝',
                'child'=>$this->payBanks(4),
            ),*/
        );

        return retJson(0, $data);
    }

    /**
     * 生成订单
     * @param Request $request
     * @return array|mixed|string
     */
    public function order(Request $request){

        // 创建用户类
        $iUserId = $request->token_to_user->user_id;
        $oUser = UserUser::find($request->token_to_user->user_id);
        $_users = new _users($iUserId);

        // 判断用户是否设置资金密码
        if (!$_users->isSetFundPassword()){
            return retJson(5, [], '未设置资金密码!');
        }

        // 验证表单
        $aFormRules = [
            'bank' => 'numeric',
            'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'fund_password' => ($this->bCheckFundPassword ? 'required|' : '') . 'between:0, 60',
            'deposit_mode' => 'required|in:' . UserDeposit::DEPOSIT_MODE_BANK_CARD . ',' . UserDeposit::DEPOSIT_MODE_THIRD_PART,
        ];

        $validator = Validator::make($request->toArray(), $aFormRules);
        if (!$validator->passes()) { // 表单未通过验证
            $error = isset($validator->errors()->all()[0]) ? $validator->errors()->all()[0] : '参数异常';
            return retJson(2, [], $error);
        }

        // 支付密码异常
        if ($this->bCheckFundPassword && !$oUser->checkFundPassword($this->params['fund_password'])) {
            return retJson(6, [], '支付密码错误!');
        }

        // 用户选择银行转账
        if ($request->deposit_mode == UserDeposit::DEPOSIT_MODE_BANK_CARD) {
            $oBank = Bank::find($request->bank);
            if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
                return retJson(7, [], '该充值方式正在维护中!');
            }

            // 当前银行是否支持银行卡转账
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_BANK_CARD])) {
                return retJson(6, [], '该银行不支持银行卡转账!');
            }

            // 金额超出范围
            if ($request->amount < $oBank->min_load || $request->amount > $oBank->max_load) {
                return retJson(7, [], '转账金额不在该银行转帐额度范围内!');
            }
        }

        // 用户选择第三方充值
        if ($request->deposit_mode == UserDeposit::DEPOSIT_MODE_THIRD_PART) {
            $oBank = Bank::find($request->bank);
            if (!$oBank || $oBank->status != Bank::BANK_STATUS_AVAILABLE) {
                return retJson(7, [], '该充值方式正在维护中!');
            }

            // 当前银行是否支持第三方充值
            if (!in_array($oBank->mode, [BANK::BANK_MODE_ALL, BANK::BANK_MODE_THIRD_PART])) {
                return retJson(8, [], '该银行不支持第三方充值!');
            }
            $fMinLoad = number_format(SysConfig::readValue('deposit_3rdpart_min_amount '), 2, '.', '');
            $fMaxLoad = number_format(SysConfig::readValue('deposit_3rdpart_max_amount'), 2, '.', '');
            if ($request->amount < $fMinLoad || $request->amount > $fMaxLoad) { // 金额超出范围
                return retJson(9, [], '转账金额不在该银行转帐额度范围内!');
            }
        }

        // 生成订单号
        $CompanyOrderNum = UserDeposit::getDepositOrderNum();
        // 获取paymode
        $iPayMode = Bank::getPayMode($oBank->identifier, $request->deposit_mode);

        // 订单数据
        $aInitData = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'top_agent' => array_get(explode(',', $oUser->forefathers), 0),
            'bank_id' => $request['bank'],
            'amount' => $request['amount'],
            'company_order_num' => $CompanyOrderNum,
            'deposit_mode' => $request->deposit_mode,
            'pay_mode' => $iPayMode,
        ];

        // 如果是微信支付
        if(null !== $request->input('bank_code') && $request->input('bank_code') == 'WEIXIN'){

            if (intval($request->input('amount')) < \config('controller.wallet.weixin_min_cash') || intval($request->input('amount')) > \config('controller.wallet.weixin_max_cash')){
                return retJson(2, [], '充值金额未在指定的金额内,请重新选择!');
            }

            $aWeixinPayBank = $this->_getEnableWeixinPay();

            // log::info(json_encode($aWeixinPayBank));

            if(empty($aWeixinPayBank)) {
                return retJson(10, [], '微信支付未开启!');
            }

            // $iDepositWay = array_rand($aWeixinPayBank);
            $iDepositWay = 'lefu';
            if('lefu' == $iDepositWay){
                $weixindata = $this->sendRequestMessageForLefu($iDepositWay, $CompanyOrderNum, $aInitData);
                return retJson(0, $weixindata);
            }
        }

        // 判断是否银行卡是否在维护中
        $status = Mbank::isMaintainBank($request->input('bank'), $request->input('deposit_mode'));
        if ($status) {
            return retJson(5, [], '该充值渠道在维护中!');
        }

        // 4 是否达到充值次数上限
//        $sRequestMethod = Request::method();
        /* Step 2: 创建新订单 */
        // 创建订单
        $oUserDeposit = UserDeposit::createDeposit($aInitData);

        // 生成订单失败
        if (!$oUserDeposit) {
            return retJson(13, [], '订单生成失败!');
        }

        /* Step 3: 向Mownecum发送新订单请求 */
        $oApplyBank = Bank::find($request['bank']);

        $aDepositApiData = [
            'amount' => number_format($request['amount'], 2, '.', ''),
            'bank_id' => $oApplyBank->mc_bank_id,
            'company_order_num' => $CompanyOrderNum,
            'company_user' => $oUser->user_flag,
            'estimated_payment_bank' => $oApplyBank->mc_bank_id, // SAME AS bank_id,
            'deposit_mode' => $request->deposit_mode,
            'group_id' => 0, // 目前为空
            // 'web_url' => $request->server('HTTP_HOST'),
            'web_url' => 'www.qupai.lgv',
            'memo' => '',
            'note' => '',
            'note_model' => UserDeposit::DEPOSIT_NOTE_MODE_MOW, // 使用MOW附言
        ];

        $aResponse = $this->sendDeposit2Mownecum($aDepositApiData);

        return $this->_mcRender($aResponse, $oUserDeposit, $request->deposit_mode, $oApplyBank);
    }

    /**
     * 获取全部银行卡信息
     */
    private function payBanks($type){
        $type = isset($type) ? $type : '';

        switch ($type){
            // 支持银行卡转账
            case 1:
                $oAllBanks = Bank::getSupportCardBank();
                break;
            // 网银快捷
            case 2:
                $oAllBanks = Bank::getSupportThirdPartBank();
                break;
            // 微信
            case 3:
                $oAllBanks = Bank::getSupportWeixin();
                return $oAllBanks;
                break;
            // 支付宝
            case 4:
                $oAllBanks = Bank::getSupportAlipay();
                return $oAllBanks;
                break;
            default:
                return retJson(2);
                break;
        }

        foreach ($oAllBanks as $k=>$oBank) {
            if ($oBank->identifier == 'TENPAY'){
                unset($oAllBanks[$k]);
            }
/*            if ($oBank->identifier == 'WEIXIN'){
                $bIsWeiXinEnable = $oBank->status;
            }
            if ($oBank->identifier == 'ALIPAY'){
                $bIsAliPayEnable = $oBank->status;
            }
            $oAllBanks[$k]->is_net_mbank = Mbank::checkMbank($oBank->id, Mbank::BANK_MODE_BANK_CARD);
            $oAllBanks[$k]->is_quick_mbank = Mbank::checkMbank($oBank->id, Mbank::BANK_MODE_THIRD_PART);*/
        }
        $data = array();
        foreach ($oAllBanks as $bank) {
            $data[] = [
                'id' => $bank->id,
                'name' => $bank->name,
                'txMin' => SysConfig::readValue('withdraw_default_min_amount'),
                'txMax' => SysConfig::readValue('withdraw_default_max_amount'),
                'czMin' => $bank->min_load,
                'czMax' => $bank->max_load,
                'quick_max' => $bank->quick_max_load,
                'text' => $bank->notice,
                'identifier'=>$bank->identifier,
            ];
        }

        return $data;
    }

    private function sendRequestMessageForLefu($iDepositWay, $CompanyOrderNum, $aInitData){
        $CompanyOrderNum = 'lf' . $CompanyOrderNum;
        $aInitData['deposit_channel'] = $iDepositWay;
        $aInitData['company_order_num'] = $CompanyOrderNum;
        $oUserDeposit = UserDeposit::createDeposit($aInitData); // 创建订单

        if (!$oUserDeposit) { // 生成订单失败
            return retJson(11, [], '订单生成失败');
        }

        switch ($iDepositWay) {
            case 'lefu':
                $sResponse = $this->sendDeposit2LeFuForWeiXin($CompanyOrderNum, $aInitData['amount']);

                if (!$sResponse)
                    return $this->goBack('error', '网络繁忙，请稍后再试');

                $aSaveData = [
                    'status' => UserDeposit::DEPOSIT_STATUS_RECEIVED,
                ];

                $oUserDeposit->fill($aSaveData);
                if (!$oUserDeposit->save()) { // 系统错误，更新订单失败
                    file_put_contents("/tmp/deposit_validError", $oUserDeposit->validationErrors,FILE_APPEND);
                    throw new Exception(__('_deposit.deposit-error-05'));
                }

                $aResponse = json_decode($sResponse, true);
                $sPath   =   dirname(app_path())."/config/lefu/rsa_private_key.pem";  //私钥地址
                $sPrivateKey= file_get_contents($sPath);
                $sParams = LefuOrder::decrypted_private($aResponse['response'], $sPrivateKey);
                $aParams = json_decode($sParams, true);
                $sPath   =   dirname(app_path())."/config/lefu/lefu_public_key.pem";  //系统公钥地址
                $sPublicKey= file_get_contents($sPath);
                $bVerify = LefuOrder::verify($sParams, $aResponse['sign'], $sPublicKey);
                if ($bVerify) {
                    $aResponse['break_url'] = $aParams['base64QRCode'];
//                        $this->setVars('is_tester',$oUser->is_tester);
//                        $this->setVars('is_youjie',0);
//                        $this->setVars('is_weixin',1);
                    $this->view = 'centerUser.fundsManage.recharge.weixinPay';
                }else{
                    return retJson(12, [], '网络繁忙!');
                }
                break;
        }
        return array(
            'aResponse'=>$aResponse,
            'iDepositWay'=>$iDepositWay,
        );
    }

    private function sendDeposit2LeFuForWeiXin($sOutTradeNo, $fAmount){
        $url = SysConfig::readValue('lefu_request_url').'/api/gateway';
        $aRequestParams = [];
        $aRequestParams['service'] = 'wx_pay';
        $aRequestParams['partner'] = SysConfig::readValue('lefu_weixin_partner');
        $aRequestParams['out_trade_no'] = $sOutTradeNo;
        $aRequestParams['amount_str'] = $fAmount;
        $aRequestParams['wx_pay_type'] = 'wx_sm';
        $aRequestParams['subject'] = 'dianquan';
        $aRequestParams['sub_body'] = 'dianquan';
        $aRequestParams['remark'] = 'dianquan';
        $aRequestParams['return_url'] = SysConfig::readValue('lefu_return_url');
        $sPath   =   dirname(app_path())."/config/lefu/rsa_private_key.pem";  //私钥地址
        $sPrivateKey= file_get_contents($sPath);
        $sData = LefuOrder::arrayToUrl($aRequestParams);
        $sSign = LefuOrder::sign($sData, $sPrivateKey);
        $sPath   =   dirname(app_path())."/config/lefu/lefu_public_key.pem";  //平台公钥
        $sLefuPublicKey = file_get_contents($sPath);
        $aParams = [];
        $aParams['partner'] = SysConfig::readValue('lefu_weixin_partner');
        $aParams['input_charset'] = 'UTF-8';
        $aParams['sign_type'] = 'SHA1WITHRSA';
        $aParams['sign'] = $sSign;
        $aParams['request_time'] = date('YmdHis');
        $aParams['content'] = LefuOrder::encrypt_public($sData, $sLefuPublicKey);
        $aResponses = LefuOrder::doPostRequest($url, $aParams);
        return $aResponses;
    }

    private function _getEnableWeixinPay(){
        $oBank = Bank::find(48);

        $aData = [];
/*        if($oBank->is_enable_mc){
            $aData['mc'] = 1;
        }
        if($oBank->is_enable_tonghuika){
            $aData['tonghuika'] = 1;
        }
        if($oBank->is_enable_youfu){
            $aData['youfu'] = 1;
        }
        if($oBank->is_enable_fastpay){
            $aData['fastpay'] = 1;
        }
        if($oBank->is_enable_xinbei){
            $aData['xinbei'] = 1;
        }
        if($oBank->is_enable_youjie && Session::get('username') == 'testruby'){
            $aData['youjie'] = 1;
        }*/
        if($oBank->is_enable_lefu){
            $aData['lefu'] = 1;
        }
        return $aData;
    }

    /**
     * 向Mownecum发送订单数据
     * @param array $aData 要发送的数据包
     * @return array
     */
    private function sendDeposit2Mownecum(array $aData) {
//        $oSysConfig = new SysConfig;
        $sMcDepositUrl = SysConfig::readValue('mc_deposit_url');

        $iCompanyId = SysConfig::readValue('mc_company_id');
        $aData['company_id'] = $iCompanyId;
        $aData['key'] = UserDeposit::getApiKey($aData, UserDeposit::DEPOSIT_API_REQUEST);
        $oCurl = new MyCurl($sMcDepositUrl);
        $oCurl->setPost(http_build_query($aData));
        $oCurl->createCurl();

//        $oCurl->setTimeout(20);
        $oCurl->execute();
        $sResponse = $oCurl->__tostring();
        $this->writeLog(['url' => $sMcDepositUrl, 'date' => date('Y-m-d H:i:s'), 'request' => $aData, 'response' => $sResponse]);
        $aResponse = !empty($sResponse) ? json_decode($sResponse, true) : [];
        return $aResponse;
    }

    /**
     *
     * @param $aResponse
     * @param $oUserDeposit
     * @param $iDepositMode
     * @param $oApplyBank
     * @return mixed
     */
    private function _mcRender($aResponse, $oUserDeposit, $iDepositMode, $oApplyBank){
        try {
            if (empty($aResponse)) { // MC无响应
                return retJson(14, [], '生成MC订单无响应!');
            }
            if (!array_get($aResponse, 'status', 0)) { // MC主动返回错误
                // throw new Exception(__('_deposit.deposit-error-03'));
                return retJson(15, [], '生成MC订单出现异常!');
            }

            $verifyResult = $this->verifyApiResponse($aResponse,$oUserDeposit);

            if (!$verifyResult) { // MC响应信息未通过接口验证
                // throw new Exception(__('_deposit.deposit-error-04'));
                return retJson(16, [], 'MC响应信息未通过接口验证!');
            }

            $aSaveData = [
                'amount' => number_format(array_get($aResponse, 'amount'), 2, '.', ''),
                'note' => addslashes(array_get($aResponse, 'note')),
                'mownecum_order_num' => addslashes(array_get($aResponse, 'mownecum_order_num')),
                'accept_card_num' => addslashes(array_get($aResponse, 'bank_card_num')),
                'accept_email' => addslashes(array_get($aResponse, 'email')),
                'accept_acc_name' => addslashes(array_get($aResponse, 'bank_acc_name')),
                'accept_bank_address' => array_get($aResponse, 'issuing_bank_address', ''),
                'mode' => array_get($aResponse, 'mode'),
                'break_url' => addslashes(array_get($aResponse, 'break_url', '')),
                'status' => UserDeposit::DEPOSIT_STATUS_RECEIVED,
            ];

            if ($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD) { // 银行转账充值时进行特别处理
                $oCollectionBank = Bank::findBankByMcBankId(array_get($aResponse, 'collection_bank_id'));
                $aSaveData['collection_bank_id'] = $oCollectionBank->id;
            }
            if ($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART) { // 第三方充值时进行特别处理
                $iStrPos = strpos(array_get($aResponse, 'break_url', ''), 'token=');
                if ($iStrPos !== false) {
                    $iStrPos += 6; // 加上'token='的长度6
                    // 只取MC的token进行MD5以防止在高并发下多域名使用同一token的问题
                    $aSaveData['mc_token'] = md5(substr(trim($aResponse['break_url']), $iStrPos));
                }
            }
            $oUserDeposit->fill($aSaveData);
            if (!$oUserDeposit->save()) { // 系统错误，更新订单失败
                file_put_contents("/tmp/deposit_validError", $oUserDeposit->validationErrors,FILE_APPEND);
//               pr($oUserDeposit->validationErrors);
                // throw new Exception(__('_deposit.deposit-error-05'));
                return retJson(18, [], '系统错误，更新订单失败');
            }

            //微信充值直接跳转到目标地址
            if($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART && $oApplyBank->mc_bank_id != 51){
                return retJson(0, array('break_url'=>$oUserDeposit->break_url));
            }
        } catch (Exception $e) {
            return retJson(17, [], $e->getMessage());
            // $oUserDeposit->setRefused(['error_msg' => array_get($aResponse, 'error_msg')]);
            //return $this->goBack('error', $e->getMessage());

        }
        /* Step 4: 页面展示 */
        // return View::make($this->resourceView . '.confirm');
        $data['ApplyBank'] = $oApplyBank->toArray();
        $data['oCollectionBank'] = $oCollectionBank->toArray();
        $data['oUserDeposit'] = $oUserDeposit->toArray();

        return retJson(0, $data);
    }

    /**
     * 验证订单响应接口
     * @param array $aResponse 得到的响应信息
     * @param Deposit $oDeposit 充值实例
     * @return boolean
     */
    private function verifyApiResponse(array $aResponse, $oDeposit) {
        $validator = Validator::make($aResponse, $this->depositApiRules);
        if (!$validator->passes()) {
            return false;
        }
        if ($aResponse['company_order_num'] != $oDeposit->company_order_num) { // company_order_num error
//            echo 'company_order_num error';
            return false;
        }
        if ($aResponse['deposit_mode'] == 1 && !in_array($aResponse['mode'], [1, 2]) || in_array($aResponse['deposit_mode'], [2, 3]) && !$aResponse['mode'] == 0) {
//            echo 'mode error';
            return false;
        }
        if (UserDeposit::getApiKey($aResponse, UserDeposit::DEPOSIT_API_RESPONSE) != $aResponse['key']) { // key error
//            echo 'key error';
            return false;
        }
        return true;
    }


    /**
     * 写充值日志
     * @param string|array $msg
     */
    protected function writeLog($msg,$url='/tmp/qupai_deposit') {
        !is_array($msg) or $msg = var_export($msg, true);
        @file_put_contents($url, $msg . "\n", FILE_APPEND);
    }


    public function cash(Request $request){
        $iUserId = $request->token_to_user->user_id;
        $_users = new _users($iUserId);
        $oUser = UserUser::find($iUserId);

        $sFundPassword = trim($request->input('fund_password'));
        $fAmount = floatval(trim($request->input('amount')));

        if (!is_object($oUser)) {
            return retJson(5 , [], '用户信息异常!');
        }
        if (!$_users->isSetFundPassword()){
            return retJson(6, [], '未设置资金密码!');
        }

        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return retJson(7, [], '帐号被锁定!');
        }
        // $oUserBandCard = new UserUserBankCard;
        if (!$iUserCardNum = $_users->getBankCards()) {
            // pr($iUserCardNum);exit;
            // return View::make('centerUser.withdrawal.noCard');// Redirect::route('user-withdrawals.withdrawal-card');
           return retJson(8, [], '用户未绑定银行卡');
        }
//        $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId($iUserId);
//        if(empty($oRes)){
//            return Redirect::route('security-questions.index');
//        }
        //当前是否允许发起提现
        if(SysConfig::readValue('prohibited_withdraw')){
            return retJson(9, [], '当前不允许发起提现申请');
        }

        $oUserBandCardModel = new UserUserBankCard;

        // 提现银行卡ID
        $iCardId = $request->id;

        $oUserBandCard = $oUserBandCardModel->getUserCardInfoById($iCardId);

        if (empty($oUserBandCard) || $oUserBandCard->user_id != $iUserId) {
            return retJson(10, [], '银行卡信息异常');
        }

        // 新增/修改卡后2个小时才可以提现
        if (Carbon::now()->subHour(Withdrawal::WITHDRAWAL_TIME_LIMIT)->toDateTimeString() < $oUserBandCard->updated_at) {
            return retJson(11, [], '新增/修改银行卡后2个小时才可以提现');
        }

        // pr($oUser->account_id);
        $this->Message = new Message($this->errorFiles);

        $oAccount = Account::lock($oUser->account_id, $iLocker);

        if (empty($oAccount)) {
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(12, [], '账户开户信息异常!');
        }

//            if ($fAmount > $oAccount->withdrawable) return Redirect::route('user-withdrawals.index')->with('error', '您的可提现余额不足，请稍后再试！');

        // pr(Input::all());
        // pr('----------');
        // pr($fAmount . '  ' . $oAccount->withdrawable);exit;
        // TODO 待过滤提现黑白名单
        // $aWithdrawalBlackList = [];
        // if (in_array($iUserId, $aWithdrawalBlackList)) {
        //     $this->langVars['reason'] = '无法提现 ';
        // }
        // TODO 提现金额最小值，应该等同于所选银行卡的最小提现金额
        $fMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $fMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');

        if (!$bValidated = (is_float($fAmount) && $fAmount >= $fMinWithdrawAmount && $fAmount <= $fMaxWithdrawAmount)) {
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(13, [], '提现金额不符合规则!');
        }

        // 暂时使用可用余额，后期改为可提现余额true_withdrawable
      if (!$bValidated = $fAmount <= $oAccount->available) {
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(14, [], '提现金额低于用户拥有的金额');
        }

        // 资金密码检查
        if (!$bValidated = $oUser->checkFundPassword($sFundPassword)) {
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(15, [], '资金密码错误');
        }
        $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily'); // UserWithdrawal::WITHDRAWAL_LIMIT_PER_DAY;
        $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);

        if ($iWithdrawLimitNum > 0 && $iWithdrawalNum >= $iWithdrawLimitNum) {
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(16, [], '超出每天提现次数');
        }

        $oWidthdrawal = new UserWithdrawal;
        // pr($this->params);
        $data = &$oWidthdrawal->compileData($request->id, $request->amount);

        // pr($data);
        $oWidthdrawal->fill($data);
        // pr($oWidthdrawal->toArray());exit;
        DB::connection()->beginTransaction();
        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_FREEZE_FOR_WITHDRAWAL, $fAmount);

        // pr($iReturn);exit;
        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
//                pr(Transaction->getValidationErrorString());
//                pr($iReturn);
//                pr($this->Message->getResponseMsg($iReturn));
//                exit;
            return retJson(17, [], '事务处理异常!');
        }
        if ($bSucc = $oWidthdrawal->save()) {
            DB::connection()->commit();
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(0);
        } else {
            // $queries = DB::getQueryLog();
            // $last_query = end($queries);
            // pr($last_query);
            // pr($oWidthdrawal->toArray());
            // pr('---------');
            // pr($oWidthdrawal->validationErrors);exit;
            Session::put(self::WITHDRAWAL_STATUS, 1);
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            return retJson(18);
        }
    }


    //获取游戏连接
    public function getUrl(Request $request){
        $iUserId = $request->token_to_user->user_id;
        $sPlatform = $request->input('sPlatform');
        $sGameCode = $request->input('sGameCode');

        $bLogin = $this->checkLogin($iUserId);

        if (!$bLogin){
            return retJson(5, '', '用户不在线!');
        };


        // 小写处理
        $ImProvider = [];
        foreach (Slot::$aImProviders as $aImProvider) {
            $ImProvider[] = strtolower($aImProvider);
        }

        if (in_array($sPlatform, $ImProvider)){
            $sPlatform = 'Im';
        }


        $sGameCode = trim($sGameCode);
        $sPlatform = ucfirst(strtolower($sPlatform));
        $oPlatformApi = $sPlatform::getInstance();

        $iUserId = $iUserId;
        $oUser = User::find($iUserId);

        //检查用户对应平台是否已经创建了账号
        $bExists = $oUser->checkPlayerExists(strtolower($sPlatform));

        if ($bExists) {
            $sPlatformUsernameField = strtolower($sPlatform).'_username';
            $sPlatformPasswordField = strtolower($sPlatform).'_password';
            $sPlatformUsername = $oUser->$sPlatformUsernameField;
            $sPlatformPassword = $oUser->$sPlatformPasswordField;

            if($sPlatform == 'Im' && isMobile()){
                $aResponse = $oPlatformApi->launchMobileGame($sPlatformUsername, $sGameCode);
            } else {
                $aResponse = $oPlatformApi->launchGame($sPlatformUsername, $sPlatformPassword, $sGameCode);
            }

            if (is_array($aResponse)){
                Log::info(json_encode($aResponse));
            }else{
                Log::info($aResponse);
            }

            //由于PT需要使用模拟登录，所有做了特殊处理
            if($sPlatform == 'Pt' && $aResponse['Code'] == 0){
                return retJson(0, $aResponse);
            }


            if (filter_var($aResponse, FILTER_VALIDATE_URL)){
                return retJson(0, ['GameUrl'=>$aResponse]);
            }

            file_put_contents('/tmp/launch_game_debug', json_encode($aResponse)."\n\r", FILE_APPEND);

            /*            if (is_array($aResponse)){
                            Log::info(json_encode($aResponse));
                        }else{
                            Log::info($aResponse);
                        }*/

            return retJson(6, [], '游戏正在维护，请稍后再试!');

        }else{
            return retJson(7, [], '平台账户尚未创建');
        }
    }

    /**
     * 检查是否登录
     * @return bool
     */
    private function checkLogin($iUserId) {
        $user_id = $iUserId;
        if(!$user_id){
            return boolval($user_id);
        }
        return UserOnline::isOnline($user_id);
    }


    public function trans(Request $request){
        $userId = accessToken::$session['user_id'];

        $fAmount = $request->amount;
        $iWalletOut = $request->wallet_out_id;
        $iWalletIn = $request->wallet_in_id;

        if (!$iWalletOut || !$iWalletIn){
            return retJson(2);
        }

        $oUser = User::find($userId);
        //资金已冻结
        if ($oUser->blocked == User::BLOCK_FUND_OPERATE) {
            return retJson(5, [], '用户被锁定');
        }

        if (empty($fAmount) || !is_numeric($fAmount) || $fAmount < 0 || (($iWalletIn == Account::WALLET_AG || $iWalletOut == Account::WALLET_AG) && !is_int($fAmount + 0))) {
            return retJson(6, [], '转账金额异常');
        }

        $oAccount = Account::lock($oUser->account_id, $this->accountLocker);
        if (!$oAccount) {
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            return retJson(4, [], '用户账户失效');
        }

        $aExtraData = ['related_user_id' => $oUser->id, 'related_user_name' => $oUser->username];

        $sWallet = Account::$aWallets[$iWalletOut];
        $sPlatformLock = strtolower(Account::$aPlatform[$iWalletOut]).'_lock';

        if(Account::$aPlatform[$iWalletOut] != '' && $oAccount->$sPlatformLock == Account::LOCK_TRANSFER_OUT) {
            $oActivityReport = ActivityReport::doWhere([
                'user_id' => ['=', $userId],
                'wallet' => ['=', $iWalletOut]
            ])->orderBy('id', 'desc')->first();

            return retJson(7, array(), strtoupper(Account::$aPlatform[$iWalletOut]).'钱包未完成活动任务，您还需'.($oActivityReport->total_turnover - $oActivityReport->finished_turnover).'流水，或等5分钟后系统同步投注信息后再来转账，谢谢！');
        }

        if (Account::$aPlatform[$iWalletOut] != ''){
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            $bExists = $oUser->checkPlayerExists(strtolower(Account::$aPlatform[$iWalletOut]));
            if(!$bExists){
                Account::unLock($oUser->account_id, $this->accountLocker, false);
                return retJson(4, [], 'checkPlayer fail!');
            }
        }

        $aInResponse = $this->updateLockByBalance($iWalletIn, $oUser, $oAccount);
        Account::unLock($oUser->account_id, $this->accountLocker, false);
        $aOutResponse = $this->updateLockByBalance($iWalletOut, $oUser, $oAccount);

        if (!$aOutResponse['status'] || !$aInResponse['status']){
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            return retJson(4, [], 'updateLock fail');
        }

        $fWalletOutBalance = $aOutResponse['balance'];
        if($fWalletOutBalance < $fAmount) {
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            return retJson(7, [], '金额异常!');
        }

        DB::beginTransaction();
        $bOutSucc = $this->doTransaction($iWalletOut, $oUser, $oAccount, "OUT", $fAmount, $aExtraData);
        if (!$bOutSucc) {
            DB::rollback();
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            return retJson(8, [], 'error', '转账失败-'.Account::$aWallets[$iWalletOut].'转出失败');
        } else {
            DB::commit();
        }

        DB::beginTransaction();
        $bInSucc = $this->doTransaction($iWalletIn, $oUser, $oAccount, "IN", $fAmount, $aExtraData);
        if (!$bInSucc) {
            DB::rollback();
            Account::unLock($oUser->account_id, $this->accountLocker, false);
            return retJson(9, array(), '转账失败-'.Account::$aWallets[$iWalletIn].'转入失败');
        }else {
            DB::commit();
        }

        Account::unLock($oUser->account_id, $this->accountLocker, false);
        return retJson(0, [], '转账成功');
    }

    public function doTransaction($iWallet, $oUser, $oAccount, $sType, $fAmount, $aExtraData){
        @file_put_contents('/tmp/user-transfer', '['.date('Y-m-d H:i:s').'] 用户:'.$oUser->username.' '.Account::$aWallets[$iWallet].' 转账类型:'.$sType.' 金额:'.$fAmount."\n\r", FILE_APPEND);
        $iType = Account::$aTransactionType[$sType][$iWallet];
        //当钱包为主钱包，默认转账成功
        if (!$iType) return true;
        $iReturn = Transaction::addTransaction($oUser, $oAccount, $iType, $fAmount, $aExtraData, $sSerialNumber);
        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
            @file_put_contents('/tmp/user-transfer', '['.date('Y-m-d H:i:s').'] 用户:'.$oUser->username.' 账变类型type_id:'.$iType.' 生成失败'."\n\r", FILE_APPEND);
            return false;
        }
        $sPlatform = Account::$aPlatform[$iWallet];
        if (!$sPlatform) return true;
        $sUsernameField = strtolower($sPlatform).'_username';
        $sPasswordField = strtolower($sPlatform).'_password';
        $oApi = $sPlatform::getInstance();
        $bSucc = $oApi->transaction($oUser->$sUsernameField, $oUser->$sPasswordField, $sSerialNumber, $sType, $fAmount);
        @file_put_contents('/tmp/user-transfer', '['.date('Y-m-d H:i:s').'] 用户:'.$oUser->username.' 转账类型:'.$sType.' 调用转账接口状态:'.$bSucc."\n\r", FILE_APPEND);
        if ($bSucc && $sType == "IN") {
            $sPlatformLockField = strtolower($sPlatform).'_lock';
            if ($oAccount->$sPlatformLockField == Account::LOCK_TRANSFER_OUT) {
                $oActivityReport = ActivityReport::doWhere([
                    'user_id' => ['=', $oUser->id],
                    'wallet' => ['=', $iWallet],
                ])->orderBy('id', 'desc')->first();
                if ($oActivityReport) {
                    $oActivityReport->transfer_in += $fAmount;
                    $oActivityReport->save();
                }
            }
        }

        return $bSucc;
    }

    private function updateLockByBalance($iWallet, & $oUser, & $oAccount){
        $sPlatform = Account::$aPlatform[$iWallet];

        if (!$sPlatform) {
            return ['status' => 1, 'balance' => $oAccount->available];
        }
        $sWallet = strtolower($sPlatform);
        if(!$oUser->checkPlayerExists($sPlatform)) return ['status' => 0, 'balance' => $oAccount->$sWallet];
        $oApi = $sPlatform::getInstance();
        $sUsernameField = strtolower($sPlatform).'_username';
        $sPasswordField = strtolower($sPlatform).'_password';
        $aResponse = $oApi->getBalance($oUser->$sUsernameField, $oUser->$sPasswordField);
        if (!$aResponse || !$aResponse['status'] || !isset($aResponse['status']) || $aResponse['status'] == 0 || !isset($aResponse['balance'])) {
            return ['status' => 0, 'balance' => $oAccount->$sWallet];
        }
        $fBalance = $aResponse['balance'];
        $oAccount->$sWallet = $fBalance;
        $sPlatformLockField = strtolower($sPlatform . '_lock');
        if ($oAccount->$sPlatformLockField == Account::LOCK_TRANSFER_OUT) {
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
                if($oActivityReport) $this->checkActivityStatus($oActivityReport);
            }
        }
        if($oAccount->save()) return ['status' => 1, 'balance' => $fBalance];
    }
}
