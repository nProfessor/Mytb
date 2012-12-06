<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$CLUB_ID = $arParams["CLUB_ID"];
$club = new Club($CLUB_ID);

$array=array(
    "stocks"=>array(
        "name" => "Действующие акции",
        "list" => array(),
        "show" => false
    ),
    "event"=>array(
        "name" => "Предстоящие события",
        "list" => array(),
        "show" => false
    )
);

$stokRes = $club->stock();
$eventRes = $club->event();

$countSock = $stokRes->getCount();
$countEvent = $eventRes->getCount();

if($countSock[$CLUB_ID]>0){
    $array["stocks"]["list"] = $stokRes->getListArray(array(">=DATE_ACTIVE_TO"=> date("d.m.Y")));
    $array["stocks"]["show"] = true;
    $arResult["LIST"]["stocks"]=$array["stocks"];
}

if($countEvent[$CLUB_ID]>0){
    $array["event"]["list"] = $eventRes->getListArray(array(">=DATE_ACTIVE_TO"=> date("d.m.Y")));
    $array["event"]["show"] = true;
    $arResult["LIST"]["event"]=$array["event"];
}

$arResult["CLUB_ID"]=$CLUB_ID;
$arResult["CLUB"]=$club->getInfo(array("arSelect"=>array("NAME","PREVIEW_PICTURE")));

$this->IncludeComponentTemplate();













