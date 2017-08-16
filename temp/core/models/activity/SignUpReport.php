<?php

class SignUpReport extends ActivityReport {

    public static $resourceName = 'SignUpReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'recommender',
        'activity_name',
        'wallet',
        'bonus',
        'finished_turnover',
        'login_ip',
        'register_ip',
        'signup_at',
        'receive_at',
        'first_deposit_at',
        'first_deposit_amount',
        'unlock_at'
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'receive_at' => 'desc',
    ];
}
