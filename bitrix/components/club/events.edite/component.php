<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");


global $USER;
$club = Club::getOBonTheUserID($USER::GetID());


// TODO сделать проверку редактирования акции только этим клубом

$event = $club->event();

if (isset($_POST['EDITE_STOCK']) && intval($_GET["ID"]) > 0) {
    $newsID = $event->update(intval($_GET["ID"]), array(
        "ACTIVE_FROM" => empty($_POST['ACTIVE_FROM']) ? date("d.m.Y") : $_POST['ACTIVE_FROM'],
        "ACTIVE_TO" => empty($_POST['ACTIVE_TO']) ? date("d.m.Y") : $_POST['ACTIVE_TO'],
        "NAME" => empty($_POST['NAME']) ? "Нет названия " . date("d.m.Y H:i:s") : $_POST['NAME'],
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],
    ));

}

if (isset($_GET["ID"])) {
    $arResult["EVENT"] = $event->getInfo(intval($_GET["ID"]), true);
}


$arFile = CFile::GetFileArray($arResult["EVENT"]["DETAIL_PICTURE"]);
$arResult["EVENT"]['DETAIL_PICTURE'] = imgurl($arFile['SRC'], array("w"=> 400,"h"=> 400));




$arResult["club"] = $club->getInfo(array("arSelect" => array("ID","NAME")));

if($arResult["EVENT"]['ACTIVE']=="Y"){
    $this->IncludeComponentTemplate();
}else{
    $this->IncludeComponentTemplate("archive");
}

















