<?php
//$driver = 'beanstalkd';
//$host = 'localhost';
$driver = 'redis';
$host = '127.0.0.1:6379';
$ttr = 60;
return array(
    'default'     => 'main',
    /*
      |--------------------------------------------------------------------------
      | Queue Connections
      |--------------------------------------------------------------------------
      |
      | Here you may configure the connection information for each server that
      | is used by your application. A default configuration has been added
      | for each back-end shipped with Laravel. You are free to add more.
      |
     */
    'connections' => array(
        'sync'               => array(
            'driver' => 'sync',
        ),
        'main'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'main',
            'ttr'    => $ttr,
        ),
        'recommend_activity'         => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'recommend_activity',
            'ttr'    => $ttr,
        ),
        'account'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'account',
            'ttr'    => $ttr,
        ),
        'withdraw'           => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'withdraw',
            'ttr'    => $ttr,
        ),
        'stat'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'stat',
            'ttr'    => $ttr,
        ),
        'CheckSecurityCode'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckSecurityCode',
            'ttr'    => $ttr,
        ),
        'SetWithdrawable'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'SetWithdrawable',
            'ttr'    => $ttr,
        ),
        'GenerateDayCommissionForUserBetRecord'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'GenerateDayCommissionForUserBetRecord',
            'ttr'    => $ttr,
        ),
        'GenerateBonusForUserBetRecord'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'GenerateBonusForUserBetRecord',
            'ttr'    => $ttr,
        ),
        'CheckPtActivityStatus'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckPtActivityStatus',
            'ttr'    => $ttr,
        ),
        'CheckImActivityStatus'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckImActivityStatus',
            'ttr'    => $ttr,
        ),
        'CheckAgActivityStatus'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckAgActivityStatus',
            'ttr'    => $ttr,
        ),
        'CheckHbActivityStatus'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckHbActivityStatus',
            'ttr'    => $ttr,
        ),
        'CheckMgActivityStatus'               => array(
            'driver' => $driver,
            'host'   => $host,
            'queue'  => 'CheckMgActivityStatus',
            'ttr'    => $ttr,
        ),

    ),
    /*
      |--------------------------------------------------------------------------
      | Failed Queue Jobs
      |--------------------------------------------------------------------------
      |
      | These options configure the behavior of failed queue job logging so you
      | can control which database and table are used to store the jobs that
      | have failed. You may change them to any database / table you wish.
      |
     */
    'failed'      => array(
        'database' => 'mysql','table'    => 'failed_jobs',
    ),
);
