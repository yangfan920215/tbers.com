<?php

class PtBetLog extends BaseModel {

    public static $resourceName = 'PtBetLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'created_at',
        'RNum',
        'PlayerName',
        'WindowCode',
        'GameId',
        'GameCode',
        'GameType',
        'GameName',
        'SessionId',
        'Bet',
        'Win',
        'ProgressiveBet',
        'ProgressiveWin',
        'Balance',
        'CurrentBet',
        'GameDate',
        'LiveNetwork',
        'status_calculate_user_bet_record',
        'status_calculate_user_bet_report',
        'status_calculate_withdrawable',
        'status_check_activity',
    ];

    protected $table = 'pt_bet_logs';


    protected $fillable = [
        'id',
        'Rnum',
        'PlayerName',
        'WindowCode',
        'GameId',
        'GameCode',
        'GameType',
        'GameName',
        'SessionId',
        'Bet',
        'Win',
        'ProgressiveBet',
        'ProgressiveWin',
        'Balance',
        'CurrentBet',
        'GameDate',
        'LiveNetwork',
        'RNum',
        'created_at',
        'updated_at',
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
