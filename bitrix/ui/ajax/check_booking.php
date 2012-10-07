<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
global $USER;
$userID = $USER->GetID();
$userInfo =CUser::GetByID($userID)->Fetch();

$tableID  = (int)$_POST["table_id"];
$clubID   = (int)$_POST["club_id"];
$date     = $_POST["date"];

$comments = trim(strip_tags($_POST["comments"]));




$phone = $_POST["phone"];
$fio   = $_POST["fio"];
$time  = $_POST["time"];


switch ($_POST["type"]) {
    case "club":
        $type = BOOKING_TYPE_CLUB;
        break;
    default:
        $type = BOOKING_TYPE_SITE;
        break;
}

// укажем формат этой даты
$format = "DD.MM.YYYY";

// получим формат текущего сайта
$new_format = CSite::GetDateFormat("SHORT"); // YYYY-MM-DD

// переведем дату из одного формата в другой
$new_date = $DB->FormatDate($date, $format, $new_format);

$el = new CIBlockElement;

$PROP = array(
    "USER"  => $userID,
    "TABLE" => $tableID,
    "CLUB"  => $clubID,
    "TYPE"  => $type,
    "PHONE" => $phone,
    "FIO"   => $fio,
    "TIME"  => $time
);


$arLoadProductArray = Array(
    "MODIFIED_BY"       => $userID, // элемент изменен текущим пользователем
    "DATE_ACTIVE_FROM"  => $new_date, //
    "IBLOCK_SECTION_ID" => FALSE, // элемент лежит в корне раздела
    "IBLOCK_ID"         => IB_BOOKING_ID,
    "DETAIL_TEXT"       => $comments,
    "PROPERTY_VALUES"   => $PROP,
    "NAME"              => "Бронь столика {$clubID} клуба {$tableID}", //TODO нужно номер столика сюда забабахать
    "ACTIVE"            => "Y", // активен
);


if ($BOOKING_ID = $el->Add($arLoadProductArray)) {
    $APPLICATION->IncludeComponent("mytb:payment", "", array("BOOKING_ID" => $BOOKING_ID), FALSE);
}

?>