<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserPromoCode extends BaseModel{

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_promo_codes';
    protected $softDelete = true;
    protected $fillable = [
        'id',
        'is_admin',
        'is_tester',
        'user_id',
        'username',
        'is_agent',
        'promo_code',
        'created_count',
        'url',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserPromoCode';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'is_admin',
        'is_tester',
        'user_id',
        'username',
         'is_agent',
        'account_available',
         'promo_code',
        'created_count',
        'url',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [

    ];

    public static function getPromoCode(){
        return substr(md5(Session::get('username') . time() . random_str(5)), 0, 8);
    }
}
