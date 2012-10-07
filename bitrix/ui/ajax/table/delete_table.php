<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$tableID=(int)$_POST["tableID"];

global $USER;
$userID=$USER->GetID();

$Club=Club::getOBonTheUserID($USER->GetID());
$table = $Club->table()->getInfo($tableID);


// Установим новое значение для данного свойства данного элемента
$el = new CIBlockElement;

$arLoadProductArray = Array(
    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
    "ACTIVE" => "N",
);

$res = $el->Update($table["ID"], $arLoadProductArray);

?>