<?php
/**
 * User:        ����
 * Data:        11.06.12 22:14
 * Site: http://sForge.ru
 **/
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

$arSelect = Array(
    "ID",
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    'PROPERTY_RATING',
    'PROPERTY_METRO',
    'PROPERTY_TIME_WORKING',
    'PROPERTY_PRICE_COCKTAIL',
    'PROPERTY_CARDS',
);
$arFilter = Array(
    "IBLOCK_ID" => IB_CLUB_ID,
    "ACTIVE_DATE" => "Y",
    "ACTIVE" => "Y");

$res = CIBlockElement::GetList(Array("SORT"=>"DESC"), $arFilter, false, Array("nPageSize" => 50), $arSelect);
while ($ob = $res->GetNextElement()) {
    $arField = $ob->GetFields();
    $arFile = CFile::GetFileArray($arField["PREVIEW_PICTURE"]);


    $arFields[]=array(
        "ID"=>$arField["ID"],
        "NAME"=>$arField["NAME"],
        "PREVIEW_TEXT"=>$arField["~PREVIEW_TEXT"],
        "PREVIEW_PICTURE"=>$arFile["SRC"],
        "PROPERTY_METRO_VALUE"=>$arField["PROPERTY_METRO_VALUE"],
        "PROPERTY_RATING_VALUE"=>$arField["PROPERTY_RATING_VALUE"],
        "PROPERTY_TIME_WORKING_VALUE"=>$arField["PROPERTY_TIME_WORKING_VALUE"],
        "PROPERTY_PRICE_COCKTAIL_VALUE"=>$arField["PROPERTY_PRICE_COCKTAIL_VALUE"],
        "PROPERTY_CARDS_VALUE"=>$arField["PROPERTY_CARDS_VALUE"],

    );
}

$stocksRes = new Stocks(1);
$stocksCount=$stocksRes->getCount();



$arResult["stocksCount"]=$stocksCount;
$arResult["ClubList"]=$arFields;
$arResult["res"]=$res;


$this->IncludeComponentTemplate();