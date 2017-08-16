<?php

/**
 * 方案模型
 */
class PlatformAccount extends BaseModel {
    
    public static $resourceName = 'PlatformAccount';
    protected $table = 'platform_accounts';

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
    ];

    public function createUser($iPlatformId, $aUserData){
        
    }
    
    public function checkUserExists($iPlatformId, $sUsername){
        
    }
    
    public function getBalance($iPlatformId, $iUserId){
        
    }
    
    public function getInfoByUsername($iPlatformId, $sUsername){
        
    }
    
    public function getInfo($iPlatformId, $iUserId){
        
    }
    
    public function resetPassword($iPlatformId, $iUserId, $sNewPassword){
        
    }
    
    public function login($iPlatformId, $sUsername, $sPassword){
        
    }
    
//    public function checkIsLogin($iPlatformId, $sUsername){
//        
//    }
    
    public function deposit($iPlatformId, $iUserId, $fAmount){
        
    }
    
    public function withdraw($iPlatformId, $iUserId, $fAmount){
        
    }
    
    public function killSession($iPlatformId, $iUserId){
        
    }
}
