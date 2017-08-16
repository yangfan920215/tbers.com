<?php

class GiftVoucherReport extends ActivityReport {

    public static $resourceName = 'GiftVoucherReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_agent',
        'code',
        'activity_name',
        'wallet',
        'amount',
        'bonus',
        'finished_turnover',
        'login_ip',
        'register_ip',
        'signup_at',
        'receive_at',
        'unlock_at',
    ];

    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'type' => 'aTypes',
        'wallet' => 'aWallets'
    ];
}
