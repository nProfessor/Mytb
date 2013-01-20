<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");


$user = new User($arParams['USER_ID']);

//достаем информуци.  у пользователя
$rs = $user->getProps(array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_EVENT"));

//Если Ajax о показываем информацию только по одному клубу
if ($arParams["AJAX"] == "Y" && intval($_POST['clubID']) != 0) {
    $clubID = intval($_POST['clubID']);
    if (in_array($clubID, $rs['PROPERTY_LINK_STOK_VALUE'])) {
        $arListClubSubs = array(intval($_POST['clubID']));
    }
} else { //иначе достаем спиисок всех клубов
    $arListClubSubs = array_merge($rs['PROPERTY_LINK_STOK_VALUE'],$rs['PROPERTY_LINK_EVENT_VALUE']);
}

if (count($arListClubSubs) > 0) {
    $ob = CIBlockElement::GetList(
        array("SORT" => "ASC"),
        array("PROPERTY_CLUB_ID"        => $arListClubSubs,
              "IBLOCK_ID"               => array(IB_SUB_STOCK_ID,IB_SUB_EVENT_ID),
              ">=DATE_ACTIVE_TO"        => date("d.m.Y")),
        FALSE,
        FALSE,
        array(
             "ID",
             "NAME",
             "IBLOCK_ID",
             "DATE_ACTIVE_FROM",
             "DATE_ACTIVE_TO",
             "PREVIEW_TEXT",
             "DETAIL_PICTURE",
             "PROPERTY_CLUB_ID",
             "DATE_ACTIVE_TO"
        ));
}

if ($ob) {
    while ($ar = $ob->Fetch()) {
        $arFile               = CFile::GetFileArray($ar["DETAIL_PICTURE"]);
        $ar["DETAIL_PICTURE"] = $arFile["SRC"];
        $clubListID[]         = intval($ar['PROPERTY_CLUB_ID_VALUE']);

        $arStockList[] = $ar;
    }
}

if (count($arListClubSubs) > 0) {
    $rsClub = Club::getList(array(), array("ID"=> $arListClubSubs), FALSE, FALSE, array("NAME", "ID"));
    while ($arClub = $rsClub->Fetch()) {
        $arClubList[$arClub['ID']] = array("NAME"=> $arClub['NAME'], "ID"=> $arClub['ID']);
    }
}


$arResult['stockList'] = $arStockList;
$arResult['club']      = $arClubList;


$this->IncludeComponentTemplate();












