<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->IncludeComponent("mytb:club.event",
    "",
    array(
        "EVENT_ID"=> intval($_GET["ID"])
    ), false);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

