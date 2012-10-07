<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CModule::IncludeModule("iblock");




$arSelect = Array("ID","NAME");
$arFilter = Array("IBLOCK_ID"=>IB_BOOKING_ID,"ACTIVE"=>"Y","ID"=>$arParams["BOOKING_ID"]);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$ob = $res->GetNext();


$this->IncludeComponentTemplate();













