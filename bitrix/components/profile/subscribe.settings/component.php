<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE)
    die();

CModule::IncludeModule("iblock");

global $USER;
$user    = new User($USER::GetID());
$clubAll = array();


$clubSave = array();
if (isset($_POST['save'])) {
    $clubSave["LINK_STOK"]  = array();
    $clubSave["LINK_EVENT"] = array();

    if (count($_POST['stok']))
        foreach ($_POST['stok'] as $var) {
            $var                     = intval($var);
            $clubSave["LINK_STOK"][] = $var;
        }

    if (count($_POST['event']))
        foreach ($_POST['event'] as $var) {
            $var                      = intval($var);
            $clubSave["LINK_EVENT"][] = $var;
        }



    if(isset($_POST['metod'])){
        foreach($_POST['metod'] as $var){
            if($var=="sms"){
                $clubSave['metod'][]="sms";
            }elseif($var=="email"){
                $clubSave['metod'][]="email";
            }
        }
    }

    if(isset($_POST['day'])){
        foreach($_POST['day'] as $var){
            $var=intval($var);
            if($var>0&$var<8){
                $clubSave['day'][]=$var;
            }
        }
    }

    $user->setSubscribeData($clubSave);;


}

$rs = $user->getProps(array("ID", "PROPERTY_USER", "PROPERTY_LINK_STOK", "PROPERTY_LINK_EVENT","PROPERTY_NOTICE"));


$arListClubSubsEVENT = $rs['PROPERTY_LINK_EVENT_VALUE'];
$arListClubSubsSTOK  = $rs['PROPERTY_LINK_STOK_VALUE'];
$NOTICE  = unserialize($rs['PROPERTY_NOTICE_VALUE']);




$clubAll = array_merge((array)$arListClubSubsEVENT, (array)$arListClubSubsSTOK);

if (count($clubAll) > 0) {
    $rsClub = Club::getList(array(), array("ID"=> $clubAll, "ACTIVE"=> "Y"), FALSE, FALSE, array("NAME", "ID",
                                                                                                 "PREVIEW_PICTURE"));

    while ($arClub = $rsClub->Fetch()) {
        $arFile                    = CFile::GetFileArray($arClub["PREVIEW_PICTURE"]);
        $arClubList[$arClub['ID']] = array("NAME"=> $arClub['NAME'], "ID"=> $arClub['ID'], "SRC"=> $arFile["SRC"]);
    }
}

$arResult['club']          = $arClubList;
$arResult['SUBS']["EVENT"] = $arListClubSubsEVENT;
$arResult['SUBS']["STOK"]  = $arListClubSubsSTOK;
$arResult['NOTICE']  = $NOTICE;


$this->IncludeComponentTemplate();




















