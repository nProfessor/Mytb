<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("mytb");

define('AJAX_QUERY',strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_SERVER['HTTP_X_REQUESTED_WITH']));

if (!AJAX_QUERY){
    die(json_decode(array("status"=>"errors")));
}
$arFilter = array(
    "ACTIVE" => "Y"
);
$arFilter["ID"] = Club::getListHaveStocks();



$res = Club::getList(Array("SORT" => "DESC"), $arFilter, FALSE, $arNavStartParams, Array(
    "NAME",
    "ID",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    'PROPERTY_RATING',
    'PROPERTY_METRO',
    'PROPERTY_TIME_WORKING',
    'PROPERTY_PRICE_COCKTAIL',
    'PROPERTY_CARDS',
    'PROPERTY_TYPE_FACILITY'
));

$clubsID=array();
while ($arField = $res->Fetch()) {
    $arFile = CFile::GetFileArray($arField["PREVIEW_PICTURE"]);

    $result[] = array(
        "ID" => $arField["ID"],
        "NAME" => $arField["NAME"],
        "PREVIEW_TEXT" => $arField["~PREVIEW_TEXT"],
        "PREVIEW_PICTURE" => imgurl($arFile["SRC"], array("w" => 100)),
        "PROPERTY_METRO_VALUE" => $arField["PROPERTY_METRO_VALUE"],
        "PROPERTY_RATING_VALUE" => $arField["PROPERTY_RATING_VALUE"],
        "PROPERTY_TIME_WORKING_VALUE" => str_replace(";", "<br/>", $arField["PROPERTY_TIME_WORKING_VALUE"]),
        "PROPERTY_PRICE_COCKTAIL_VALUE" => $arField["PROPERTY_PRICE_COCKTAIL_VALUE"],
        "PROPERTY_CARDS_VALUE" => $arField["PROPERTY_CARDS_VALUE"],
        "PROPERTY_TYPE_FACILITY" => implode("/",$arField["PROPERTY_TYPE_FACILITY_VALUE"]),
        "PROPERTY_TYPE_FACILITY_VALUE" => array_keys($arField["PROPERTY_TYPE_FACILITY_VALUE"]),

    );


    $clubsID[]=intval($arField["ID"]);
}

$resAdress=Club::getAddressAll($clubsID);

$adress=array();
foreach ($resAdress as $var) {
    $var["PHONE"]=implode("<br/>",$var["PHONE"]);
    $adress[$var['CLUB_ID']]=$var;
}



die(json_encode(array("status"=>"ok","club"=>$result,"address"=>$adress)));
?>
