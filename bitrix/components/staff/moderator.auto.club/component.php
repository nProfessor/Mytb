<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();


$arResult["CLUB_LIST"]=Club::getListModerator();



$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"KIND_CLUB"));
while($enum_fields = $property_enums->GetNext())
{
    $arResult["KIND_CLUB_LIST"][]=$enum_fields;
}


$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"MUSIC"));
while($enum_fields = $property_enums->GetNext())
{
    $arResult["MUSIC_LIST"][]=$enum_fields;
}



$this->IncludeComponentTemplate();

