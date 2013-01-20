<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$CLUB_ID = $arParams["CLUB_ID"];
$club = new Club($CLUB_ID);


$resEventList = $club->getListEvent();

while ($ar = $resEventList->Fetch()) {
    $arEventList[] = $ar;
}


$arResult['eventList'] = $arEventList;
$arResult['club']      = $club->getInfo(array("arSelect"=> array("NAME")));


$this->IncludeComponentTemplate();













