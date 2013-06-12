<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");



$article=Article::getList();


$arResult["ARTICLE"]=$article;



$this->IncludeComponentTemplate();


$APPLICATION->SetTitle("Список интересных статей от проекта MyTb.ru");

$APPLICATION->SetPageProperty("description", "Список статей от проекта MyTb.ru");












