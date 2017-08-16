<?php

/**
 * 方案模型
 */
class Platform extends BaseModel {
    public static $resourceName = 'Platform';
    protected $table = 'platforms';
    public static $allPlatforms = ['ag','hb','mg','pt','im', 'ttg', 'other', 'yoplay'];
    public static $aPlatforms = ['ag','hb','mg','pt','im'];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    protected $fillable = [
    ];
    
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => '',
        'url' => '',
        'status' => '',
    ];

}
