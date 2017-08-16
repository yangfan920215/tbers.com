<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserContactVali extends BaseModel {
    const TYPE_PHONE = 1;
    const TYPE_EMAIL = 2;
    const TYPE_QQ = 3;

    public static $aContactTypes = [
        self::TYPE_PHONE => '电话',
        self::TYPE_EMAIL => '邮件',
        self::TYPE_QQ => 'QQ',
    ];

    const STATUS_NORMAL = 0;
    const STATUS_FALSE = 1;
    const STATUS_REAL = 2;

    public static $aStatus = [
        self::STATUS_NORMAL => '未验证',
        self::STATUS_FALSE => '虚假',
        self::STATUS_REAL => '真实'
    ];

    public static $htmlSelectColumns = [
        'contact_type' => 'aContactTypes',
        'status' => 'aStatus',
    ];

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'user_contact_valis';
    protected $fillable = [
        'id',
        'username',
        'contact_type',
        'status',
        'contact',
        'user_id',
        'admin',
        'admin_id',
        'created_at',
        'updated_at',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserContactValis';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'username',
        'contact_type',
        'status',
        'contact',
        'admin',
    ];

    public static function isRealContact($iContactType, $iUserId){
        $aConditions = [
            'contact_type' => $iContactType,
            'user_id' => $iUserId,
            'status' => self::STATUS_REAL,
        ];
        return self::doWhere($aConditions)->count();
    }
}
