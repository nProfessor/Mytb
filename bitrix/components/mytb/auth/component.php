<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


if ($this->StartResultCache()) {
CModule::IncludeModule("socialservices");

$FBappID = CSocServFacebook::GetOption("facebook_appid");
$FBappSecret = CSocServFacebook::GetOption("facebook_appsecret");
$fb_soc = new CFacebookInterface($FBappID,$FBappSecret);

$arResult['URL_FB']=$fb_soc->GetAuthUrl("http://".MAINSERVER."/auth/facebook/".base64_encode("http://".MAINSERVER.$arParams['AUTH_URL']));

$arResult['URL_VK']="https://oauth.vk.com/authorize?client_id=".CSocServVKontakte::GetOption("vkontakte_appid")."&scope=8199&redirect_uri=".urlencode("http://".MAINSERVER."/auth/vkontakte/".base64_encode("http://".MAINSERVER.$arParams['AUTH_URL']));
$arResult['LOGIN_TOP_REDIRECT']=$arParams['LOGIN_TOP_REDIRECT'];


$this->IncludeComponentTemplate();
}