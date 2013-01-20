<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


//$arResult['userinfo'] = $arParams['USER_INFO'];

$userID = $USER::GetID();
$userRes = new User($userID);
$infoUser=$userRes->getInfo();

$infoUser['PERSONAL_PHOTO']= CFile::GetFileArray($infoUser["PERSONAL_PHOTO"]);

$arResult['userinfo']=$infoUser;

$this->IncludeComponentTemplate();
