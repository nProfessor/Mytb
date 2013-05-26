<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");


$APPLICATION->IncludeComponent("mytb:club.list.stock",
    "uone",
    array(
         "CLUB_ID"=> intval($_GET["ID"])
    ), false);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
