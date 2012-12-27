<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 20.12.12 18:54
 * File name: getinfo.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");


$club_id = intval($_POST["club_id"]);

$clubRes = new Club($club_id);
$clubInfo = $clubRes->getInfo(array("arSelect"=>array(
    "NAME",
    "PREVIEW_PICTURE",
    "PROPERTY_SITE",
    "PROPERTY_EMAIL_MANAGER",
    "PROPERTY_AVERAGE_CHECK",
    "PROPERTY_TIME_WORKING",
    "PROPERTY_KIND_CLUB",
    "PROPERTY_MUSIC",
    "PROPERTY_DESCR",

)), true);


$arFile = CFile::GetFileArray($clubInfo["PREVIEW_PICTURE"]);
$clubInfo["PREVIEW_PICTURE"]=$arFile["SRC"];






die(json_encode(array("status" => "ok", "result" => array(
    "ID"=>$clubInfo['ID'],
    "NAME" => $clubInfo['NAME'],
    "PREVIEW_PICTURE" => $clubInfo['PREVIEW_PICTURE'],
    "SITE" => $clubInfo['PROPERTY_SITE_VALUE'],
    "EMAIL_MANAGER" => $clubInfo['PROPERTY_EMAIL_MANAGER_VALUE'],
    "AVERAGE_CHECK" => $clubInfo['PROPERTY_AVERAGE_CHECK_VALUE'],
    "TIME_WORKING" => $clubInfo['PROPERTY_TIME_WORKING_VALUE'],
    "KIND_CLUB" => (array)$clubInfo['PROPERTY_KIND_CLUB_VALUE'],
    "MUSIC" => (array)$clubInfo['PROPERTY_MUSIC_VALUE'],
    "DESCR" => $clubInfo['PROPERTY_DESCR_VALUE'],
    "ADDRESS" => $clubRes->getAddress(),
))));