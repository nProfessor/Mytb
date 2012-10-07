<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");


global $USER;
$arGroupPrice = array();

$Club = Club::getOBonTheUserID($USER->GetID());

$arField = $Club->getInfo(array(
    "arSelect" => array("ID", "PROPERTY_PLAN","NAME"),
    "arFilter" => array("ACTIVE" => "Y"),
));


$obTableList = $Club->getResTableList(array(
    "arSelect" => array("ID", "NAME", "ACTIVE", "PROPERTY_PRICE_GROUP", "PREVIEW_PICTURE", "PROPERTY_COUNT", "PROPERTY_PRICE_GROUP"),
    "arFilter" => array("ACTIVE" => "Y"),
));


$arTableList = array();
while ($ob = $obTableList->Fetch()) {
    $arFile = CFile::GetFileArray($ob["PREVIEW_PICTURE"]);
    $ob["PREVIEW_PICTURE"] = empty($arFile["SRC"]) ? "/img/160x120.gif" : $arFile["SRC"];
    $arTableList[] = $ob;
}


$PriceGroupList = $Club->getPriceGroupList(array(
    "arSelect" => array("ID", "ACTIVE", "PROPERTY_PRICE", "PREVIEW_CLUB"),
));

while ($ob = $PriceGroupList->Fetch()) {
    $arGroupPrice[$ob["ID"]] = $ob["PROPERTY_PRICE_VALUE"];
}


$arResult['arFields'] = $arField;
$arResult['arTableList'] = $arTableList;
$arResult['arGroupPrice'] = $arGroupPrice;


$this->IncludeComponentTemplate();












