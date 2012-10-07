<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (intval($_GET['ID']) != 0):
    $APPLICATION->IncludeComponent("mytb:club", "detail", array(
            "ID" => $_GET['ID']
        ),
        false
    ); else:
    $APPLICATION->SetTitle("Список клубов, боров ресторанов");
    $APPLICATION->IncludeComponent("mytb:club.list", "home", array(),
        false
    );
endif;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
