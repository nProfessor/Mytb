<?php
/**
 * Выключает прошедшие акции
 * User: Tabota Oleg (sForge.ru)
 * Date: 18.11.12 16:20
 * File name: no_active_stock.php
 */


function no_active_stock()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");


    $sort=array("SORT" => "ASC");
    $filter=array(
            "ACTIVE"=>"Y",
            "IBLOCK_ID" => IB_SUB_STOCK_ID,
            "<DATE_ACTIVE_TO" => date("d.m.Y"));
    $select=array("ID","IBLOCK_ID");
    $ob = CIBlockElement::GetList($sort,$filter,FALSE,FALSE,$select);

    while($row=$ob->Fetch()){
        $el = new CIBlockElement;
        $el->Update(intval($row['ID']), Array("ACTIVE"=> "N"));
    }

    return "no_active_stock();";
}