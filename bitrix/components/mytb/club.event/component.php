<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$EVENT_ID = $arParams["EVENT_ID"];
$club = new Club($CLUB_ID);

$event=new Event();
$evenInfo=$event->getInfo($EVENT_ID,true);


$arResult['stockInfo'] = $evenInfo;
$club=new Club($evenInfo['PROPERTY_CLUB_ID_VALUE']);

$arResult['club']  = $club->getInfo(array("arSelect"=> array("NAME","ID","PROPERTY_TYPE_FACILITY")));

$APPLICATION->SetTitle($evenInfo['NAME']);

$this->IncludeComponentTemplate();













