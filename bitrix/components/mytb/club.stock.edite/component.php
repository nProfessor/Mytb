<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;
$club = Club::getOBonTheUserID($USER::GetID());


$stock = $club->stock();
if (isset($_POST['ADD_STOCK'])) {
    $newsID = $stock->add(array(
        "ACTIVE_FROM" => empty($_POST['ACTIVE_FROM']) ? date("d.m.Y") : $_POST['ACTIVE_FROM'],
        "ACTIVE_TO" => empty($_POST['ACTIVE_TO']) ? date("d.m.Y") : $_POST['ACTIVE_TO'],
        "NAME" => empty($_POST['NAME']) ? "Нет названия " . date("d.m.Y H:i:s") : $_POST['NAME'],
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],
    ));
    header("Location: /kabinet-menedzhera/club_stocks_edite/edite/{$newsID}");
    die();
}

if (isset($_POST['EDITE_STOCK']) && intval($_GET["ID"]) > 0) {


    $newsID = $stock->update(intval($_GET["ID"]), array(
        "ACTIVE_FROM" => empty($_POST['ACTIVE_FROM']) ? date("d.m.Y") : $_POST['ACTIVE_FROM'],
        "ACTIVE_TO" => empty($_POST['ACTIVE_TO']) ? date("d.m.Y") : $_POST['ACTIVE_TO'],
        "NAME" => empty($_POST['NAME']) ? "Нет названия " . date("d.m.Y H:i:s") : $_POST['NAME'],
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],

    ));

}

if (isset($_GET["ID"])) {
    $arResult["STOCK"] = $stock->getInfo(intval($_GET["ID"]), true);
}


$arFile = CFile::GetFileArray($arResult["STOCK"]["PREVIEW_PICTURE"]);
$arResult["STOCK"]['PREVIEW_PICTURE'] = $arFile["SRC"];


$arResult["club"] = $club->getInfo(array("arSelect" => array("NAME")));

$newsListRes = $club->stock()->getList();
while ($obj = $newsListRes->Fetch()) {
    $obj["ACTIVE_FROM"] = date("d.m.Y", strtotime($obj["ACTIVE_FROM"]));
    $arResult["STOCKS_LIST"][] = $obj;
}


$this->IncludeComponentTemplate();














