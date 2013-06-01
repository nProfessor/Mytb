<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Все заведения с акциями и скидками на карте.");
?>

<p><?$APPLICATION->IncludeComponent("mytb:map.stocks", ".default", array(
    ),
    false
);?></p>