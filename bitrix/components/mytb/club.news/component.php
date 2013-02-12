<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$NEWS_ID = $arParams["NEWS_ID"];
$club = new Club($CLUB_ID);

$news=News::getInfoStatic($NEWS_ID)->GetNext();


$arResult['news'] = $news;
$club=new Club($news['PROPERTY_CLUB_ID_VALUE']);

$arResult['club']  = $club->getInfo(array("arSelect"=> array("NAME","ID","PROPERTY_TYPE_FACILITY")));

$APPLICATION->SetTitle($arResult['club']['NAME'].": ".$news['NAME']);

$this->IncludeComponentTemplate();













