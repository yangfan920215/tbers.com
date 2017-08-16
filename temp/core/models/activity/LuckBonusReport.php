<?php

class LuckBonusReport extends ActivityReport {

    public static $resourceName = 'LuckBonusReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_agent',
        'activity_name',
        'date_range',
        'amount',
        'wallet',
        'bonus',
        'finished_turnover',
        'login_ip',
        'register_ip',
        'signup_at',
        'receive_at',
        'unlock_at'
    ];
}
