<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");


if(empty($_GET['URL'])){
    printAr("SDAfrdsfa");
    $Stocks = new Stocks();
    $arFields=$Stocks->getInfo(intval($_GET['ID']));

    $url=formUrl($arFields['ID'],$arFields['NAME']);

    header('HTTP/1.1 301 Moved Permanently');
    header("Location: /club/stock/{$url}");
    die();

}

$APPLICATION->IncludeComponent("mytb:club.stock",
    "",
    array(
         "STOCK_ID"=> intval($_GET["ID"])
    ), false);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
