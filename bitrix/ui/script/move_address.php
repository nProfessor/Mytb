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

while($row=$res->Fetch()){
    foreach($row["PROPERTY_ADDRESS_VALUE"] as $var){
        MyTbCore::Add(array(
            "CLUB_ID"=>$row["ID"],
            "SITY_ID"=>1,
            "ADDRESS"=>$var,
            "PHONE"=>serialize((array)$row["PROPERTY_PHONE_VALUE"])
        ),"address");
    }
}

?>