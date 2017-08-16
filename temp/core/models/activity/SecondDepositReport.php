<?php

class SecondDepositReport extends ActivityReport {

    public static $resourceName = 'SecondDepositReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_agent',
        'activity_name',
        'amount',
        'bonus',
        'finished_turnover',
        'login_ip',
        'register_ip',
        'signup_at',
        'receive_at',
        'unlock_at'
    ];
}
