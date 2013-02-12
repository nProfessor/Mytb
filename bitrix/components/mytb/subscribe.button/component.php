<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

$arResult["CLUB_ID"]=intval($arParams["CLUB_ID"]);
$arResult["NAME"]=$arParams["CLUB_NAME"];

if ($USER->IsAuthorized()):

$userID=$USER::GetID();
$userRes = new User($userID);
$arRes=$userRes->getProps(array("PROPERTY_LINK_STOK"));
$arRes['PROPERTY_LINK_STOK_VALUE']=unserialize($arRes['PROPERTY_LINK_STOK_VALUE']);

if(in_array($arResult["CLUB_ID"],$arRes['PROPERTY_LINK_STOK_VALUE']['VALUE'])){
    $this->IncludeComponentTemplate("ok");
    return;
}
endif;
$this->IncludeComponentTemplate();
