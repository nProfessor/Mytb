<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$STOCK_ID = $arParams["STOCK_ID"];
$club = new Club($CLUB_ID);

$stock=new Stocks();
$stockInfo=$stock->getInfo($STOCK_ID,true);



$arResult['stockInfo'] = $stockInfo;
$club=new Club($stockInfo['PROPERTY_CLUB_ID_VALUE']);

$arResult['club']  = $club->getInfo(array("arSelect"=> array("NAME","ID","PROPERTY_TYPE_FACILITY")));

$this->IncludeComponentTemplate();













