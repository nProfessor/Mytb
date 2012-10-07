<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");

global $USER;
$user = new User($USER::GetID());


$rs = $user->getProps(array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_NEWS", "PROPERTY_LINK_EVENT"));


if (isset($_GET['clubID']) && intval($_GET['clubID']) != 0) {
    $clubID = intval($_GET['clubID']);
    if (in_array($clubID, $rs['PROPERTY_LINK_NEWS_VALUE'])) {
        $arListClubSubs = array($clubID);
    }
} else {
    $arListClubSubs = $rs['PROPERTY_LINK_NEWS_VALUE'];
}

if (count($arListClubSubs) > 0) {
    $ob = CIBlockElement::GetList(
        array("SORT" => "ASC"),
        array("PROPERTY_CLUB_ID" => $arListClubSubs,
              "IBLOCK_ID"        => IB_SUB_NEWS_ID),
        FALSE,
        FALSE,
        array(
             "ID",
             "NAME",
             "DATE_ACTIVE_FROM",
             "DATE_ACTIVE_TO",
             "PREVIEW_TEXT",
             "DETAIL_PICTURE",
             "PROPERTY_CLUB_ID",
        ));
}

if ($ob) {

    while ($ar = $ob->Fetch()) {
        $arFile = CFile::GetFileArray($ar["DETAIL_PICTURE"]);

        $ar["DETAIL_PICTURE"]                                             = $arFile["SRC"];
        $areventList[date("d.m.Y", strtotime($ar['DATE_ACTIVE_FROM']))][] = $ar;
    }
}

if (count($arListClubSubs) > 0) {
    $rsClub = Club::getList(array(), array("ID"=> $rs['PROPERTY_LINK_NEWS_VALUE']), FALSE, FALSE, array("NAME",
                                                                                                        "ID",
                                                                                                        "PREVIEW_PICTURE"));

    while ($arClub = $rsClub->Fetch()) {
        $arFile                    = CFile::GetFileArray($arClub["PREVIEW_PICTURE"]);
        $arClubList[$arClub['ID']] = array("NAME"=> $arClub['NAME'], "ID"=> $arClub['ID'], "SRC"=> $arFile["SRC"]);
    }
}

$arResult['eventList'] = $areventList;
$arResult['club']      = $arClubList;
$arResult['clubID']    = intval($_GET['clubID']);


$this->IncludeComponentTemplate();




















