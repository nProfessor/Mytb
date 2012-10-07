<?php
/**
 * User:        Олег
 * Data:        11.06.12 22:56
 * Site: http://sForge.ru
 **/
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$tableID = (int)$_POST["tableID"];
$name = $_POST["name"];
$count = (int)$_POST["count"];
$price_group = (int)$_POST["price_group"];


if (trim($name) == "" || $count == 0 || $price_group == 0) {
    die(json_encode(array("errors" => "ok")));
}


global $USER;
$userID = $USER->GetID();

$Club = Club::getOBonTheUserID($USER->GetID());
$table = $Club->table()->getInfo($tableID,array("arSelect"=>array("PROPERTY_PRICE_GROUP")));




$el = new CIBlockElement;

$arLoadProductArray = Array(
    "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
    "NAME" => trim($name), // элемент изменен текущим пользователем
);

if (intval($table["ID"]) > 0) {
    $res = $el->Update(intval($table["ID"]), $arLoadProductArray);
    CIBlockElement::SetPropertyValuesEx(intval($table["ID"]), IB_TABLE_ID, array("COUNT" => $count, "PRICE_GROUP" => $price_group));
} else {
    die(json_encode(array("errors" => "ok")));

}
if ($res) {
    //TODO нужно наверно написать отдельную функцию для получения ценовой группы
    $table = $Club->table()->getInfo($tableID,array("arSelect"=>array("PROPERTY_PRICE_GROUP")));
    die(json_encode(array("message" => "ok", "table" => array(
        "name" => trim($name),
        "count" => $count,
        "price_group" => $table['PROPERTY_PRICE_GROUP']["PROPERTY_PRICE_VALUE"]
    ))));
}

die(json_encode(array("errors" => "ok")));

?>