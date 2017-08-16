<?php

class HbBetLog extends BaseModel {

    public static $resourceName = 'HbBetLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'created_at',
        'PlayerId',
        'BrandId',
        'Username',
        'BrandGameId',
        'GameKeyName',
        'GameTypeId',
        'DtStarted',
        'DtCompleted',
        'FriendlyGameInstanceId',
        'GameInstanceId',
        'Stake',
        'Payout',
        'JackpotWin',
        'JackpotContribution',
        'CurrencyCode',
        'ChannelTypeId',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity',
    ];

    protected $table = 'hb_bet_logs';


    protected $fillable = [
        'id',
        'PlayerId',
        'BrandId',
        'Username',
        'BrandGameId',
        'GameKeyName',
        'GameTypeId',
        'DtStarted',
        'DtCompleted',
        'FriendlyGameInstanceId',
        'GameInstanceId',
        'Stake',
        'Payout',
        'JackpotWin',
        'JackpotContribution',
        'CurrencyCode',
        'ChannelTypeId',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity',
        'created_at',
        'updated_at',
    ];

    public $orderColumns = [
        'created_at' => 'desc'
    ];
}
