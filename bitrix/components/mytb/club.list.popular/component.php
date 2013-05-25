<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");
global $USER;



$arResult["CLUB_LIST"] =Club::getListPopular();






$this->IncludeComponentTemplate();













