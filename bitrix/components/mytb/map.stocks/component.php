<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($this->StartResultCache()) {
    $property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>IB_CLUB_ID, "CODE"=>"TYPE_FACILITY"));
    while($enum_fields = $property_enums->Fetch())
    {
        if($enum_fields['ID']!=9)
        $arResult["KIND_CLUB_LIST"][]=$enum_fields;
    }

    $this->IncludeComponentTemplate();
}