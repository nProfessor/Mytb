<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (intval($_GET['ID']) != 0):
    if(empty($_GET['URL'])){
        $club = new Club(intval($_GET['ID']));
        $arFields=$club->getInfo(array("arSelect"=> array(
            "ID",
            "NAME",
            "PROPERTY_TYPE_FACILITY"
        )),true);


        $url=formUrl($arFields['ID'],implode("-",$arFields['PROPERTY_TYPE_FACILITY_VALUE'])." ".$arFields['NAME']);

        header('HTTP/1.1 301 Moved Permanently');
        header("Location: /club/{$url}/");
        die();

    }


    $APPLICATION->IncludeComponent("mytb:club", "detail", array(
            "ID" => $_GET['ID'],
            "AUTH"=>$USER->IsAuthorized(),
            "CACHE_TIME"=>600
        ),
        false
    ); else:
    $APPLICATION->SetTitle("Список клубов, боров ресторанов");
    $APPLICATION->IncludeComponent("mytb:club.list", "home", array(
            "CACHE_TIME"=>600,
        ),
        false
    );
endif;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
