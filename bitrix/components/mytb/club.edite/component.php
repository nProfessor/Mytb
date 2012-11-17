<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");


global $USER;
$arGroupPrice = array();

$Club = Club::getOBonTheUserID($USER->GetID());


if($_POST['CLUB']){

    if(!empty($_POST['CLUB']['NAME'])){
        $clubID=Club::getClubID($USER->GetID());


        $el = new CIBlockElement;

        $PROP = array();
        $PROP["PHONE"] = trim(strip_tags($_POST['CLUB']['PHONE']));
        $PROP["ADDRESS"] = trim(strip_tags($_POST['CLUB']['ADDRESS']));
        $PROP["METRO"] = trim(strip_tags($_POST['CLUB']['METRO']));
        $PROP["SITE"] = trim(strip_tags($_POST['CLUB']['SITE']));
        $PROP["PRICE_COCKTAIL"] = trim(($_POST['CLUB']['PRICE_COCKTAIL']));
        $PROP["TIME_WORKING"] = trim(($_POST['CLUB']['TIME_WORKING']));

        //            "PROPERTY_TYPE_FACILITY"=>trim($_POST['CLUB']['TYPE_FACILITY']),
//            "PROPERTY_MUSIC"=>trim($_POST['CLUB']['MUSIC']),
//            "PROPERTY_FACE_CONTROL"=>trim($_POST['CLUB']['FACE_CONTROL']),
//            "PROPERTY_DRESS_CODE"=>trim($_POST['CLUB']['DRESS_CODE']),
//            "PROPERTY_EMAIL_MANAGER"=>trim($_POST['CLUB']['EMAIL_MANAGER']),
//            "PROPERTY_AVERAGE_TICKET"=>trim($_POST['CLUB']['AVERAGE_TICKET']),



        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "NAME"=>trim(strip_tags($_POST['CLUB']['NAME'])),
            "DETAIL_TEXT"=>trim($_POST['CLUB']['DETAIL_TEXT']),
            "PROPERTY_VALUES"=> $PROP,
        );

        $res = $el->Update($clubID, $arLoadProductArray);

    }

}


$arField = $Club->getInfo(array(
    "arSelect" => array(
        "ID",
        "PROPERTY_PLAN",
        "NAME",
        "PREVIEW_PICTURE",
        "DETAIL_TEXT",
        "PROPERTY_PHONE",
        "PROPERTY_ADDRESS",
        "PROPERTY_METRO",
        "PROPERTY_SITE",
        "PROPERTY_PRICE_COCKTAIL",
        "PROPERTY_TYPE_FACILITY",
        "PROPERTY_MUSIC",
        "PROPERTY_FACE_CONTROL",
        "PROPERTY_DRESS_CODE",
        "PROPERTY_TIME_WORKING",
        "PROPERTY_EMAIL_MANAGER",
        "PROPERTY_AVERAGE_TICKET",
    ),
    "arFilter" => array(),
),true);

$arFile = CFile::GetFileArray($arField["PREVIEW_PICTURE"]);
$arField["PREVIEW_PICTURE"]=$arFile["SRC"];




$obTableList = $Club->getResTableList(array(
    "arSelect" => array("ID", "NAME", "ACTIVE", "PROPERTY_PRICE_GROUP", "PREVIEW_PICTURE", "PROPERTY_COUNT", "PROPERTY_PRICE_GROUP"),
    "arFilter" => array("ACTIVE" => "Y"),
));


$arTableList = array();
while ($ob = $obTableList->Fetch()) {
    $arFile = CFile::GetFileArray($ob["PREVIEW_PICTURE"]);
    $ob["PREVIEW_PICTURE"] = empty($arFile["SRC"]) ? "/img/160x120.gif" : $arFile["SRC"];
    $arTableList[] = $ob;
}


$PriceGroupList = $Club->getPriceGroupList(array(
    "arSelect" => array("ID", "ACTIVE", "PROPERTY_PRICE", "PREVIEW_CLUB"),
));

while ($ob = $PriceGroupList->Fetch()) {
    $arGroupPrice[$ob["ID"]] = $ob["PROPERTY_PRICE_VALUE"];
}


$arResult['arFields'] = $arField;
$arResult['arTableList'] = $arTableList;
$arResult['arGroupPrice'] = $arGroupPrice;


$this->IncludeComponentTemplate();












