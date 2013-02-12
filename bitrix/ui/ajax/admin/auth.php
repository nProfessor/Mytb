<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 12.02.13 0:23
 * File name: auth.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
$userID=$USER::GetID();
if (in_array(GROUP_CONTEN,$USER->GetUserGroupArray())) {

    $clubID=intval($_POST['clubID']);

        $userRes=new User($userID);
        die(json_encode(array($userRes->setClub($clubID),"club"=>$clubID)));
}

if(isset($_SESSION["ADMIN"])&&intval($_SESSION["ADMIN"])>0){
    $USER->Authorize($_SESSION["ADMIN"]);
}