<?php
/**
 *
 * User: Tabota Oleg (sForge.ru)
 * Date: 20.12.12 18:54
 * File name: getinfo.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");


$club_id = intval($_POST["club_id"]);
$clubInfo['NAME']=trim($_POST["NAME"]);
$clubInfo['DESCR']=trim($_POST["DESCR"]);
$clubInfo['SITE']=trim(str_replace("http://","",$_POST["SITE"]));
$clubInfo['EMAIL_MANAGER']=trim($_POST["EMAIL_MANAGER"]);
$clubInfo['TIME_WORKING']=trim($_POST["TIME_WORKING"]);
$clubInfo['AVERAGE_CHECK']=trim($_POST["AVERAGE_CHECK"]);
$clubInfo['KIND_CLUB']=(array)$_POST["KIND_CLUB"];
$clubInfo['MUSIC']=(array)$_POST["MUSIC"];


$clubInfo['ADDRES']=(array)$_POST["ADDRES"];
$clubInfo['ADDRES_NEW']=(array)$_POST["ADDRES_NEW"];




foreach($clubInfo['ADDRES'] as $address){
    $obj=json_decode(file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode(trim($address["addres"]))."&format=json"));

    list($LAT,$LON)=explode(" ",$obj->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

    $res=new MyTbCore;
    $res->update(intval($address["id"]),array(
        "SITY_ID"=>intval($address["sity"]),
        "ADDRESS"=>trim($address["addres"]),
        "LON"=>$LON,
        "LAT"=>$LAT,
        "PHONE"=>serialize(explode("\n",$address["phone"]))
    ),"address");
}


foreach($clubInfo['ADDRES_NEW'] as $address){
    $obj=json_decode(file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode(trim($address["addres"]))."&format=json"));

    list($LAT,$LON)=explode(" ",$obj->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

    $res=new MyTbCore;
    $res->Add(array(
        "CLUB_ID"=>$club_id,
        "SITY_ID"=>intval($address["sity"]),
        "ADDRESS"=>trim($address["addres"]),
        "LON"=>$LON,
        "LAT"=>$LAT,
        "PHONE"=>serialize(explode("\n",$address["phone"]))
    ),"address");
}

$clubRes = new Club($club_id);

$clubRes->update(array(
    "NAME" => $clubInfo['NAME'],
    "SITE" => $clubInfo['SITE'],
    "EMAIL_MANAGER" => $clubInfo['EMAIL_MANAGER'],
    "AVERAGE_CHECK" => $clubInfo['AVERAGE_CHECK'],
    "TIME_WORKING" => $clubInfo['TIME_WORKING'],
    "KIND_CLUB" => $clubInfo['KIND_CLUB'],
    "MUSIC" => $clubInfo['MUSIC'],
    "DESCR" => $clubInfo['DESCR']
));

die(json_encode(array("status" => "ok", "result" => array(
    "ID"=>$clubInfo['ID'],
    "NAME" => $clubInfo['NAME'],
    "PREVIEW_PICTURE" => $clubInfo['PREVIEW_PICTURE'],
    "SITE" => $clubInfo['PROPERTY_SITE_VALUE'],
    "EMAIL_MANAGER" => $clubInfo['PROPERTY_EMAIL_MANAGER_VALUE'],
    "AVERAGE_CHECK" => $clubInfo['PROPERTY_AVERAGE_CHECK_VALUE'],
    "TIME_WORKING" => $clubInfo['PROPERTY_TIME_WORKING_VALUE'],
    "KIND_CLUB" => (array)$clubInfo['PROPERTY_KIND_CLUB_VALUE'],
    "MUSIC" => (array)$clubInfo['PROPERTY_MUSIC_VALUE'],
    "DESCR" => $clubInfo['PROPERTY_DESCR_VALUE'],
    "ADDRESS" => $clubRes->getAddress(),
))));