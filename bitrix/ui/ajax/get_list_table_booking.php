<?php
/**
 * User:        Олег
 * Data:        14.06.12 22:59
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
$date=$_GET["dateText"];


$arSelect = Array("ID","NAME","PROPERTY_CLUB","PROPERTY_TABLE","ACTIVE_FROM");
$arFilter = Array("IBLOCK_ID"=>IB_BOOKING_ID,"ACTIVE"=>"Y","DATE_ACTIVE_FROM"=>date("d.m.Y",strtotime($date)));
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNext())
{
    $arFields[]=$ob;
}

echo json_encode($arFields);

