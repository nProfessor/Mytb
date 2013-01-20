<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");


$clubID = (int)$arParams['CLUB_ID'];

$club = new Club($clubID);

$arResult["ADDRESS"]=$club->getAddress();
$arResult["NAME"]=$arParams['NAME'];

if(count($arResult["ADDRESS"])>1){
    $this->IncludeComponentTemplate("list");

}else{

    $this->IncludeComponentTemplate();
}








