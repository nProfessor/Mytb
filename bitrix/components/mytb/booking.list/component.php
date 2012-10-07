<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");

global $USER;
$arUserlist = array();

if (!isset($_GET["date"])) {
    $date = date('d.m.Y');
} else {
    $date = $_GET["date"];
}

$club = Club::getOBonTheUserID($USER::GetId());


$res = $club->getResBookingList(array(
    "arSelect" => Array(
        "ID",
        "ACTIVE_FROM",
        "PROPERTY_CLUB",
        "PROPERTY_TABLE",
        "PROPERTY_USER",
        'PROPERTY_PAID',
        'PROPERTY_TYPE',
        'PROPERTY_FIO',
        'PROPERTY_TIME',
        'PROPERTY_PHONE',
    ),
    "arFilter" => Array(
        "DATE_ACTIVE_FROM" => $date,
        "ACTIVE" => "Y",
    )
));


while ($ob = $res->Fetch()) {
    $i++;
    $arFields[$ob["ID"]] = array(
        "ID" => $ob["ID"],
        "ACTIVE_FROM" => $ob["ACTIVE_FROM"],
        "PROPERTY_CLUB_VALUE" => $ob["PROPERTY_CLUB_VALUE"],
        "PROPERTY_TABLE_VALUE" => $ob["PROPERTY_TABLE_VALUE"],
        "PROPERTY_USER_VALUE" => $ob["PROPERTY_USER_VALUE"],
        "PROPERTY_PAID_VALUE" => $ob["PROPERTY_PAID_VALUE"],
        "PROPERTY_TYPE_VALUE" => $ob["PROPERTY_TYPE_VALUE"],
        "PROPERTY_FIO_VALUE" => $ob["PROPERTY_FIO_VALUE"],
        "PROPERTY_TIME_VALUE" => $ob["PROPERTY_TIME_VALUE"],
        "PROPERTY_PHONE_VALUE" => $ob["PROPERTY_PHONE_VALUE"],
    );

    $arUserlistID[$ob["PROPERTY_USER_VALUE"]] = $ob["PROPERTY_USER_VALUE"];
    $arTablelistID[$ob["PROPERTY_TABLE_VALUE"]] = $ob["PROPERTY_TABLE_VALUE"];
}


/* Достаем пользователей */
if (count($arUserlistID) && is_array($arUserlistID)) {
    $filter = Array
    (
        "ID" => implode(" | ", $arUserlistID)
    );
    $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter); // выбираем пользователей

    while ($rsUser = $rsUsers->Fetch()) :
        $arUsersList[$rsUser["ID"]] = $rsUser;
    endwhile;

}

if (count($arTablelistID)) {


    $res = $club->getResTableList(array(
        "arOrder" => array("ID" => "DESC"),
        "arSelect" => Array(
            "ID",
            "NAME",
            "PROPERTY_COUNT"
        ),
        "arFilter" => Array(
            "ID" => $arTablelistID,
            "ACTIVE" => "Y"
        )
    ));

    while ($ob = $res->Fetch()) {

        $arFieldsTable[$ob["ID"]] = array(
            "ID" => $ob["ID"],
            "NAME" => $ob["NAME"],
            "PROPERTY_COUNT_VALUE" => $ob["PROPERTY_COUNT_VALUE"],
        );
    }
}

$arResult["BookingList"] = $arFields;
$arResult["UsersList"] = $arUsersList;
$arResult["TableList"] = $arFieldsTable;
$arResult["date"] = $date;

$this->IncludeComponentTemplate();