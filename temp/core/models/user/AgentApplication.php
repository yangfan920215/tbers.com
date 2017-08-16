<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class AgentApplication extends BaseModel {
    const STATUS_NEW = 0;
    const STATUS_CREATED = 1;
    const STATUS_OBSOLETE = 2;
    public static $aStatus = [
        self::STATUS_NEW => 'normal',
        self::STATUS_CREATED => 'created',
        self::STATUS_OBSOLETE => 'obsoleted'
    ];

    public static $rules = [
        'expected_username'                      => 'required|regex:/^[a-zA-Z0-9]{6,16}$/|unique:agent_applications,expected_username,',
        'name'                      => 'required',
        'phone'                           => 'required|regex:/^1\d{10}$/|unique:agent_applications,phone,',
        'qq'                           => 'required|regex:/^\d{6,}$/|unique:agent_applications,qq,',
        'email'                         => 'required|email|between:0, 50', // |unique:users,email,
    ];

    public static $customMessages = [
        'expected_username.required' => '请填写用户名',
        'expected_username.alpha_num' => '用户名只能由大小写字母和数字组成',
        'expected_username.between' => '用户名长度有误，请输入 :min - :max 位字符',
        'expected_username.unique' => '用户名已被注册',
        'expected_username.custom_first_character' => '首字符必须是英文字母',
        // 'email.required'                  => '请填写邮箱地址',
    ];

    public $orderColumns = [
        'status' => 'asc'
    ];
    public static $htmlSelectColumns = [
        'status' => 'aStatus',
    ];
    protected $table = 'agent_applications';
    protected $fillable = [
        'name',
        'phone',
        'qq',
        'email',
        'expected_username',
        'url',
        'channel',
        'status',
        'admin',
        'created_at',
        'updated_at',
        'note',
    ];

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'AgentApplication';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'phone',
        'qq',
        'email',
        'expected_username',
        'url',
        'channel',
        'status',
        'admin',
        'created_at',
        'note',
    ];

    public static $ignoreColumnsInView = [
        'updated_at',
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
    ];

    protected function getFormattedStatusAttribute() {
        return __('_agentapplication.' . strtolower(Str::slug(static::$aStatus[$this->attributes['status']])));
    }

    protected function beforeValidate() {
        if ($this->id) {
            // self::$rules['username'] .= $this->id; // str_replace('{:id}', $this->id, self::$rules['username'] );
            self::$rules['expected_username'] = 'required|alpha_num|between:6,16|unique:agent_applications,expected_username,' . $this->id;
            self::$rules['qq'] = 'required|between:6,16|unique:agent_applications,qq,' . $this->id;
            self::$rules['phone'] = 'required|regex:/^1\d{10}$/|unique:agent_applications,phone,' . $this->id;
            // self::$rules['email']    = 'email|between:0, 50|unique:users,email,' . $this->id;
        }
        return parent::beforeValidate();
    }
}
