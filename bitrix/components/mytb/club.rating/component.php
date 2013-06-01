<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();


$clubID = (int)$arParams['CLUB_ID'];



global $USER;


$club = new Club($clubID);

$arFields=$club->getInfo(array("arSelect"=> array(
    "ID",
    "PROPERTY_RATING",
)),true);

$arResult["RATING"]=$arFields['PROPERTY_RATING_VALUE'];

$this->IncludeComponentTemplate();














