<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$clubID=intval($arParams["CLUB_ID"]);

$club = new Club($clubID);

$arFields=$club->getInfo(array("arSelect"=> array(
    "ID",
    "PREVIEW_PICTURE",
)),true);


$arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);


$arResult["PREVIEW_PICTURE"]=imgurl($arFile["SRC"], array("w" => 200));


$this->IncludeComponentTemplate();
