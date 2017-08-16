<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use InvitationCode;
use UserUser;
use Activity;
use ActivityRule;
use ActivityReport;
use Role;
use UserOnline;
use Transaction;
use TransactionType;
use UserBetReport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use Validator;
use Carbon\Carbon;
use libs\Users as _users;
use DB;
use libs\Encrypt;
use libs\accessToken;
use libs\Games;

class UserController extends Controller
{




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

                }else $sError = $oUser->getValidationErrorString();

                // 只有链接开户时需要增加链接的开户数以及关联开户用户
                if ($sCode && $bSucc) {
                    $oUserRegisterLink->increment('created_count');

                    if ($oUserRegisterLink->is_admin && $oUserRegisterLink->created_count == 0) {
                        $oUserRegisterLink->increment('status');
                    }

                    $oUserRegisterLink->users()->attach($oUser->id, ['url' => $oUserRegisterLink->url, 'username' => $oUser->username]);
                }

            }else $sError = $oAccount->getValidationErrorString();
        }else $sError = $oUser->getValidationErrorString();
        return $bSucc;
    }

}
