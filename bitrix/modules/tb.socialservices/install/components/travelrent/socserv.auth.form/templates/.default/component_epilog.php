<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arExt = array();
if($arParams["POPUP"])
	$arExt[] = "window";
CUtil::InitJSCore($arExt);
$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/js/tr.socialservices/css/ss.css");
$GLOBALS["APPLICATION"]->AddHeadScript("/bitrix/js/tr.socialservices/ss.js");
?>