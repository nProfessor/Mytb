<?php
/**
 * User:        Олег
 * Data:        14.06.12 22:59
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$clubID = intval($_GET["clubID"]);
$priceGroupListID = Array();

$arSelect = Array(
    "ID",
    "NAME",
    "PROPERTY_COORDINATESX",
    "PROPERTY_COORDINATESY",
    "PROPERTY_WIDTH",
    "PROPERTY_HEIGHT",
    "PREVIEW_PICTURE",
    "PROPERTY_COUNT",
    "PROPERTY_PRICE_GROUP",
);

$arFilter = Array("IBLOCK_ID" => IB_TABLE_ID, "ACTIVE" => "Y", "PROPERTY_CLUB" => $clubID);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

while ($ob = $res->GetNext()) {
    $arFile = CFile::GetFileArray($ob["PREVIEW_PICTURE"]);
    $ob["PREVIEW_PICTURE"] = $arFile["SRC"];
    $arFields[] = $ob;
    $priceGroupListID[] = (int)$ob["PROPERTY_PRICE_GROUP_VALUE"];
}

if (count($priceGroupListID)) {
    $arSelect = Array(
        "ID",
        "PROPERTY_PRICE",
    );

    $arFilter = Array("IBLOCK_ID" => IB_PRICE_GROUP, "ACTIVE" => "Y", "PROPERTY_CLUB" => $clubID);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    while ($ob = $res->Fetch()) {
        $priceGroupList[$ob['ID']] = $ob['PROPERTY_PRICE_VALUE'];
    }

}

foreach ($arFields as $val=>$var) {
    if (isset($priceGroupList[$var["PROPERTY_PRICE_GROUP_VALUE"]])) {
        $arFields[$val]["PROPERTY_PRICE_GROUP"]=formatPrice($priceGroupList[$var["PROPERTY_PRICE_GROUP_VALUE"]]*$var["PROPERTY_COUNT_VALUE"],"руб.");
    }
}


echo json_encode($arFields);

