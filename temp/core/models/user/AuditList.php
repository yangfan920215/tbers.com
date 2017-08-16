<?php

class AuditList extends BaseModel {
    const STATUS_IN_AUDITING = 0;
    const STATUS_AUDITED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CANCELED = 3;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'audit_lists';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'AuditList';

    public static $columnForList = [
        'type_id',
        'username',
        'admin_name',
        'auditor_name',
        'description',
        'status',
        'updated_at',
    ];

    public static $htmlSelectColumns = [
        'type_id' => 'aAuditTypes',
        'status' => 'aStatus', // 0:审核中, 1: 审核通过, 2: 审核拒绝, 3: 撤销密码重置
    ];

    public static $statusDesc = [
        self::STATUS_IN_AUDITING => 'in-auditting',
        self::STATUS_AUDITED     => 'audited',
        self::STATUS_REJECTED    => 'rejected',
        self::STATUS_CANCELED    => 'canceled'
    ];

    protected $fillable = [
        'id',
        'type_id',
        'user_id',
        'admin_id',
        'auditor_id',
        'username',
        'admin_name',
        'auditor_name',
        'audit_data',
        'description',
        'status',
    ];
    public static $rules = [
        'type_id'       => 'required|integer',
        'user_id'       => 'required|integer',
        'admin_id'      => 'required|integer',
        'auditor_id'    => 'integer',
        'username'     => 'required|between:0,16',
        'admin_name'    => 'required|between:0,16',
        'auditor_name'  => 'between:0,16',
        'audit_data'    => 'required',
        'description'   => 'between:0,255',
        'status'        => 'integer|in:0,1,2,3',
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];

    /**
     * [explodeParams 解析审核数据]
     * @param  [Object] $filledModel [待填充数据的审核Model对象]
     * @return [Object]              [填充完毕的数据的审核Model对象]
     */
    public function explodeParams(& $filledModel)
    {
        if (! $this->audit_data) return false;
        $aParam = explode(',', $this->audit_data);
        foreach ($aParam as $key => $value) {
            $aItem = explode('=', $value);
            $filledModel->{$aItem[0]} = $aItem[1];
        }
        return true;
    }
}