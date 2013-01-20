<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;


$CLUB_ID = $arParams["CLUB_ID"];
$club = new Club($CLUB_ID);
$resStockList = $club->getListNews();


while ($ar = $resStockList->Fetch()) {
    $arStockList[] = $ar;
}

$arResult['stockList'] = $arStockList;
$arResult['club']      = $club->getInfo(array("arSelect"=> array("NAME")));


$this->IncludeComponentTemplate();













