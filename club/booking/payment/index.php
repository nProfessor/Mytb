<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

    $APPLICATION->IncludeComponent("mytb:payment", "", array(
            "CLUB_ID" => $_GET['ID'],
            "TABLE_ID" => $_GET['ID'],
            "BOOKING"=>"Y"
        ),
        false
    );

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
