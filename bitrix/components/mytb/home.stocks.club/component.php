<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;


if(isset($arParams["LIMIT"])){
    $arNavStartParams=array("nTopCount"=>intval($arParams["LIMIT"]));
}else{
    $arNavStartParams=false;
}

$ob = CIBlockElement::GetList(
    array("DATE_ACTIVE_TO" => "ASC"),
    array("IBLOCK_ID"               => IB_SUB_STOCK_ID,
          ">DATE_ACTIVE_TO"         => date("d.m.Y h:i:s")),
    array("PROPERTY_CLUB_ID"),
    false,
    array(
         "ID",
         "NAME",
         "DATE_ACTIVE_FROM",
         "DATE_ACTIVE_TO",
         "ACTIVE_TO",
         "PREVIEW_TEXT",
         "DETAIL_PICTURE",
         "PROPERTY_CLUB_ID",
         "DATE_ACTIVE_TO",
         "PROPERTY_URL",
         "PROPERTY_CLUB_ID",
         "PROPERTY_PRICECOUPON",
         "PROPERTY_DISCOUNT"
    ));

$clubListID = array();
$clubList = array();
while ($row = $ob->Fetch()) {
    $clubListID[] = intval($row['PROPERTY_CLUB_ID_VALUE']);
}

if (count($clubListID)) {
    $res = CIBlockElement::GetList(Array(), array("ID"                      => $clubListID,
                                                  "IBLOCK_ID"               => IB_CLUB_ID,), FALSE, $arNavStartParams, array("ID","NAME","PROPERTY_TYPE_FACILITY"));


    while ($row = $res->Fetch()) {
        $arResult['clubList'][$row["ID"]] = $row;
    }
}


$this->IncludeComponentTemplate();








