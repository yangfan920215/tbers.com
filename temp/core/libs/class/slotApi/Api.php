<?php

/**
 * Created by PhpStorm.
 * User: endless
 * Date: 16-11-15
 * Time: 下午4:33
 */
Interface Api
{
    function createPlayer($sUsername, $sPassword);
//    function checkPlayerExists();
    function getBalance($sLoginName, $sPassword);
    function resetPassword($sUsername, $sNewPassword, $sOldPassword = '');
//    function killSession();
//    function authenticatePlayer();
//    function checkPlayerToken();
    function transaction($sLoginName, $sPassword, $sBillNo, $iType, $fCredit);
//    function checkTransaction();
//    function getBetLog();
    function launchGame($sUsername, $sPassword, $sGameCode);
    function launchFreeGame($sUsername, $sPassword, $sGameCode);
}