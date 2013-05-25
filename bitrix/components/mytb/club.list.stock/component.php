<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;


$CLUB_ID = $arParams["CLUB_ID"];
$club = new Club($CLUB_ID);


if ($USER->IsAuthorized() && $_GET["subscribe"] == "ok") {
    $obUser = new User($USER::GetID());
    $obUser->setSubscribe($CLUB_ID, array("LINK_STOK"));
}


$resStockList = $club->getListStok();

while ($ar = $resStockList->Fetch()) {
    $arStockList[] = $ar;
}


$arResult['stockList'] = $arStockList;
$arResult['club']      = $club->getInfo(array("arSelect"=> array("NAME","PREVIEW_PICTURE")));
$arResult['club']['NAME']=trim($arResult['club']['NAME']);
$APPLICATION->SetTitle("Акции {$arResult['club']['NAME']}. Обычные и купонные, действующие акции заведения {$arResult['club']['NAME']}");


$this->IncludeComponentTemplate();













