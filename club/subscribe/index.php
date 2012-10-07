<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
if ($USER->IsAuthorized()) {
    $APPLICATION->IncludeComponent("mytb:club.subscribe", "", array(
                                                               "ID" => $_GET['ID']
                                                          ),
        FALSE
    );
} else {

}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
