<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
global $USER;
$userID=$USER->GetID();

$arSelect = Array();
$arFilter = Array("IBLOCK_ID"=>IB_CLUB_USER_ID,"ACTIVE"=>"Y", "PROPERTY_USER"=>$userID);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$arField = $res->GetNextElement()->GetProperties("CLUB");

if(intval($arField["CLUB"]["VALUE"])==0){
    die("Данный пользователь не привязан к клубу");
}

$X=(int)$_POST["x"];
$Y=(int)$_POST["y"];
$W=(int)$_POST["w"];
$H=(int)$_POST["h"];
$text=$_POST["text"];

$number=$_POST["number"];
$count=(int)$_POST["count"];
$price_group=(int)$_POST["price_group"];
$club=$_POST["club"];

$el = new CIBlockElement;

$PROP = array(
    "COORDINATESX"=>$X,
    "COORDINATESY"=>$Y,
    "WIDTH"=>$W,
    "HEIGHT"=>$H,
    "CLUB"=>intval($arField["CLUB"]["VALUE"]),
    "PRICE_GROUP"=>$price_group,
    "COUNT"=>$count

);


$arLoadProductArray = Array(
    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
    "IBLOCK_ID"      => IB_TABLE_ID,
    "PROPERTY_VALUES"=> $PROP,
    "NAME"           => "Столик №".$number,
    "ACTIVE"         => "Y",            // активен
);

if($PRODUCT_ID = $el->Add($arLoadProductArray))
    echo $PRODUCT_ID;
?>