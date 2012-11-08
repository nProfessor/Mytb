<?php
/**
 * Created by JetBrains PhpStorm.
 * User: professor
 * Date: 28.10.12
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

function send()
{

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");

    $ob = CIBlockElement::GetList(
        array("SORT" => "ASC"),
        array(
            "!PROPERTY_EMAIL_MANAGER"=>fale,
            "IBLOCK_ID"               => IB_CLUB_ID),
        FALSE,
        FALSE,
        array(
            "ID",
            "NAME",
            "PROPERTY_EMAIL_MANAGER",
        ));

    while($row=$ob->Fetch()){
        if(trim($row['PROPERTY_EMAIL_MANAGER_VALUE']!="")){
            $arEventFields = array("EMAIL"=>$row['PROPERTY_EMAIL_MANAGER_VALUE']);
            CEvent::Send("RASSYLKA", "s1", $arEventFields);
        }
    }

    return true;
}