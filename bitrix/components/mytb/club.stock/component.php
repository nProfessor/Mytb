<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();


CModule::IncludeModule("iblock");
global $USER;

$STOCK_ID = $arParams["STOCK_ID"];



$cache_id = serialize(array($arParams,$_SESSION['CLEAR_CASH']));
$arParams['CACHE_TIME']=intval($arParams['CACHE_TIME'])>0?$arParams['CACHE_TIME']:3600;
//
$obCache = new CPHPCache;
if ($obCache->InitCache($arParams['CACHE_TIME'], $cache_id,"/stock/"))
{
    $vars = $obCache->GetVars();
    $arResult = $vars['arResult'];
}
elseif ($obCache->StartDataCache()){

$club = new Club($CLUB_ID);

$stock=new Stocks();
$stockInfo=$stock->getInfo($STOCK_ID,true);



$arResult['stockInfo'] = $stockInfo;
$club=new Club($stockInfo['PROPERTY_CLUB_ID_VALUE']);

$arResult['club']  = $club->getInfo(array("arSelect"=> array("NAME","ID","PROPERTY_TYPE_FACILITY")));

    $obCache->EndDataCache(array(
        'arResult' => $arResult,
    ));

}





$APPLICATION->SetTitle($arResult['stockInfo']['NAME']);
$APPLICATION->SetPageProperty("Expires",date("r",strtotime("+30 day")));
$this->IncludeComponentTemplate();










