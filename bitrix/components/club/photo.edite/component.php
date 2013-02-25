<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");

global $USER;

$userRes=new User($USER::GetID());
$userInfo=$userRes->getInfo();
$user_props=$userRes->getProps(array("PROPERTY_CLUB"));
$clubID = intval($user_props['PROPERTY_CLUB_VALUE']);


$club = new Club($clubID);



$arResult['photo'] = $club->getPhotoList();


$arResult['clubID']=$clubID;



$this->IncludeComponentTemplate();


