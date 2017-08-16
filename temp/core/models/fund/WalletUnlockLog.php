<?php

class WalletUnlockLog extends BaseModel {
    public static $resourceName = 'WalletUnlockLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'admin',
        'wallet',
        'note',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'account_id',
        'admin',
        'wallet',
        'note',
        'created_at',
        'updated_at',
    ];
}
