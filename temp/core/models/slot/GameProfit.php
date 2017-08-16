<?php

class GameProfit extends BaseModel {

    public static $resourceName = 'GameProfit';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'date',
        'game_name_en',
        'game_name_cn',
        'game_code',
        'platform',
        'bet',
        'win',
        'profit',
//        'rtp',
        'progressive_bet',
        'progressive_win',
    ];

    protected $table = 'game_profits';


    protected $fillable = [
        'id',
        'date',
        'game_name_en',
        'game_name_cn',
        'game_code',
        'platform',
        'bet',
        'win',
        'profit',
        'rtp',
        'progressive_bet',
        'progressive_win',
        'created_at',
        'updated_at',
    ];

    public $orderColumns = [
        'date' => 'desc'
    ];
}
