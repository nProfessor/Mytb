<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CModule::IncludeModule("iblock");


global $USER;
$tableID=(int)$arParams["TABLE_ID"];

$Club=Club::getOBonTheUserID($USER->GetID());
$arTable = $Club->table()->getInfo($tableID,array("arSelect"=>array("NAME","PREVIEW_PICTURE")));


$arResult['arTable']=$arTable;





$this->IncludeComponentTemplate();












