<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");

$ARTICLE_ID = (int)$arParams['ARTICLE_ID'];


$objArticle=new Article($ARTICLE_ID);
$article=$objArticle->getItem();

$arResult["ARTICLE"]=$article[$ARTICLE_ID];



$this->IncludeComponentTemplate();


$APPLICATION->SetTitle(html_entity_decode($arResult["ARTICLE"]['NAME']));












