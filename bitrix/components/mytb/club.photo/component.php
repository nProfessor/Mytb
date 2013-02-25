<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");


$clubID = intval($arParams['CLUB_ID']);


$club = new Club($clubID);



$arResult['photo'] = $club->getPhotoList();


$arResult['clubID']=$clubID;
$arResult['clubName']=$arParams['CLUB_NAME'];



$this->IncludeComponentTemplate();


