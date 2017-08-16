<?php

class BetLog extends BaseModel {

    public static $resourceName = 'BetLog';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'RNum',
        'PlayerName',
//        'window_code',
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
//        'live_network'
    ];

    protected $table = 'bet_logs';


    protected $fillable = [
        'id',
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
    ];
    public $orderColumns = [
        'date' => 'desc'
    ];
}
