<?php

class Jackpot extends BaseModel {

    public static $resourceName = 'Jackpot';

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'game_name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'jackpot_code',
        'platform',
        'amount',
        'is_display',
    ];

    /**
     * the main param for index page
     * @var string
     */
//    public static $mainParamColumn = 'parent_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'jackpot_code' => 'required|max:50',
        'platform' => 'required|max:50',
        'amount' => '',
        'is_display' => '',
    ];
    protected $table = 'jackpots';

    protected $fillable = [
        'id',
        'jackpot_code',
        'platform',
        'amount',
        'is_display',
    ];
}
