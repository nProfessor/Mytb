<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");


global $USER;

$userRes=new User($USER::GetID());
$userInfo=$userRes->getInfo();
$user_props=$userRes->getProps(array("PROPERTY_CLUB"));
$clubID = intval($user_props['PROPERTY_CLUB_VALUE']);


$club = new Club($clubID);

$arFields=$club->getInfo(array("arSelect"=> array(
    "ID",
    "DETAIL_TEXT",
    "NAME",
    "DATE_ACTIVE_FROM",
    "PROPERTY_ADDRESS",
    "PROPERTY_TIME_WORKING",
    "PROPERTY_PRICE_COCKTAIL",
    "PROPERTY_PHONE",
    "PROPERTY_RATING",
    "PROPERTY_MUSIC",
    "PROPERTY_FACE",
    "PROPERTY_DRESS_CODE",
    "PROPERTY_SITE",
    "PROPERTY_METRO",
    "PROPERTY_PLAN",
    "PREVIEW_PICTURE",
    "PROPERTY_TYPE_FACILITY"
)),true);





$arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);

$arFields["PREVIEW_PICTURE"]=imgurl($arFile["SRC"], array("w" => 200));
$arResult['arFields'] = $arFields;
$arResult['subs']['stock'] = $club->getCountSubStocks();
$arResult['subs']['event'] = $club->getCountSubEvent();



$this->IncludeComponentTemplate();


