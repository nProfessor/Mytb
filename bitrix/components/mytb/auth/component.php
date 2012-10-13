<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult["AUTH_SERVICES"] = false;
$arResult["CURRENT_SERVICE"] = false;
$arResult["AUTH_SERVICES_HTML"] = '';

CModule::IncludeModule("socialservices");
$oAuthManager = new CSocServAuthManager();
$arServices = $oAuthManager->GetActiveAuthServices($arResult);

$arResult["AUTH_SERVICES"]=$arServices;
$this->IncludeComponentTemplate();