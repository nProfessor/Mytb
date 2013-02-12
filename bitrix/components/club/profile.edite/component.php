<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");



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
    "PROPERTY_EMAIL_MANAGER",
    "PROPERTY_PRICE_COCKTAIL",
    "PROPERTY_AVERAGE_CHECK",
    "PREVIEW_PICTURE",
    "PROPERTY_TYPE_FACILITY"
)),true);



$arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);

$arFields["PREVIEW_PICTURE"]=imgurl($arFile["SRC"], array("w" => 200));
$arResult['arFields'] = $arFields;
$arResult['subs']['stock'] = $club->getCountSubStocks();
$arResult['subs']['event'] = $club->getCountSubEvent();


$arResult['userInfo'] =$userInfo;
$arResult['ADDRESS'] = $club->getAddress();


$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"TYPE_FACILITY"));
while($enum_fields = $property_enums->GetNext())
{
    $arResult["KIND_CLUB_LIST"][]=$enum_fields;
}


$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"MUSIC"));
while($enum_fields = $property_enums->GetNext())
{
    $arResult["MUSIC_LIST"][]=$enum_fields;
}



$this->IncludeComponentTemplate();


