<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();



if ($this->StartResultCache()) {
$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"TYPE_FACILITY"));
while($enum_fields = $property_enums->Fetch())
{
    $arResult["KIND_CLUB_LIST"][]=$enum_fields;
}


$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"MUSIC"));
while($enum_fields = $property_enums->Fetch())
{
    $arResult["MUSIC_LIST"][]=$enum_fields;
}


$arResult["FILTER"]=$arParams['GET'];



$this->IncludeComponentTemplate();
}