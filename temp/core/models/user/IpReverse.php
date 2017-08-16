<?php
class IpReverse extends User {
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'IpReverse';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'parent',
        'register_ip',
        'login_ip',
        'register_at',
        'signin_at',
        'deposit',
        'withdraw',
    ];

    protected function getDepositAttribute(){
        return Transaction::doWhere([
            'user_id' => ['=', $this->attributes['id']],
            'type_id' => ['in', TransactionType::$aDepositTypes],
        ])->sum('amount');
    }

    protected function getWithdrawAttribute(){
        return Transaction::doWhere([
            'user_id' => ['=', $this->attributes['id']],
            'type_id' => ['in', TransactionType::$aWithdrawTypes],
        ])->sum('amount');
    }
}
