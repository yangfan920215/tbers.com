<?php

class InvitationCode extends BaseModel {

    public static $resourceName = 'InvitationCode';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'invitation_code',
        'user_id',
        'username',
        'parent_id',
        'parent_username',
        'is_tester',
        'created_at',
        'updated_at',
    ];

    public static $ignoreColumnsInView = [

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

    ];
    protected $table = 'invitation_codes';
    public static $htmlSelectColumns = [
//        'type' => 'aType',
//        'status' => 'aStatus',
    ];



    protected $fillable = [
        'id',
        'invitation_code',
        'user_id',
        'username',
        'parent_id',
        'parent_username',
        'is_tester',
        'created_at',
        'updated_at',
    ];

}
