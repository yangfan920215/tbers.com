<?php

class AgBetLog extends BaseModel {

    public static $resourceName = 'AgBetLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'created_at',
        'dataType',
        'deviceType',
        'ID',
        'tradeNo',
        'platformType',
        'sceneId',
        'playerName',
        'type',
        'SceneStartTime',
        'SceneEndTime',
        'Roomid',
        'Roombet',
        'Cost',
        'Earn',
        'Jackpotcomm',
        'transferAmount',
        'previousAmount',
        'currentAmount',
        'currency',
        'exchangeRate',
        'IP',
        'flag',
        'creationTime',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity',
    ];

    protected $table = 'ag_bet_logs';


    protected $fillable = [
        'dataType',
        'deviceType',
        'ID',
        'tradeNo',
        'platformType',
        'sceneId',
        'playerName',
        'type',
        'SceneStartTime',
        'SceneEndTime',
        'Roomid',
        'Roombet',
        'Cost',
        'Earn',
        'Jackpotcomm',
        'transferAmount',
        'previousAmount',
        'currentAmount',
        'currency',
        'exchangeRate',
        'IP',
        'flag',
        'creationTime',
        'gameCode',
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
