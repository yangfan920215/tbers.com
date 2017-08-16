<?php

class MgBetLog extends BaseModel {

    public static $resourceName = 'MgBetLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'created_at',
        'key',
        'colId',
        'agentId',
        'mbrid',
        'mbrCode',
        'transId',
        'gameId',
        'transType',
        'transTime',
        'mgsGameId',
        'mgsActionId',
        'amnt',
        'clrngAmnt',
        'balance',
        'refTransId',
        'refTransType',
        'updated_at',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity'
    ];

    protected $table = 'mg_bet_logs';


    protected $fillable = [
        'id',
        'key',
        'colId',
        'agentId',
        'mbrid',
        'mbrCode',
        'transId',
        'gameId',
        'transType',
        'transTime',
        'mgsGameId',
        'mgsActionId',
        'amnt',
        'clrngAmnt',
        'balance',
        'refTransId',
        'refTransType',
        'created_at',
        'updated_at',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity'
    ];

    public $orderColumns = [
        'created_at' => 'desc'
    ];
}
