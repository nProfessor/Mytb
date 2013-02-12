<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 13.01.13 15:21
 * File name: subscribe.php
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

global $USER;
$CLUB_ID=intval($_POST["CLUB_ID"]);

if ($USER->IsAuthorized()) {
    $obUser = new User($USER::GetID());
    $obUser->setSubscribe($CLUB_ID, array("LINK_STOK"));

    global $USER;
    $user = new User($arParams["USER_ID"]);

//достаем информуци.  у пользователя
    $rs = $user->getProps(array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_EVENT"));
    die(json_encode(array("status"=>"ok","result"=>$rs)));

}

die(json_encode(array("status"=>"error")));