<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;
$club = Club::getOBonTheUserID($USER::GetID());


$news = $club->news();
if (isset($_POST['ADD_NEWS'])) {
    $newsID = $news->add(array(
        "ACTIVE_FROM" => empty($_POST['ACTIVE_FROM']) ? date("d.m.Y") : $_POST['ACTIVE_FROM'],
        "NAME" => empty($_POST['NAME']) ? "Нет названия " . date("d.m.Y H:i:s") : $_POST['NAME'],
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],
    ));
    header("Location: /kabinet-menedzhera/club_news_edite/edite/{$newsID}");
    die();
}

if (isset($_POST['EDITE_NEWS']) && intval($_GET["ID"]) > 0) {


    $newsID = $news->update(intval($_GET["ID"]), array(
        "ACTIVE_FROM" => empty($_POST['ACTIVE_FROM']) ? date("d.m.Y") : $_POST['ACTIVE_FROM'],
        "NAME" => empty($_POST['NAME']) ? "Нет названия " . date("d.m.Y H:i:s") : $_POST['NAME'],
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],

    ));

}

if (isset($_GET["ID"])) {
    $arResult["NEWS"] = $news->getInfo(intval($_GET["ID"]), true);
}


$arFile = CFile::GetFileArray($arResult["NEWS"]["PREVIEW_PICTURE"]);
$arResult["NEWS"]['PREVIEW_PICTURE'] = $arFile["SRC"];


$arResult["club"] = $club->getInfo(array("arSelect" => array("NAME")));

$newsListRes = $club->news()->getList();
while ($obj = $newsListRes->Fetch()) {
    $obj["ACTIVE_FROM"] = date("d.m.Y", strtotime($obj["ACTIVE_FROM"]));
    $arResult["NEWS_LIST"][] = $obj;
}


$this->IncludeComponentTemplate();














