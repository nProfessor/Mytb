<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();
CModule::IncludeModule("iblock");
$APPLICATION->AddHeadScript("/jslibs/jquery/jquery-1.7.2.min.js");
$APPLICATION->AddHeadScript("/jslibs/jqueryui/js/jquery-ui-1.8.21.custom.min.js");

global $USER;

$clubID = (int)$arParams['ID'];

$club = new Club($clubID);


$arResult['arFields'] = $club->getInfo(array("arSelect"=> array(
    "ID",
    "NAME"
)), TRUE);


if (isset($_POST["clubID"]) && intval($_POST["clubID"]) > 0) {
    $user = new User($USER::GetID());

    $_POST["subsrcibe"]["stok"];
    $_POST["subsrcibe"]["event"];
    $_POST["subsrcibe"]["news"];

    $user->setSubscribe();

}




$this->IncludeComponentTemplate();

