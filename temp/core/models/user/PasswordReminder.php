<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class PasswordReminder extends BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'password_reminders';
    protected $fillable = [
        'id',
        'type',
        'email',
        'token',
        'created_at',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'PasswordReminder';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'type',
        'email',
        'token',
        'created_at',
    ];

}
