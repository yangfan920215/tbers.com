<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserContact extends User {
    protected $fillable = [
        'account_id',
        'username',
        'pt_username',
        'im_username',
        'ag_fishing_username',
        'nickname',
        'phone',
        'email',
        'qq',
        'birthday',
        'parent_id',
        'parent',
        'forefather_ids',
        'forefathers',
        'blocked',
        'activated_at',
        'signin_at',
        'register_at',
        'is_agent',
        'is_tester',
        'fund_password',
        'fund_password_confirmation',
        'password',
        'pt_password',
        'im_password',
        'ag_fishing_password',
        'password_confirmation',
        'register_ip',
        'login_ip',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserContact';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'nickname',
        'phone',
        'email',
        'qq'
    ];

    public static $ignoreColumnsInView = [
        'id',
        'role_ids',
        'password',
        'fund_password',
        'remember_token',
        'account_id',
        'parent',
        'role_id',
        'blocked',
        'parent_str',
        'is_agent',
        'is_from_link',
        'is_tester',
        'user_level',
        'login_ip',
        'register_ip',
        'signin_at',
        'activated_at',
        'register_at',
        'deleted_at',
        'created_at',
        'updated_at',
        'pt_username',
        'im_username',
        'ag_fishing_username',
        'pt_password',
        'im_password',
        'ag_fishing_password',
        'recommender',
        'first_deposit_at',
        'first_deposit_amount',
        'ag_username',
        'ag_password',
        'hb_username',
        'hb_password',
        'mg_username',
        'mg_password',
        'mg_user_id',
        'birthday',
        'recommender_id',
    ];

    public function getPhoneAttribute(){
        $oUserContactVali = UserContactVali::getObjectByParams(['user_id'=>$this->id, 'contact_type' => UserContactVali::TYPE_PHONE]);
        $sPhone = $this->attributes['phone'];
        if (!$oUserContactVali) return $sPhone;
        if ($oUserContactVali->status == UserContactVali::STATUS_REAL) {
            $sPhone = preg_replace('/^(\d{3})(\d+)(\d{4})$/', '$1***$3', $sPhone);
        }
        return $sPhone;
    }

    public function getEmailAttribute(){
        $oUserContactVali = UserContactVali::getObjectByParams(['user_id'=>$this->id, 'contact_type' => UserContactVali::TYPE_EMAIL]);
        $sEmail = $this->attributes['email'];
        if (!$oUserContactVali) return $sEmail;
        if ($oUserContactVali->status == UserContactVali::STATUS_REAL) {
            $sEmail = preg_replace('/^([\S\s]{2})(.*)([\S\s]{2})(@.*)$/', '$1***$3$4', $sEmail);
        }
        return $sEmail;
    }

    public function getQqAttribute(){
        $oUserContactVali = UserContactVali::getObjectByParams(['user_id'=>$this->id, 'contact_type' => UserContactVali::TYPE_QQ]);
        $sQq = $this->attributes['qq'];
        if (!$oUserContactVali) return $sQq;
        if ($oUserContactVali->status == UserContactVali::STATUS_REAL) {
            $sQq = preg_replace('/^([\S\s]{2})(.*)([\S\s]{2})$/', '$1***$3', $sQq);
        }
        return $sQq;
    }
}
