<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (intval($_GET['ID']) != 0):
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
