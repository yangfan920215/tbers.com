<?php

class AgFishingBonusReport extends ActivityReport {

    public static $resourceName = 'AgFishingBonusReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_agent',
        'date_range',
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
