<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 13.01.13 15:21
 * File name: subscribe.php
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

global $USER;
$CLUB_ID=intval($_POST["CLUB_ID"]);

$ob = CIBlockElement::GetList(
    array("SORT" => "ASC"),
    array("PROPERTY_CLUB_ID"        => $CLUB_ID,
        "IBLOCK_ID"               => array(IB_SUB_STOCK_ID,IB_SUB_EVENT_ID),
        ">=DATE_ACTIVE_TO"        => date("d.m.Y")),
    FALSE,
    FALSE,
    array(
        "ID",
        "NAME",
        "IBLOCK_ID",
        "DATE_ACTIVE_FROM",
        "DATE_ACTIVE_TO",
        "PREVIEW_TEXT",
        "DETAIL_PICTURE",
        "PROPERTY_CLUB_ID",
        "DATE_ACTIVE_TO"
    ));

while ($ar = $ob->Fetch()) {
    $ar['DATE_ACTIVE_TO']=dateShowTextMonth($ar['DATE_ACTIVE_TO']);
    $ar["CLASS"]=$ar["IBLOCK_ID"] == IB_SUB_STOCK_ID ? "stock" : "event";
    $arStockList[] = $ar;
}
if (count($arStockList)>0) {
    die(json_encode(array("status"=>"ok","data"=>$arStockList)));
}else{
    die(json_encode(array("status"=>"error","message"=>"Нет действующих акций и событий")));
}

