<?php
namespace App\Http\Controllers;

use libs\Games;
use libs\Users as _users;
use libs\Encrypt;
use Carbon\Carbon;
use libs\accessToken;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Validator;


use DB;
use Role;
use UserUser;
use Activity;
use UserOnline;
use Transaction;
use ActivityRule;
use TransactionType;
use ActivityReport;
use InvitationCode;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

/**
 * 非登录用户可访问
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * 忘记密码接口参数规则
     * @var array
     */
    private $rules_resetpassword = [
        'email' => 'required|email',
    ];

    /**
     * 注册接口参数规则
     * @var array
     */
    private $rules_register = [
        'username' => 'bail|required|regex:/^[a-zA-Z0-9]{6,16}$/|unique:users,username,',
        'name' => 'required|between:1,20',
        'nickname' => 'between:2,16',
        'qq' => 'required|regex:/^\d{6,14}$/|unique:users,qq,',
        'phone' => 'required|regex:/^1\d{10}$/|unique:users,phone,',
        'email' => 'required|email|between:0, 50|unique:users,email,',
        'code' => 'size:8',
        'checked' => 'accepted',

        'password' => 'required|confirmed|different:username',
        'password_confirmation' => 'required',
    ];

    /**
     * 注册异常信息
     * @var array
     */
    private $messages_register = [
        'username.required' => '用户名必须填写!',
        'username.regex' => '用户名格式不正确!',
        'username.unique' => '用户名已存在!',
        'name.required' => '姓名必须填写!',
        'name.between' => '姓名格式不正确!',
        'nickname.between' => '昵称格式不正确!',
        'qq.required' => 'QQ号码必须填写!',
        'qq.regex' => 'QQ号码格式不正确!',
        'qq.unique' => 'QQ号码已被占用！',
        'phone.required' => '电话号码必须填写!',
        'phone.regex' => '电话号码格式不正确!',
        'phone.unique' => '电话号码已被占用!',
        'email.required' => '邮箱必须填写!',
        'email.email' => '邮箱格式不正确!',
        'email.between' => '邮箱格式不正确!!',
        'email.unique' => '邮箱已被占用',
        'code.size' => '邀请码格式错误!',
        'checked.accepted' => '请勾选确认用户协议!',

        'password.required' => '密码必须填写!',
        'password.confirmed' => '两次输入密码必须相同!',
        'password.different' => '密码不能和用户名重复!',
    ];

    /**
     * 登录验证
     * @var array
     */
    private $rules_login = array(
        'username' => 'bail|required',
        'password' => 'required|different:username',
    );

    /**
     * 站点初始信息下发
     */
    public function init(){
        $data['dom'] = \config('controller.index.dom');
        // 客服链接
        $data['customerLink'] = \config('controller.index.customerLink');
        // 找回密码
        $data['resetpasswordLink'] = \config('controller.index.resetpasswordLink');
        // 展示信息
        $data['showMsg'] = \config('controller.index.showMsg');
        // 轮播图路径
        $data['banner_ing'] = \config('controller.index.banner_ing');

        // 游戏列表
        $games = Games::gamesSearch(null, null, null, [5], null, null)['data'];

        $data['games'] = array();
        $data['games']['img_file'] = \config('controller.index.games_file');
        foreach ($games as $game){
            if (isset($game['game_name_cn']) && isset($game['platform'])){
                $data['games']['games'][] = array(
                    'game_name_cn' => $game['game_name_cn'],
                    'game_code' => $game['game_code'],
                    'platform' => $game['platform'],
                    'img_name' =>$game['platform'] . '/' . $game['game_code'] . '.jpg',
                    'game_code' => $game['game_code']
                );
            }
        }

        // 游戏种类
        $data['agents'] = \config('controller.index.agents');

        return retJson(0, $data);
    }

    /**
     * 查询某个渠道下的游戏列表
     */
    public function games(Request $request){
        $name = null === $request->input('name') ? [] : $request->input('name');
        $platform = null === $request->input('platform') ? [] : $request->input('platform');

        $games = Games::gamesSearch($name, $platform, [],[], [], [])['data'];

        $data['img_file'] = \config('controller.index.games_file');
        $data['default_img_file'] = \config('controller.index.default_img_file');
        $data['games'] = array();
        foreach ($games as $game){
            if (isset($game['game_name_cn']) && isset($game['platform'])){
                $data['games'][] = array(
                    'game_name_cn' => $game['game_name_cn'],
                    'platform' => $game['platform'],
                    'gameCode' => $game['game_code'],
                    'img_file' => $game['platform'] . '/' . $game['game_code'] . '.jpg',
                    'game_code' => $game['game_code']
                );
            }
        }

        return retJson(0, $data);
    }


    /**
     * 实际注册逻辑
     * @param $sKeyword
     * @return mixed
     */
    public function register(Request $request)
    {
        // 验证
        $validator = Validator::make($request->all(), $this->rules_register, $this->messages_register);
        // 异常处理
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
                $message = $error . ',';
            }
            // 返回参数异常信息
            return retJson(2, [], $message);
        }

        if (!$request->input('checked')){
            return retJson(2, [], '请勾选确认用户协议!');
        }

        // 用户名 姓名 联系QQ 手机号 电子邮箱 密码 确认密码 邀请码 协议确认
        $data = $request->all();

        //判断是否有写邀请码
        if (null != $request->input('code') && $request->input('code') != '') {
            $oInvitationCode = InvitationCode::where('invitation_code', $data['code'])->first();
            if (!$oInvitationCode){
                return retJson(5, [], '邀请码不存在！');
            }

            $iRecommenderId = $oInvitationCode->user_id;
            $sRecommender = $oInvitationCode->username;
            $iParentId = $oInvitationCode->parent_id;
            $sParent = $oInvitationCode->parent_username;

            // 绑定该邀请码下的用户信息
            $aExtraData['parent_id'] = $iParentId;
            $aExtraData['parent'] = $sParent;
            $aExtraData['recommender'] = $sRecommender;
            $aExtraData['recommender_id'] = $iRecommenderId;
        }

        // 其他信息
        $aExtraData['is_agent'] = 0;
        $aExtraData['register_at'] = Carbon::now()->toDateTimeString();
        $aExtraData['signin_at'] = date('Y-m-d H:i:s');

        unset($data['code']);

        // 合并信息
        $data = array_merge($data, $aExtraData);
        // 开始写入事务
        DB::connection()->beginTransaction();

        // 用户类
        $oUser = new UserUser;
        // 生成新建用户的信息
        $aReturnMsg = $oUser->generateUserInfo($data);

        // 封装MODEL函数，若异常返回系统错误
        if(! $aReturnMsg['success']) {
            return retJson(4, array(), isset($aReturnMsg['msg']) ? $aReturnMsg['msg'] : '注册信息异常！');
        }

        $oUser->is_from_link = 0;
        $sError = "";
        // 用户开户 并进行数据层验证
        $bSucc = $this->createProcess($oUser, null, null, $sError);


        if ($bSucc) {
            // 信息格式验证成功
            $bSuccess = true;
            if ($request->input('code')) {
                // 取出'category' => Activity::CATEGORY_RECOMMEND的活动配置
                $oActivity = Activity::doWhere([
                    'category' => Activity::CATEGORY_RECOMMEND
                ])->first();

                if (!$oActivity) {
                    return retJson(6, [], '推荐礼金活动配置异常!');
                }
                // 取出ActivityRule中id=Activity.rule.id的值
                $oActivityRule = ActivityRule::find($oActivity->rule_id);

                if (!$oActivityRule) {
                    return retJson(7, [], '推荐礼金活动规则!');
                }

                $aJobData = [
                    'category' => Activity::CATEGORY_RECOMMEND,
                    'activity_id' => $oActivity->id,
                    'activity_name' => $oActivity->name,
                    'recommender' => $oUser->username,
                    'recommender_id' => $oUser->id,
                    'username' => $sRecommender,
                    'user_id' => $iRecommenderId,
                    'signup_at' => date('Y-m-d'),
                    'bonus' => $oActivityRule->bonus_max,
                    'multiple' => $oActivityRule->multiple,
                    'total_turnover' => $oActivityRule->bonus_max * $oActivityRule->multiple,
                    'status' => ActivityReport::STATUS_NORMAL,
                    'register_ip' => $oUser->register_ip,
                ];

                $oActivityReport = new ActivityReport();
                $oActivityReport = $oActivityReport->fill($aJobData);
                if (!$oActivityReport->save()) {
                    $bSuccess = false;
                    DB::connection()->rollback();
                    return retJson(4, [], '注册信息异常！');
                }
            }

            DB::connection()->commit();

            //注册时生成第三方平台帐号，但不返回值
            $sPassword = $data['password'];
            $redis = new \Redis();
            $redis->connect(\config('database.redis.default.host'),\config('database.redis.default.port'));
            $redis->lPush('CreatePlayer',json_encode(['user_id'=>$oUser->id, 'password'=>Encrypt::encode($sPassword)]));
            return retJson(0);
        } else {

            DB::connection()->rollback();
            // 添加失败,返回异常信息
            return retJson(8, array(), $sError);
        }
    }

    /**
     * 登录接口
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request){
        // 验证
        $validator = Validator::make($request->all(), $this->rules_login, $this->messages_register);
        // 异常处理
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
                $message = $error . ',';
            }
            // 返回参数异常信息
            return retJson(2, [], $message);
        }

        $sUsername = trim($request->input('username'));
        $sPassword = md5(md5(md5($sUsername . $request->input('password'))));

        // 查询账户密码是否存在
        $user = UserUser::where('username', '=', $sUsername)->first();

        // 账户不存在
        if (empty($user)) {
            return retJson(5, [], '账户不存在');
        }

        // 密码错误
        if (!Hash::check($sPassword, $user->password)){
            return retJson(6, [], '密码错误');
        }

        // 账户是否被锁
        if ($user->blocked == 1) {
            return retJson(7, [], '账户被锁定,无法登录');
        }

        // 登录
        $redisCache = $this->postSign($user);

        // 运行token授权机制
        $accessToken = new accessToken($redisCache['user_id'], $redisCache);

        if($accessToken->UserLogin()){
            $data = array(
                'token'=>$accessToken->getToken(),
                'nickname'=>$redisCache['username'],
                'ugames'=>'',
            );

            // 查询玩家最近玩过的九款游戏
            $_users = new _users($redisCache['user_id']);
            $uGamesObj = $_users->getUgames();

            $uGames = array();

            foreach ($uGamesObj as $key=>$datum) {
                $uGames[$key]['id'] = $datum->id;
                $uGames[$key]['date'] = $datum->date;
                $uGames[$key]['game_name_cn'] = $datum->game_name_cn;
                $uGames[$key]['game_name_en'] = $datum->game_name_en;
                $uGames[$key]['platform'] = $datum->platform;
            }

            $data['ugames'] = $uGames;

            return retJson(0, $data);
        };
    }

    /**
     * 忘记密码
     * @param Request $request
     */
    public function resetpassword(Request $request){

/*        $validator = Validator::make($request->all(), $this->rules_resetpassword, $this->messages_register);

        // 异常处理
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
                $message = $error . ',';
            }
            // 返回参数异常信息
            return retJson(2, [], $message);
        }

        // 调用PC端接口
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://www.qupaimobile.com',
            // You can set any number of default request options.
            'timeout'  => 5.0,
        ]);

        try {

            $response = $client->post('http://www.qupai888.com/auth/post-forgot-password', [
                'form_params' => [
                    'email'=>$request->input('email'),
                ]
            ]);
            $body = $response->getBody();
            echo $body;
        } catch (GuzzleException $e) {
            echo (string) $e->getResponse()->getBody();
            exit;
        }*/
    }

    /**
     * 登陆
     * @param $user 用户对象
     * @return mixed
     */
    private function postSign(&$user){
        $redisCache = array();

        if (! $user->is_from_link && ! $user->signin_at) {
            $redisCache['first_login'] = true;
        }
        //上次登陆时间
        if($user->signin_at){
            $redisCache['last_signin_at'] = $user->signin_at;
        }

        $user->signin_at = Carbon::now()->toDateTimeString();
        $user->login_ip = get_client_ip();
        DB::table('users')->where('id',$user->id)->update(['signin_at'=>$user->signin_at,'login_ip'=>$user->login_ip]);

        $roles = $this->_getUserRole($user);
        if (in_array(Role::DENY, $roles)){
            // 用户角色异常
            return retJson(8);
        }

        UserOnline::online($user->id);
        if($user->is_agent){
            $redisCache['show_overlimit'] = !empty($aOverLimits);
        }

        $redisCache['user_id'] = $user->id;
        $redisCache['username'] = $user->username;
        $redisCache['nickname'] = $user->nickname;
        $redisCache['language'] = $user->language;
        $redisCache['account_id'] = $user->account_id;
        $redisCache['forefather_ids'] = $user->forefather_ids;
        $redisCache['is_agent'] = $user->is_agent;
        $redisCache['is_top_agent'] = empty($user->parent_id);
        $redisCache['is_player'] = 0;
        $redisCache['CurUserRole'] = $roles;
        $redisCache['signin_at'] = $user->signin_at;

        if (!$user->first_deposit_at || !$user->first_deposit_amount) {
            $oTransaction = Transaction::doWhere([
                'user_id' => ['=', $user->id],
                'type_id' => ['in', TransactionType::$aDepositTypes],
            ])->first();
            if ($oTransaction) {
                $user->first_deposit_at = $oTransaction->created_at;
                $user->first_deposit_amount = $oTransaction->amount;
                $user->save();
            }
        }

        $redisCache['first_deposit_at'] = $user->first_deposit_at;
        $redisCache['first_deposit_amount'] = $user->first_deposit_amount;

        return $redisCache;
    }

    /**
     * 获取用户的角色信息
     * @param $oUser
     * @return array
     */
    private function _getUserRole($oUser) {
        $roles = $oUser->getRoleIds();

        $aDefaultRoles[] = Role::EVERY_USER;

        if ($oUser->is_agent){
            $aDefaultRoles[] = Role::AGENT;
            if (empty($oUser->parent_id)){
                $aDefaultRoles[] = Role::TOP_AGENT;
            }
        }
        else{
            $aDefaultRoles[] = Role::PLAYER;
        }
        $roles = array_merge($roles , $aDefaultRoles);
        $roles = array_unique($roles);
        $roles = array_map(function($value){
            return (int)$value;
        }, $roles);

        return $roles;
    }

    /**
     * [createProcess 开户流程]
     * @param  [Object] $oUser       [用户对象]
     * @param  [Array]  $aPrizeGroup [奖金组数据]
     * @param  [Object] $oPrizeGroup [开户链接对象]
     * @param  [String] $sPrizeGroupCode [链接开户特征码]
     * @return [Boolean]             [开户成功/失败]
     */
    private function createProcess($oUser, $oUserRegisterLink, $sCode, &$sError)
    {
        $bSucc = false;
        if ($bSucc = $oUser->save()) {
            $oAccount = $oUser->generateAccountInfo();
            if ($bSucc = $oAccount->save()) {
                $oUser->account_id = $oAccount->id;
                if ($bSucc = $oUser->save()) {
                    $bSucc = true;

                }else {
                    $sError = $oUser->getValidationErrorString();
                }
                // 只有链接开户时需要增加链接的开户数以及关联开户用户
                if ($sCode && $bSucc) {
                    $oUserRegisterLink->increment('created_count');

                    if ($oUserRegisterLink->is_admin && $oUserRegisterLink->created_count == 0) {
                        $oUserRegisterLink->increment('status');
                    }

                    $oUserRegisterLink->users()->attach($oUser->id, ['url' => $oUserRegisterLink->url, 'username' => $oUser->username]);
                }

            }else $sError = $oAccount->getValidationErrorString();
        }else{
            $sError = $oUser->getValidationErrorString();
        }

        return $bSucc;
    }

}
