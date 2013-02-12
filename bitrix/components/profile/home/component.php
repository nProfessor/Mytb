<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


global $USER;

$userID=$USER::GetID();

$userInfo=CUser::GetByID($userID)->fetch();


$userInfo["PERSONAL_PHOTO"] = CFile::GetFileArray($userInfo["PERSONAL_PHOTO"]);


$arResult['userinfo']=$userInfo;

$this->IncludeComponentTemplate();
