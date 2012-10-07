<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (intval($_GET['ID']) != 0):
    $APPLICATION->IncludeComponent("mytb:club", "booking", array(
            "ID" => $_GET['ID'],
            "BOOKING"=>"Y"
        ),
        false
    ); else:
    $APPLICATION->IncludeComponent("mytb:club.list", "home", array(),
        false
    );
endif;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
