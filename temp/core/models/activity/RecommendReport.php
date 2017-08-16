<?php

class RecommendReport extends ActivityReport {

    public static $resourceName = 'RecommendReport';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'recommender',
        'username',
        'signup_at',
        'first_deposit_at',
        'activity_name',
        'wallet',
        'first_deposit_amount',
        'deposit',
        'bonus',
        'finished_turnover',
        'status',
        'receive_at',
        'unlock_at'
    ];
}
