<?php
/**
 * User:        Олег
 * Data:        14.06.12 22:59
 * Site: http://sForge.ru
 **/

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

if (isset($_GET["dateFrom"])) {
    $dateFrom = "01." . $_GET["dateFrom"];

} else {
    $dateFrom = "01." . date("m.Y");
}

$club = Club::getOBonTheUserID($USER::GetId());

$dateTo = strtotime("+3 month", strtotime($dateFrom));


$res = $club->getResBookingList(array(
    "arSelect" => Array("ID", "ACTIVE_FROM"),
    "arFilter" => Array(
        "ACTIVE" => "Y",
        "<=DATE_ACTIVE_FROM" => date("d-m-Y", $dateTo),
        ">=DATE_ACTIVE_FROM" => date("d-m-Y", strtotime($dateFrom))),
));


while ($ob = $res->GetNext()) {
    $d = date("dn", strtotime($ob["ACTIVE_FROM"]));
    $arFields[$d] = $d;

}

echo json_encode($arFields);

