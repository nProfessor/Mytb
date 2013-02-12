<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");
global $USER;

$obUser = new User($USER::GetID());
$user_props=$obUser->getProps(array("PROPERTY_CLUB"));
$clubID = intval($user_props['PROPERTY_CLUB_VALUE']);
$club = new Club($clubID);


if(!empty($_POST['NAME'])){
    $events = new Event($clubID);
    $id=(int)$events->add(array("NAME"=>trim(strip_tags($_POST['NAME'])),"ACTIVE"=>"N"));
    header("Location: /personal/club/event/".$id);
    die();
}



$resStockList = $club->getListEventManager("Y", array("nPageSize" => 33, "bShowAll" => false));

while ($ar = $resStockList->Fetch()) {
    $arList[] = $ar;
}


$arResult['arList'] = $arList;
$arResult['club']['ID'] = $clubID;
$arResult["NAV_STRING"] = $resStockList->GetPageNavStringEx($navComponentObject, "", "modern");



$this->IncludeComponentTemplate();













