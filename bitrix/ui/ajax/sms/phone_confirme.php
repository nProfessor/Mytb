<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 22.09.12
 * Time: 17:26
 * To change this template use File | Settings | File Templates.
 */


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

global $USER;


$code = strip_tags($_POST['code']);

$obUser=new User($USER::GetID());

if($obUser->checkVerificationCode($code)){
    $obUser->saveMobilePhone();
    die(json_encode(array("status"=>"ok")));
}else{
    die(json_encode(array("status"=>"errors")));
}




