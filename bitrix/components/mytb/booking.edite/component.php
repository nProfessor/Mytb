<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

global $USER;
$arUserlist = array();

if (!isset($_GET["date"])) {
    $date = date('d.m.Y');
} else {
    $date = $_GET["date"];
}



$club = Club::getOBonTheUserID($USER::GetId());


$clubInfo = $club->getInfo(array(
    "arSelect" => Array(
        "ID",
        "ACTIVE_FROM",
        "PROPERTY_PLAN"

    )
));


$arResult['clubInfo']=$clubInfo;

$APPLICATION->SetTitle(html_entity_decode($arField['NAME']));
$arResult["date"] = $date;

$this->IncludeComponentTemplate();