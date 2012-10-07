<?php
/**
 * User:        Олег
 * Data:        14.06.12 22:59
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

global $USER;
$tableID=(int)$_GET["tableID"];

$Club=Club::getOBonTheUserID($USER->GetID());

$table = $Club->table()->getInfo($tableID,array(
    "arSelect"=>array("NAME", "ACTIVE", "PROPERTY_PRICE_GROUP", "PREVIEW_PICTURE", "PROPERTY_COUNT", "PROPERTY_PRICE_GROUP")
));




echo json_encode($table);

