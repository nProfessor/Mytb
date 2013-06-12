<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");



$article=Article::getList();


$arResult["ARTICLE"]=$article;



$this->IncludeComponentTemplate();


$APPLICATION->SetTitle(html_entity_decode($arResult["ARTICLE"]['NAME']));












