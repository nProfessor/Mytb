<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");
die();

$res=Club::getList(array(),array(),false,false,array("ID","PROPERTY_ADDRESS","PROPERTY_PHONE"));
$i=0;
while($row=$res->Fetch()){
    foreach($row["PROPERTY_ADDRESS_VALUE"] as $var){


                $obj=json_decode(file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode(trim($var))."&format=json"));
                list($LAT,$LON)=explode(" ",$obj->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
        MyTbCore::Add(array(
            "CLUB_ID"=>$row["ID"],
            "SITY_ID"=>1,
            "LON"=>$LON,
            "LAT"=>$LAT,
            "ADDRESS"=>$var,
            "PHONE"=>serialize((array)$row["PROPERTY_PHONE_VALUE"])
        ),"address");
    }
    $i++;
}

echo $i;

?>