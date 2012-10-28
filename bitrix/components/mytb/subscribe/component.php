<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

$arParams["CLUB_INFO"]=2;
$arResult["AUTH"]=$arParams["AUTH"]?"yes":"no";

$this->IncludeComponentTemplate();
