<?php

class AgTransfer extends BaseModel {

    public static $resourceName = 'AgTransfer';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'ID',
        'dataType',
        'agentCode',
        'transferId',
        'tradeNo',
        'platformType',
        'playerName',
        'transferType',
        'transferAmount',
        'previousAmount',
        'currentAmount',
        'currency',
        'exchangeRate',
        'IP',
        'flag',
        'creationTime',
        'remark',
        'gameCode',
    ];

    protected $table = 'ag_transfers';


    protected $fillable = [
        'ID',
        'dataType',
        'agentCode',
        'transferId',
        'tradeNo',
        'platformType',
        'playerName',
        'transferType',
        'transferAmount',
        'previousAmount',
        'currentAmount',
        'currency',
        'exchangeRate',
        'IP',
        'flag',
        'creationTime',
        'remark',
        'gameCode',
    ];
}
