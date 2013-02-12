<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arFilter = array(
    "ACTIVE" => "Y"
);

$arFilter["ID"] = Club::getListHaveNews();


$arNavStartParams = array("nPageSize" => 33, "bShowAll" => false);
$result = array();



$res = Club::getList(Array("SORT" => "DESC"), $arFilter, FALSE, $arNavStartParams, Array(
    "NAME",
    "ID",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    'PROPERTY_RATING',
    'PROPERTY_METRO',
    'PROPERTY_TIME_WORKING',
    'PROPERTY_PRICE_COCKTAIL',
    'PROPERTY_CARDS',
));

while ($arField = $res->Fetch()) {
    $arFile = CFile::GetFileArray($arField["PREVIEW_PICTURE"]);

    $result[] = array(
        "ID" => $arField["ID"],
        "NAME" => $arField["NAME"],
        "PREVIEW_TEXT" => $arField["~PREVIEW_TEXT"],
        "PREVIEW_PICTURE" => imgurl($arFile["SRC"], array("w" => 200)),
        "PROPERTY_METRO_VALUE" => $arField["PROPERTY_METRO_VALUE"],
        "PROPERTY_RATING_VALUE" => $arField["PROPERTY_RATING_VALUE"],
        "PROPERTY_TIME_WORKING_VALUE" => str_replace(";", "<br/>", $arField["PROPERTY_TIME_WORKING_VALUE"]),
        "PROPERTY_PRICE_COCKTAIL_VALUE" => $arField["PROPERTY_PRICE_COCKTAIL_VALUE"],
        "PROPERTY_CARDS_VALUE" => $arField["PROPERTY_CARDS_VALUE"],
    );

    $clubListID[]=intval($arField["ID"]);
}



$arResult["CLUB_LIST"] = $result;
$arResult["stocksCount"] = Stocks::getCount($clubListID);


$arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, "", "modern");

//$arResult["NAV"]=$res->NavPrint("События", false, "text","/include/paginator/home.php");;

$this->IncludeComponentTemplate();