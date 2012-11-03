<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$countClub=Club::count();
$ob = CIBlockElement::GetList(
    array("IBLOCK_ID" => "ASC"),
    array("IBLOCK_ID"               => IB_SUB_STOCK_ID,
        ">DATE_ACTIVE_TO"         => date("d.m.Y h:i:s")),
    array("IBLOCK_ID"),
    FALSE,
    array("IBLOCK_ID"))->Fetch();


$arResult["CLUB_COUN"]=$countClub;
$arResult["CLUB_STOCK"]=$ob['CNT'];


$this->IncludeComponentTemplate();